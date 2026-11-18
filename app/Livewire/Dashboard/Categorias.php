<?php

namespace App\Livewire\Dashboard;

use App\Models\Categoria;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Categorias extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $mostrarModal = false;

    public ?int $editandoId = null;

    public string $nombre = '';

    public ?string $descripcion = null;

    public function nueva(): void
    {
        $this->reset(['editandoId', 'nombre', 'descripcion']);
        $this->descripcion = null;
        $this->resetValidation();
        $this->mostrarModal = true;
    }

    public function editar(int $id): void
    {
        $categoria = Categoria::query()->findOrFail($id);

        $this->editandoId = $categoria->id;
        $this->nombre = $categoria->nombre;
        $this->descripcion = $categoria->descripcion;
        $this->resetValidation();
        $this->mostrarModal = true;
    }

    public function cerrarModal(): void
    {
        $this->mostrarModal = false;
        $this->reset(['editandoId', 'nombre', 'descripcion']);
        $this->descripcion = null;
        $this->resetValidation();
    }

    public function guardar(): void
    {
        $this->nombre = $this->normalizarNombre((string) $this->nombre);

        $validated = $this->validate();

        if ($this->editandoId) {
            Categoria::query()->whereKey($this->editandoId)->update($validated);

            session()->flash('success', "Categoría «{$validated['nombre']}» actualizada correctamente.");
        } else {
            Categoria::query()->create($validated);

            session()->flash('success', "Categoría «{$validated['nombre']}» creada correctamente.");
        }

        $this->cerrarModal();
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updated($property): void
    {
        if ($property === 'nombre') {
            $normalizado = $this->normalizarNombre((string) $this->nombre);

            if ($normalizado !== $this->nombre) {
                $this->nombre = $normalizado;
            }
        }

        $this->validateOnly($property);
    }

    /**
     * Elimina caracteres especiales y reemplaza acentos.
     * Solo conserva [A-Za-z0-9 ].
     */
    protected function normalizarNombre(string $valor): string
    {
        $valor = strtr($valor, [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a',
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U',
            'ñ' => 'n', 'Ñ' => 'N', 'ç' => 'c', 'Ç' => 'C',
        ]);

        // Elimina todo lo que no sea letra sin acento, número o espacio.
        $valor = (string) preg_replace('/[^A-Za-z0-9 ]+/', '', $valor);

        // Elimina espacios adicionales
        $valor = (string) preg_replace('/\s+/', ' ', $valor);

        return trim($valor);
    }

    protected function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9 ]+$/',
                $this->editandoId
                    ? Rule::unique('categorias', 'nombre')->ignore($this->editandoId)
                    : 'unique:categorias,nombre',
            ],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Esta categoría ya está registrada.',
            'nombre.max' => 'El nombre no debe exceder 255 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras sin acento, números y espacios.',
            'descripcion.max' => 'La descripción no debe exceder 1000 caracteres.',
        ];
    }

    #[Title('Categorías')]
    #[Layout('components.layouts.admin')]
    public function render()
    {
        $categorias = Categoria::query()
            ->when(trim($this->search) !== '', function ($query) {
                $term = '%' . trim($this->search) . '%';
                $query->where('nombre', 'like', $term);
            })
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view('livewire.dashboard.categorias', [
            'categorias' => $categorias,
        ]);
    }
}
