<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ventas;
use App\Models\Compra;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Pagos;
use App\Models\Producto;
use App\Models\OrdenTrabajo;
use App\Models\OrdenTrabajoEstadoCatalogo;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Exports\VentasExport;
use App\Exports\ComprasExport;

class ReporteController extends Controller
{
    public function __construct()
    {
    $this->middleware('can:reporteventas_list')->only(['reportes.ventas']);
    }


public function ventas(Request $request)
{
    $query = Ventas::with([
        'cliente',
        'user',
        'comprobanteTipo',
        'productos.producto', // ✅ CORRECTO
        'servicios',
        'transportes',
        'tpersonal'
    ]);

    if ($request->fecha_inicio) {
        $query->whereDate('created_at', '>=', $request->fecha_inicio);
    }

    if ($request->fecha_fin) {
        $query->whereDate('created_at', '<=', $request->fecha_fin);
    }

    if ($request->user_id) {
        $query->where('user_id', $request->user_id);
    }

    if ($request->cliente_id) {
        $query->where('cliente_id', $request->cliente_id);
    }

    $totalGeneral = (clone $query)->sum('total');

    $ventas = $query->orderBy('created_at', 'desc')->paginate(10);

    $usuarios = User::select('id','name')->get();
    $clientes = Cliente::select('id','razon_social')->get();

    return view('reportes.ventas', compact(
        'ventas',
        'usuarios',
        'clientes',
        'totalGeneral'
    ));
}

public function pagos(Request $request)
{
    /*
    =====================================
    QUERY BASE
    =====================================
    */

    $pagos = Pagos::with([
        'ordenTrabajo.cliente',
        'user'
    ]);

    /*
    =====================================
    FILTRO POR FECHA INICIO
    =====================================
    */

    if ($request->filled('fecha_inicio')) {
        $pagos->whereDate('created_at', '>=', $request->fecha_inicio);
    }

    /*
    =====================================
    FILTRO POR FECHA FIN
    =====================================
    */

    if ($request->filled('fecha_fin')) {
        $pagos->whereDate('created_at', '<=', $request->fecha_fin);
    }

    /*
    =====================================
    FILTRO POR CLIENTE
    =====================================
    */

    if ($request->filled('cliente_id')) {
        $pagos->whereHas('ordenTrabajo.cliente', function ($q) use ($request) {
            $q->where('id', $request->cliente_id);
        });
    }

    /*
    =====================================
    OBTENER DATOS PAGINADOS
    =====================================
    */

    $pagos = $pagos
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    /*
    =====================================
    TOTAL GENERAL
    =====================================
    */

    $totalGeneral = Pagos::with('ordenTrabajo.cliente')
        ->when($request->fecha_inicio, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->fecha_inicio);
        })
        ->when($request->fecha_fin, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->fecha_fin);
        })
        ->when($request->cliente_id, function ($q) use ($request) {
            $q->whereHas('ordenTrabajo.cliente', function ($sub) use ($request) {
                $sub->where('id', $request->cliente_id);
            });
        })
        ->sum('monto_pagado');

    /*
    =====================================
    LISTA DE CLIENTES (FILTRO)
    =====================================
    */

    $clientes = Cliente::orderBy('razon_social')->get();

    /*
    =====================================
    RETORNAR VISTA
    =====================================
    */

    return view('reportes.pagos', compact(
        'pagos',
        'totalGeneral',
        'clientes'
    ));
}


public function compras(Request $request)
{
    $query = Compra::with([
        'proveedor',
        'user',
        'comprobanteTipo',
        'detalles.producto' // 🔥 necesario para mostrar productos
    ]);

    // 🔎 FILTROS
    if ($request->fecha_inicio) {
        $query->whereDate('fecha', '>=', $request->fecha_inicio);
    }

    if ($request->fecha_fin) {
        $query->whereDate('fecha', '<=', $request->fecha_fin);
    }

    if ($request->user_id) {
        $query->where('user_id', $request->user_id);
    }

    if ($request->proveedor_id) {
        $query->where('proveedor_id', $request->proveedor_id);
    }

    // ✅ FILTRO POR PRODUCTO
    if ($request->producto_id) {
        $query->whereHas('detalles', function ($q) use ($request) {
            $q->where('producto_id', $request->producto_id);
        });
    }

    // 💰 TOTAL GENERAL
    $totalGeneral = (clone $query)->sum('total');

    // 📄 PAGINACIÓN
    $compras = $query->orderBy('fecha', 'desc')->paginate(10);

    $usuarios = User::select('id','name')->get();
    $proveedores = Proveedor::select('id','razon_social')->get();
    $productos = Producto::select('id','nombre')->get();

    return view('reportes.compras', compact(
        'compras',
        'usuarios',
        'proveedores',
        'productos',
        'totalGeneral'
    ));
}

public function reporteOrdenes(Request $request)
{
    $query = OrdenTrabajo::with([
        'cliente',
        'tecnico',
        'user',
        'sedes',
        'informes',
        'cotizacion',
        'responsables',
        'estadoCatalogo'
    ])
    //->where('estado', '!=', 13)
    ->orderByDesc('id');

    /*
    |--------------------------------------------------------------------------
    | FILTRO POR CLIENTE
    |--------------------------------------------------------------------------
    */
    if ($request->filled('cliente_id')) {
        $query->where(
            'cliente_id',
            $request->cliente_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO POR ESTADO
    |--------------------------------------------------------------------------
    */
    if ($request->filled('estado')) {
        $query->where(
            'estado',
            $request->estado
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO POR MOTIVO
    |--------------------------------------------------------------------------
    */
    if ($request->filled('motivo')) {
        $query->where(
            'motivo',
            'like',
            '%' . $request->motivo . '%'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO POR CÓDIGO OT CLIENTE
    |--------------------------------------------------------------------------
    */
    if ($request->filled('codigo_ot_cliente')) {
        $query->where(
            'codigo_ot_cliente',
            'like',
            '%' . $request->codigo_ot_cliente . '%'
        );
    }

    $ordenes = $query->paginate(20);

    /*
    |--------------------------------------------------------------------------
    | DATOS PARA FILTROS
    |--------------------------------------------------------------------------
    */
    $clientes = Cliente::orderBy('razon_social')
        ->get();

    $estados = OrdenTrabajoEstadoCatalogo::orderBy('nombre') ->get();

    return view(
        'reportes.ordenes',
        compact(
            'ordenes',
            'clientes',
            'estados'
        )
    );
}


public function exportarVentas(Request $request)
{
    $query = Ventas::with(['cliente','user']);

    // 🔎 FILTROS
    if ($request->fecha_inicio) {
        $query->whereDate('created_at', '>=', $request->fecha_inicio);
    }

    if ($request->fecha_fin) {
        $query->whereDate('created_at', '<=', $request->fecha_fin);
    }

    if ($request->user_id) {
        $query->where('user_id', $request->user_id);
    }

    if ($request->cliente_id) {
        $query->where('cliente_id', $request->cliente_id);
    }

    $data = $query->get()->map(function ($venta) {
        return [
            'Fecha'       => $venta->created_at->format('d/m/Y'),
            'Comprobante' => $venta->serie . '-' . str_pad($venta->correlativo,8,'0',STR_PAD_LEFT),
            'Cliente'     => $venta->cliente->razon_social ?? '-',
            'Vendedor'    => $venta->user->name ?? '-',
            'Total'       => $venta->total,
        ];
    });

    return Excel::download(new class($data) implements FromCollection {

        protected $data;

        public function __construct($data)
        {
            $this->data = $data;
        }

        public function collection()
        {
            return $this->data;
        }

    }, 'reporte_ventas.xlsx');
}

public function exportarCompras(Request $request)
{
    return Excel::download(
        new ComprasExport($request->all()),
        'reporte_compras.xlsx'
    );
}

}
