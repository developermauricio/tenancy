@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <table class="table table-bordered text-center">
                <thead>
                <tr class="table-light">
                    <th>{{ __("Website") }}</th>
                    <th>{{ __("Alta") }}</th>
                    <th>{{ __("Acciones") }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($hostnames as $hostname)
                    <tr class="text-white">
                        <td>{{ $hostname->website->uuid }}</td>
                        <td>{{ $hostname->created_at }}</td>
                        <td>
                            <a class="btn btn-info" target="_blank" href="//{{ $hostname->website->uuid }}.{{ env('APP_DOMAIN') }}">
                                {{ __("Visitar") }}
                            </a>
                            <a class="btn btn-danger" href="{{ route('tenants.destroy', ["id" => $hostname->id]) }}">
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
