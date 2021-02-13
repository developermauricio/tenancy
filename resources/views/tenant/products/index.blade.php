@extends('layouts.app')

@section('content')
    <div class="container">
        <a class="btn btn-primary" href="{{ route('tenants.products.create') }}">
            {{ __("Nuevo producto") }}
        </a>
        <div class="row">
            <table class="table table-bordered text-center">
                <thead>
                <tr class="table-light">
                    <th>{{ __("Nombre") }}</th>
                    <th>{{ __("Precio") }}</th>
                    <th>{{ __("Acciones") }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr class="text-white">
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>
                            <a class="btn btn-info" href="{{ route('tenants.products.show', ["id" => $product->id]) }}">
                                {{ __("Detalle") }}
                            </a>
                            <a class="btn btn-danger" href="{{ route('tenants.products.destroy', ["id" => $product->id]) }}">
                                {{ __("Eliminar") }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <div class="alert alert-danger">No hay datos todavía</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
