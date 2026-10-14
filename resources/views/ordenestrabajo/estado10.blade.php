@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">Órdenes de Trabajo – Estado 10</h5>
    </div>

    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código OT Cliente</th>
                    <th>Razón Social</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ordenes as $ot)
                    <tr>
                        <td>{{ $ot->id }}</td>
                        <td>{{ $ot->codigo_ot_cliente }}</td>
                        <td>{{ $ot->cliente->razon_social ?? '—' }}</td>
                        <td>
                            <span class="badge bg-warning text-white">
                                10
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No hay órdenes con estado 10
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
