<?php

namespace App\Livewire\Dashboard;

use App\Models\Producto;
use App\Services\ExchangeService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class CrearProducto extends Component
{
    use WithFileUploads;

    public $sku = '';

    public $nombre = '';

    public $descripcion_corta = '';

    public $descripcion_larga = '';

    public $imagen = null;

    public $precio_usd = '';

    public $precio_mxn = '';

    public $stock = 0;

    public $fecha_vigencia = null;

    public $activo = true;

    // Tipo de cambio USD -> MXN obtenido del ExchangeService
    public $tipoCambio = null;

    public $errorTipoCambio = false;

    #[Title('Agregar producto')]
    #[Layout('components.layouts.admin')]
    public function render()
    {
        return view('livewire.dashboard.crear-producto');
    }

    /**
     * Validación en tiempo real
     * 
     */
    public function updated($property): void
    {
        if ($property === 'fecha_vigencia' && $this->fecha_vigencia === '') {
            $this->fecha_vigencia = null;
        }

        if (in_array($property, ['precio_usd', 'precio_mxn'], true)) {
            $this->convertirPrecio($property);

            $this->validateOnly($property);

            $otroPrecio = $property === 'precio_mxn' ? 'precio_usd' : 'precio_mxn';

            if ($this->{$otroPrecio} !== '' && $this->{$otroPrecio} !== null) {
                $this->validateOnly($otroPrecio);
            }

            return;
        }

        $this->validateOnly($property);
    }

    public function reintentarTipoCambio(): void
    {
        $this->tipoCambio = null;
        $this->errorTipoCambio = false;

        $this->obtenerTipoCambio();
    }

    public function guardar()
    {
        $validated = $this->validate();

        // Guarda la imagen en disco (storage/app/public/products)
        $validated['imagen'] = $this->imagen
            ? $this->imagen->store('products', 'public')
            : null;

        $producto = Producto::create($validated);

        session()->flash('success', "Producto {$producto->sku} creado correctamente.");

        return redirect()->route('dashboard.productos');
    }

    /**
     * Si convertimos a mxn o usd según lo que escriba el usuario
     */
    protected function convertirPrecio(string $property): void
    {
        $valor = $this->{$property};

        if (! is_numeric($valor) || (float) $valor <= 0) {
            return;
        }

        $rate = $this->obtenerTipoCambio();

        if ($rate === null) {
            return;
        }

        if ($property === 'precio_mxn') {
            $this->precio_usd = round((float) $valor / $rate, 2);
        } else {
            $this->precio_mxn = round((float) $valor * $rate, 2);
        }
    }

    protected function obtenerTipoCambio(): ?float
    {
        if ($this->tipoCambio !== null) {
            return (float) $this->tipoCambio;
        }

        if ($this->errorTipoCambio) {
            return null;
        }

        try {
            $rate = app(ExchangeService::class)->getExchangeRate();
        } catch (\Throwable $e) {
            report($e);
            $rate = null;
        }

        if (! is_numeric($rate) || (float) $rate <= 0) {
            $this->errorTipoCambio = true;

            return null;
        }

        $this->tipoCambio = (float) $rate;

        return $this->tipoCambio;
    }

    protected function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:255', 'unique:productos,sku'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion_corta' => ['nullable', 'string', 'max:500'],
            'descripcion_larga' => ['nullable', 'string', 'max:2000'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'precio_usd' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'precio_mxn' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999999'],
            'fecha_vigencia' => ['nullable', 'date'],
            'activo' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'sku.required' => 'El SKU es obligatorio.',
            'sku.unique' => 'Este SKU ya está registrado.',
            'sku.max' => 'El SKU no debe exceder 255 caracteres.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder 255 caracteres.',
            'descripcion_corta.max' => 'La descripción corta no debe exceder 500 caracteres.',
            'descripcion_larga.max' => 'La descripción larga no debe exceder 2000 caracteres.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser un archivo JPG, JPEG, PNG o WebP.',
            'imagen.max' => 'La imagen no debe pesar más de 2 MB.',
            'imagen.uploaded' => 'No se pudo subir la imagen. Intenta con un archivo más pequeño.',
            'precio_usd.required' => 'El precio en USD es obligatorio.',
            'precio_usd.numeric' => 'El precio en USD debe ser un número.',
            'precio_usd.min' => 'El precio en USD debe ser mayor a 0.',
            'precio_usd.max' => 'El precio en USD es demasiado grande.',
            'precio_mxn.required' => 'El precio en MXN es obligatorio.',
            'precio_mxn.numeric' => 'El precio en MXN debe ser un número.',
            'precio_mxn.min' => 'El precio en MXN debe ser mayor a 0.',
            'precio_mxn.max' => 'El precio en MXN es demasiado grande.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
            'stock.max' => 'El stock es demasiado grande.',
            'fecha_vigencia.date' => 'La fecha de vigencia no es válida.',
        ];
    }

    protected function attributes(): array
    {
        return [
            'sku' => 'SKU',
            'nombre' => 'nombre',
            'descripcion_corta' => 'descripción corta',
            'descripcion_larga' => 'descripción larga',
            'imagen' => 'imagen',
            'precio_usd' => 'precio en USD',
            'precio_mxn' => 'precio en MXN',
            'stock' => 'stock',
            'fecha_vigencia' => 'fecha de vigencia',
            'activo' => 'estatus',
        ];
    }
}
