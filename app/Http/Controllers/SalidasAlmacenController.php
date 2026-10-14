<?php

namespace App\Http\Controllers;
use App\Models\SalidaAlmacen;
use App\Models\Producto;
use App\Models\ComprobanteSerie;
use App\Models\SalidaAlmacenProducto;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SalidasAlmacenController extends Controller
{
    //

        public function __construct(){
        $this->middleware('can:salidas_list')->only(['index']);
        $this->middleware('can:salidas_create')->only(['store']);
        $this->middleware('can:salidas_edit')->only(['show', 'update']);
        $this->middleware('can:salidas_delete')->only(['destroy']);
    }


    public function index(Request $request)
{
    if ($request->ajax()) {

        $data = SalidaAlmacen::with([
            'user',
            'detalles.producto',
            'cliente'
        ])
        ->select([
            'id',
            'fecha_salida',
            'motivo',
            'cliente_id',
            'observaciones',
            'serie',
            'correlativo',
            'estado',
            'total',
            'user_id'
        ])
        ->orderBy('id', 'desc');

        return DataTables::of($data)

            ->addColumn('usuario', function ($row) {
                return optional($row->user)->name ?? '-';
            })
            ->addColumn('cliente', function ($row) {
                return optional($row->cliente)->razon_social ?? '—';
            })

            ->editColumn('fecha', function ($row) {
                return \Carbon\Carbon::parse($row->fecha)
                    ->format('d/m/Y');
            })

            ->editColumn('estado', function ($row) {

                $badgeClass = match ($row->estado) {
                    'registrado' => 'success',
                    'anulado'    => 'danger',
                    default      => 'secondary',
                };

                return "<span class='badge bg-{$badgeClass} text-uppercase'>{$row->estado}</span>";
            })

            ->addColumn('action', function ($row) {

                $buttons = '';

                if (auth()->user()->can('salidas_edit')
                    && $row->estado !== 'anulado') {

                    $buttons .= view(
                        'components.button-edit',
                        ['id' => $row->id]
                    )->render();
                }

                if (auth()->user()->can('salidas_delete')
                    && $row->estado !== 'anulado') {

                    $buttons .= view(
                        'components.button-delete',
                        ['id' => $row->id]
                    )->render();
                }

                return $buttons;
            })

            ->rawColumns(['estado', 'action'])
            ->make(true);
    }

    return view('salidas.index');
}

public function getSerie(Request $request)
    {
        $request->validate([
            'comprobante_tipo_codigo' => 'required|exists:comprobante_tipos,codigo'
        ]);

        $codigo = $request->comprobante_tipo_codigo;

        // Buscar directamente en comprobante_series
        $serieConfig = ComprobanteSerie::where('comprobante_tipo_codigo', $codigo)->first();

        // Si no existe, devolver null
        if (!$serieConfig) {
            return response()->json([
                'serie' => null,
                'numero' => null
            ]);
        }
        // Devolver los valores almacenados en la tabla
        return response()->json([
            'serie' => $serieConfig->serie,
            'numero' => $serieConfig->correlativo
        ]);
    }

    public function show($id)
{
    try {

        $salida = SalidaAlmacen::with([
            'cliente',
            'detalles.producto'
        ])->findOrFail($id);

        return response()->json([
            'id' => $salida->id,
            'cliente_id' => $salida->cliente_id,
            'comprobante_tipo_codigo' => $salida->comprobante_tipo_codigo,
            'serie' => $salida->serie,
            'correlativo' => $salida->correlativo,
            'motivo' => $salida->motivo,
            'observaciones' => $salida->observaciones,
            'estado' => $salida->estado,
            'total' => $salida->total,

            'cliente' => [
                'id' => optional($salida->cliente)->id,
                'razon_social' => optional($salida->cliente)->razon_social,
            ],

            'detalles' => $salida->detalles->map(function ($detalle) {
                return [
                    'id' => $detalle->id,
                    'producto_id' => $detalle->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'subtotal' => $detalle->subtotal,

                    'producto' => [
                        'id' => optional($detalle->producto)->id,
                        'codigo' => optional($detalle->producto)->codigo,
                        'nombre' => optional($detalle->producto)->nombre,
                    ]
                ];
            })
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $data = $this->validateData($request);

        /*
        =====================================
        OBTENER SALIDA + DETALLES
        =====================================
        */
        $salida = SalidaAlmacen::with('detalles')
            ->findOrFail($id);

        /*
        =====================================
        DEVOLVER STOCK ANTERIOR
        (sumar nuevamente lo que salió)
        =====================================
        */
        foreach ($salida->detalles as $detalle) {

            $producto = \App\Models\Producto::find($detalle->producto_id);

            if ($producto) {
                $producto->increment(
                    'stock',
                    floatval($detalle->cantidad)
                );
            }
        }

        /*
        =====================================
        ELIMINAR DETALLES ANTIGUOS
        =====================================
        */
        $salida->detalles()->delete();

        /*
        =====================================
        RECORRER NUEVOS DETALLES
        =====================================
        */
        $totalGeneral = 0;

        if (!empty($data['material_detalles']) && is_array($data['material_detalles'])) {

            foreach ($data['material_detalles'] as $item) {

                if (empty($item['producto_id'])) {
                    continue;
                }

                $producto = \App\Models\Producto::findOrFail(
                    $item['producto_id']
                );

                $cantidad = floatval($item['cantidad'] ?? 0);
                $precio   = floatval($item['precio_venta'] ?? 0);
                $subtotal = $cantidad * $precio;

                /*
                =====================================
                VALIDAR STOCK DISPONIBLE
                =====================================
                */
                if ($producto->stock < $cantidad) {
                    throw new \Exception(
                        "Stock insuficiente para {$producto->nombre}"
                    );
                }

                /*
                =====================================
                DESCONTAR NUEVO STOCK
                =====================================
                */
                $producto->decrement(
                    'stock',
                    $cantidad
                );

                /*
                =====================================
                CREAR NUEVO DETALLE
                =====================================
                */
                SalidaAlmacenProducto::create([
                    'salida_id'       => $salida->id,
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        /*
        =====================================
        ACTUALIZAR CABECERA
        =====================================
        */
        $salida->update([
            'cliente_id'              => $data['cliente_id'],
            'comprobante_tipo_codigo' => $data['comprobante_tipo_codigo'],
            'motivo'                  => $data['motivo'],
            'serie'                   => $data['serie'],
            'correlativo'             => $data['correlativo'],
            'observaciones'           => $data['observaciones'] ?? null,
            'total'                   => $totalGeneral,
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Salida actualizada correctamente'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}

public function store(Request $request)
{
    DB::beginTransaction();

    try {

        $data = $this->validateData($request);

        /*
        =====================================
        CREAR CABECERA
        =====================================
        */

        $salida = SalidaAlmacen::create([
            'cliente_id'              => $data['cliente_id'],
            'comprobante_tipo_codigo' => $data['comprobante_tipo_codigo'],
            'user_id'                 => auth()->id(),
            'fecha_salida'            => now(),
            'motivo'                  => $data['motivo'],
            'serie'                   => $data['serie'],
            'correlativo'             => $data['correlativo'],
            'observaciones'           => $data['observaciones'] ?? null,
            'estado'                  => 'registrado',
            'total'                   => 0
        ]);

        $totalGeneral = 0;

        /*
        =====================================
        DETALLES DE PRODUCTOS
        =====================================
        */

        if (!empty($data['material_detalles']) && is_array($data['material_detalles'])) {

            foreach ($data['material_detalles'] as $item) {

                if (empty($item['producto_id'])) {
                    continue;
                }

                $cantidad = floatval($item['cantidad'] ?? 0);
                $precio   = floatval($item['precio_venta'] ?? 0);
                $subtotal = $cantidad * $precio;

                /*
                DEBUG TEMPORAL
                */
                // dd($item, $cantidad, $precio, $subtotal);

                SalidaAlmacenProducto::create([
                    'salida_id'       => $salida->id,
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }

            /*
            =====================================
            ACTUALIZAR STOCK
            =====================================
            */

            \App\Models\Producto::updateStock(
                false,
                $data['material_detalles']
            );
        }

        /*
        =====================================
        ACTUALIZR TOTAL CABECERA
        =====================================
        */

        $salida->update([
            'total' => $totalGeneral
        ]);

                /*
        =====================================
        ACTUALIZAR CORRELATIVO
        =====================================
        */

        $serieConfig = ComprobanteSerie::where(
            'comprobante_tipo_codigo',
            $data['comprobante_tipo_codigo']
        )->first();

        if ($serieConfig) {
            $serieConfig->increment('correlativo');
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Salida registrada correctamente'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}

public function destroy($id)
{
    DB::beginTransaction();

    try {

        /*
        =====================================
        OBTENER SALIDA + DETALLES
        =====================================
        */
        $salida = SalidaAlmacen::with([
            'detalles.producto'
        ])->findOrFail($id);

        /*
        =====================================
        DEVOLVER STOCK AL PRODUCTO
        =====================================
        */
        foreach ($salida->detalles as $detalle) {

            $producto = Producto::find($detalle->producto_id);

            if ($producto) {

                /*
                Si salió 5 unidades,
                se devuelven 5 unidades al stock
                */
                $producto->increment(
                    'stock',
                    $detalle->cantidad
                );
            }
        }

        /*
        =====================================
        ELIMINAR DETALLES
        =====================================
        */
        $salida->detalles()->delete();

        /*
        =====================================
        IMPORTANTE:
        NO RETROCEDER CORRELATIVO

        Porque si existen:
        1,2,3,4,5,6

        y eliminas el 3,
        no debe volver a usar ese número.

        El siguiente debe seguir siendo:
        7

        para mantener trazabilidad documental.
        =====================================
        */

        /*
        =====================================
        ELIMINAR CABECERA
        =====================================
        */
        $salida->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Salida eliminada y stock restaurado correctamente'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

private function validateData(Request $request)
{
    return $request->validate([
        /*
        =====================================
        CABECERA
        =====================================
        */
        'cliente_id' => 'required|exists:clientes,id',
        'comprobante_tipo_codigo' => 'required|string|max:2',
        'serie' => 'required|string|max:10',
        'correlativo' => 'required|integer',

        'motivo' => 'required|string|max:500',
        'observaciones' => 'nullable|string',

        /*
        =====================================
        DETALLES
        =====================================
        */
        'material_detalles' => 'required|array|min:1',

        'material_detalles.*.producto_id' =>
            'required|exists:productos,id',

        'material_detalles.*.cantidad' =>
            'required|numeric|min:1',

        'material_detalles.*.precio_venta' =>
            'required|numeric|min:0.01',
    ]);
}


}
