<?php

namespace App\Livewire\Home;

use App\Models\Cliente;
use App\Models\Direccion;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\CarritoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Checkout extends Component
{
    private const IVA_TASA = 0.16;

    // Paso 1: correo
    public string $correo = '';
    public ?Cliente $cliente = null;
    public bool $correoConfirmado = false;

    // Paso 2: direcciones
    /** @var \Illuminate\Database\Eloquent\Collection<int, Direccion> */
    public $direcciones = [];
    public ?int $direccionSeleccionadaId = null;
    public bool $mostrarFormularioDireccion = false;

    public string $direccion = '';
    public string $colonia = '';
    public string $ciudad = '';
    public string $estado = '';
    public string $codigo_postal = '';
    public string $referencia = '';
    public string $telefono = '';
    public bool $guardarDireccion = true;

    // Paso 3: tarjeta
    public string $tarjeta_numero = '';
    public string $tarjeta_vencimiento = '';
    public string $tarjeta_cvv = '';
    public string $tarjeta_nombre = '';

    public ?string $pagoError = null;
    public ?string $stockError = null;
    public bool $procesando = false;

    public function mount(): void
    {
        if (CarritoService::totalUnidades() === 0) {
            $this->redirectRoute('home.carrito', navigate: true);
        }
    }

    public function continuarConCorreo(): void
    {
        $this->validate([
            'correo' => ['required', 'email', 'max:255'],
        ], [
            'correo.required' => 'Escribe tu correo electrónico.',
            'correo.email' => 'Escribe un correo electrónico válido.',
        ]);

        $correoNormalizado = strtolower(trim($this->correo));

        $cliente = Cliente::query()->where('correo', $correoNormalizado)->first();

        if ($cliente === null) {
            $cliente = Cliente::query()->create([
                'correo' => $correoNormalizado,
                'tipo' => 2, // invitado
            ]);
        }

        $this->cliente = $cliente;
        $this->correo = $correoNormalizado;
        $this->correoConfirmado = true;
        $this->pagoError = null;

        $this->cargarDirecciones();
    }

    public function editarCorreo(): void
    {
        $this->correoConfirmado = false;
        $this->cliente = null;
        $this->direcciones = [];
        $this->direccionSeleccionadaId = null;
        $this->mostrarFormularioDireccion = false;
        $this->pagoError = null;
    }

    protected function cargarDirecciones(): void
    {
        if ($this->cliente === null) {
            return;
        }

        $this->direcciones = Direccion::query()
            ->where('cliente_id', $this->cliente->id)
            ->latest()
            ->get();

        if ($this->direcciones->isEmpty()) {
            $this->mostrarFormularioDireccion = true;
            $this->direccionSeleccionadaId = null;
        } else {
            $this->mostrarFormularioDireccion = false;
            // No preseleccionamos: el usuario elige con "Enviar aquí".
            $this->direccionSeleccionadaId = null;
        }
    }

    public function elegirDireccion(int $direccionId): void
    {
        $existe = collect($this->direcciones)->firstWhere('id', $direccionId);

        if ($existe === null) {
            return;
        }

        $this->direccionSeleccionadaId = $direccionId;
        $this->mostrarFormularioDireccion = false;
        $this->pagoError = null;
    }

    public function mostrarFormularioNuevaDireccion(): void
    {
        $this->mostrarFormularioDireccion = true;
        $this->direccionSeleccionadaId = null;
    }

    public function cancelarNuevaDireccion(): void
    {
        // Solo se puede cancelar si ya hay direcciones guardadas.
        if (collect($this->direcciones)->isNotEmpty()) {
            $this->mostrarFormularioDireccion = false;
            $this->reset(['direccion', 'colonia', 'ciudad', 'estado', 'codigo_postal', 'referencia', 'telefono']);
        }
    }

    protected function direccionNuevaValida(): bool
    {
        return trim($this->direccion) !== ''
            && trim($this->colonia) !== ''
            && trim($this->ciudad) !== ''
            && trim($this->estado) !== ''
            && trim($this->codigo_postal) !== ''
            && trim($this->telefono) !== '';
    }

    public function getDireccionListaProperty(): bool
    {
        if (! $this->correoConfirmado) {
            return false;
        }

        if ($this->direccionSeleccionadaId !== null) {
            return true;
        }

        return $this->mostrarFormularioDireccion && $this->direccionNuevaValida();
    }

    public function getTarjetaListaProperty(): bool
    {
        return trim($this->tarjeta_numero) !== ''
            && trim($this->tarjeta_vencimiento) !== ''
            && trim($this->tarjeta_cvv) !== ''
            && trim($this->tarjeta_nombre) !== '';
    }

    public function getPuedeProcesarProperty(): bool
    {
        return $this->correoConfirmado
            && $this->direccionLista
            && $this->tarjetaLista
            && ! $this->procesando
            && CarritoService::totalUnidades() > 0;
    }

    public function procesarPedido(): void
    {
        if ($this->procesando) {
            return;
        }

        $this->pagoError = null;
        $this->stockError = null;

        $this->validate([
            'correo' => ['required', 'email'],
            'tarjeta_numero' => ['required', 'string', 'regex:/^\d{13,19}$/'],
            'tarjeta_vencimiento' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'tarjeta_cvv' => ['required', 'string', 'regex:/^\d{3,4}$/'],
            'tarjeta_nombre' => ['required', 'string', 'min:3', 'max:100'],
        ], [
            'tarjeta_numero.regex' => 'El número de tarjeta debe tener entre 13 y 19 dígitos.',
            'tarjeta_vencimiento.regex' => 'La fecha de vencimiento debe tener el formato mm/yy.',
            'tarjeta_cvv.regex' => 'El CVV debe tener 3 o 4 dígitos.',
            'tarjeta_nombre.required' => 'Escribe el nombre que aparece en la tarjeta.',
        ]);

        // Validar dirección: o hay una seleccionada o el formulario nuevo está completo.
        if ($this->direccionSeleccionadaId === null) {
            $this->validate([
                'direccion' => ['required', 'string', 'max:255'],
                'colonia' => ['required', 'string', 'max:255'],
                'ciudad' => ['required', 'string', 'max:255'],
                'estado' => ['required', 'string', 'max:255'],
                'codigo_postal' => ['required', 'string', 'max:20'],
                'telefono' => ['required', 'string', 'max:30'],
                'referencia' => ['nullable', 'string', 'max:255'],
            ], [
                'direccion.required' => 'Escribe tu calle y número.',
                'colonia.required' => 'Escribe tu colonia.',
                'ciudad.required' => 'Escribe tu ciudad.',
                'estado.required' => 'Escribe tu estado.',
                'codigo_postal.required' => 'Escribe tu código postal.',
                'telefono.required' => 'Escribe un teléfono de contacto.',
            ]);
        }

        $carrito = CarritoService::obtener();

        if ($carrito === []) {
            $this->redirectRoute('home.carrito', navigate: true);

            return;
        }

        // Verificar stock antes de cobrar
        $productosStock = Producto::query()
            ->whereKey(array_keys($carrito))
            ->get(['id', 'nombre', 'stock', 'activo']);

        if ($productosStock->count() !== count($carrito)) {
            $this->stockError = 'Algunos productos de tu carrito ya no están disponibles. Revísalo e intenta de nuevo.';

            return;
        }

        $faltantes = [];
        foreach ($productosStock as $producto) {
            $cantidad = $carrito[$producto->id] ?? 0;

            if (! $producto->activo || (int) $producto->stock < $cantidad) {
                $faltantes[] = $producto->nombre;
            }
        }

        if ($faltantes !== []) {
            $this->stockError = 'No hay stock suficiente para: '.implode(', ', $faltantes).'. Ajusta tu carrito e intenta de nuevo.';

            return;
        }

        $this->procesando = true;

        try {
            // 1. Cobrar tarjeta vía API de pagos (transacciones ficticias).
            $numeroLimpio = preg_replace('/\D/', '', $this->tarjeta_numero) ?? '';

            $respuesta = Http::timeout(15)->post(url('/api/pagos'), [
                'numero' => $numeroLimpio,
                'fecha_vencimiento' => trim($this->tarjeta_vencimiento),
                'cvv' => trim($this->tarjeta_cvv),
                'nombre' => trim($this->tarjeta_nombre),
            ]);

            $datos = $respuesta->json();

            if (! $respuesta->successful() || ($datos['status'] ?? null) !== 'success') {
                $this->pagoError = $datos['message'] ?? 'El pago no pudo ser procesado. Intenta con otra tarjeta.';
                $this->procesando = false;

                return;
            }

            // 2. Pago exitoso: generar la orden.
            $pedido = DB::transaction(function () use ($carrito) {
                $cliente = $this->cliente
                    ?? Cliente::query()->where('correo', strtolower(trim($this->correo)))->firstOrFail();

                // Resolver dirección: seleccionada o nueva del formulario.
                if ($this->direccionSeleccionadaId !== null) {
                    Direccion::query()
                        ->where('cliente_id', $cliente->id)
                        ->whereKey($this->direccionSeleccionadaId)
                        ->firstOrFail();
                } elseif ($this->guardarDireccion) {
                    Direccion::query()->create([
                        'cliente_id' => $cliente->id,
                        'direccion' => trim($this->direccion),
                        'colonia' => trim($this->colonia),
                        'ciudad' => trim($this->ciudad),
                        'estado' => trim($this->estado),
                        'codigo_postal' => trim($this->codigo_postal),
                        'referencia' => trim($this->referencia) !== '' ? trim($this->referencia) : null,
                        'telefono' => trim($this->telefono),
                    ]);
                }
                // Si no se marcó "Guardar", la dirección del formulario solo se usa
                // para este pedido (la tabla pedidos no guarda dirección).

                $productos = Producto::query()
                    ->whereKey(array_keys($carrito))
                    ->lockForUpdate()
                    ->get(['id', 'nombre', 'precio_usd', 'precio_mxn', 'stock', 'activo']);

                // Limita cada cantidad al stock disponible para que el inventario nunca quede en negativo.
                $lineas = [];
                $subtotal = 0;
                foreach ($productos as $producto) {
                    $solicitada = $carrito[$producto->id] ?? 0;
                    $cantidad = min($solicitada, max(0, (int) $producto->stock));

                    if ($cantidad <= 0) {
                        continue;
                    }

                    $lineas[] = ['producto' => $producto, 'cantidad' => $cantidad];
                    $subtotal += round((float) $producto->precio_mxn * $cantidad, 2);
                }

                if ($lineas === []) {
                    throw new \RuntimeException('Sin stock disponible para los productos del carrito.');
                }
                $subtotal = round($subtotal, 2);
                $impuestos = round($subtotal * self::IVA_TASA, 2);
                $total = round($subtotal + $impuestos, 2);

                $folio = $this->generarFolio();

                $pedido = Pedido::query()->create([
                    'folio' => $folio,
                    'correo' => $cliente->correo,
                    'subtotal' => $subtotal,
                    'impuestos' => $impuestos,
                    'total' => $total,
                    'estatus' => 'creado',
                    'fecha' => now(),
                ]);

                foreach ($lineas as $linea) {
                    $producto = $linea['producto'];
                    $cantidad = $linea['cantidad'];

                    // Descuenta el inventario (la cantidad ya está limitada al stock, nunca queda en negativo).
                    $producto->decrement('stock', $cantidad);

                    $pedido->detalles()->create([
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'precio_usd' => $producto->precio_usd,
                        'precio_mxn' => $producto->precio_mxn,
                    ]);
                }

                return $pedido;
            });

            CarritoService::limpiar();
            $this->dispatch('carrito-actualizado', total: 0);

            $this->redirectRoute('home.checkout.success', ['folio' => $pedido->folio], navigate: true);
        } catch (\RuntimeException $e) {
            // El stock se agotó mientras se procesaba el pago: no se generó orden.
            $this->stockError = 'El stock cambió mientras se procesaba tu pago. Revisa tu carrito e intenta de nuevo.';
        } finally {
            $this->procesando = false;
        }
    }

    protected function generarFolio(): string
    {
        do {
            $folio = 'NV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Pedido::query()->where('folio', $folio)->exists());

        return $folio;
    }

    #[Layout('components.layouts.home')]
    #[Title('Checkout')]
    public function render()
    {
        $carrito = CarritoService::obtener();

        $productos = $carrito === []
            ? collect()
            : Producto::query()
                ->whereKey(array_keys($carrito))
                ->get(['id', 'nombre', 'sku', 'imagen', 'precio_mxn', 'stock']);

        $items = $productos
            ->map(fn (Producto $producto) => [
                'producto' => $producto,
                'cantidad' => $carrito[$producto->id] ?? 0,
                'linea' => round((float) $producto->precio_mxn * ($carrito[$producto->id] ?? 0), 2),
            ])
            ->filter(fn (array $item) => $item['cantidad'] > 0)
            ->values();

        $subtotal = round($items->sum(fn (array $item) => $item['linea']), 2);
        $iva = round($subtotal * self::IVA_TASA, 2);
        $total = round($subtotal + $iva, 2);

        return view('livewire.home.checkout', compact('items', 'subtotal', 'iva', 'total'));
    }
}
