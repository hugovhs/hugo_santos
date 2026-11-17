<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class ExchangeService
{
    protected const RATE_KEY = 'exchange_rate:usd_mxn';
    protected const UPDATED_AT_KEY = 'exchange_rate:usd_mxn:updated_at';
    protected const MAX_AGE_SECONDS = 21600; // 6 hours

    protected $apiUrl;

    public function __construct()
    {
        // Obtenemos la URL de la API desde el .env
        $this->apiUrl = env('EXCHANGE_RATE_API');
    }

    public function getExchangeRate()
    {
        // Primero obtenemos el valor cacheado de redis, para evitar hacer llamadas a la API repetidas
        $cachedRate = Redis::get(self::RATE_KEY); // obtenemos el último valor guardado
        $updatedAt = Redis::get(self::UPDATED_AT_KEY); // obtenemos la fecha de la última actualización

        // Si tenemos un valor cacheado y es menor a 6 horas lo retornamos
        if ($this->hasValue($cachedRate) && $this->isFresh($updatedAt)) {
            return (float) $cachedRate;
        }

        /**
         * API Response
         * {"date":"2026-11-17","base":"USD","quote":"MXN","rate":18.5414}
         * rate is the exchange rate
         */
        $response = Http::get($this->apiUrl);

        if ($response->successful()) {
            $rate = $response->json('rate');

            if ($this->hasValue($rate)) {
                // Guardamos el valor en redis y la fecha de la última actualización
                Redis::set(self::RATE_KEY, $rate);
                Redis::set(self::UPDATED_AT_KEY, time());

                return (float) $rate;
            }
        }

        return $this->hasValue($cachedRate) ? (float) $cachedRate : null;
    }

    protected function hasValue($value): bool
    {
        return $value !== null && $value !== '' && is_numeric($value);
    }

    protected function isFresh($updatedAt): bool
    {
        return $updatedAt !== null
            && (time() - (int) $updatedAt) < self::MAX_AGE_SECONDS;
    }
}