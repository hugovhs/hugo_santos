<?php

namespace App\Services;

use App\Models\Producto;

/**
 * Carrito de compras persistido en la sesión de Laravel para las pruebas
 * 
 */
final class CarritoService
{
    public const SESSION_KEY = 'carrito';

    public static function obtener(): array
    {
        $crudo = session()->get(self::SESSION_KEY, []);

        if (! is_array($crudo)) {
            return [];
        }

        $carrito = [];

        foreach ($crudo as $id => $cantidad) {
            $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $cantidad = filter_var($cantidad, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

            if ($id === false || $cantidad === false) {
                continue;
            }

            $carrito[$id] = $cantidad;
        }

        return $carrito;
    }

    public static function totalUnidades(): int
    {
        return array_sum(self::obtener());
    }

    public static function cantidadDe(int $productoId): int
    {
        return self::obtener()[$productoId] ?? 0;
    }

    public static function agregar(Producto $producto, int $cantidad = 1): int
    {
        if (! $producto->activo || (int) $producto->stock <= 0 || $cantidad < 1) {
            return self::totalUnidades();
        }

        $carrito = self::obtener();
        $id = (int) $producto->id;

        $carrito[$id] = max(1, min(
            ($carrito[$id] ?? 0) + $cantidad,
            (int) $producto->stock,
        ));

        session()->put(self::SESSION_KEY, $carrito);

        return array_sum($carrito);
    }

    public static function actualizar(int $productoId, int $cantidad): int
    {
        $carrito = self::obtener();

        if ($cantidad <= 0) {
            unset($carrito[$productoId]);
            session()->put(self::SESSION_KEY, $carrito);

            return array_sum($carrito);
        }

        $producto = Producto::query()->find($productoId);

        if ($producto === null || ! $producto->activo || (int) $producto->stock <= 0) {
            unset($carrito[$productoId]);
            session()->put(self::SESSION_KEY, $carrito);

            return array_sum($carrito);
        }

        $carrito[$productoId] = max(1, min($cantidad, (int) $producto->stock));
        session()->put(self::SESSION_KEY, $carrito);

        return array_sum($carrito);
    }

    public static function quitar(int $productoId): int
    {
        $carrito = self::obtener();
        unset($carrito[$productoId]);
        session()->put(self::SESSION_KEY, $carrito);

        return array_sum($carrito);
    }

    public static function limpiar(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
