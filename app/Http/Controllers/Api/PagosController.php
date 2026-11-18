<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PagosController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'numero' => ['required', 'string', 'regex:/^\d{13,19}$/'],
            'fecha_vencimiento' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'cvv' => ['required', 'string', 'regex:/^\d{3,4}$/'],
            'nombre' => ['required', 'string', 'min:3', 'max:100'],
        ], [
            'fecha_vencimiento.regex' => 'La fecha de vencimiento debe tener el formato mm/yy.',
        ]);

        // Simula la latencia de una pasarela de pagos real.
        sleep(3);

        // 70% de probabilidad de éxito para hacerlo más interesante.
        $success = fake()->boolean(70);

        if (! $success) {
            return response()->json([
                'status' => 'fail',
                'message' => 'El pago fue rechazado por la entidad emisora.',
                'transaction_id' => (string) Str::uuid(),
                'last_four' => substr($validated['numero'], -4),
            ], 402);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pago procesado correctamente.',
            'transaction_id' => (string) Str::uuid(),
            'last_four' => substr($validated['numero'], -4),
        ]);
    }
}
