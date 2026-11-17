<div>
    <h1>Productos</h1>

    <div>
        @foreach ($productos as $producto)
            <div>{{ $producto->nombre }}</div>
        @endforeach
        {{ $productos->links() }}
    </div>
</div>
