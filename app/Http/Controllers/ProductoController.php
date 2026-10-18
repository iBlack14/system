<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Imports\ProductosImport;
use Maatwebsite\Excel\Facades\Excel;


class ProductoController extends Controller
{
    public function __construct(){
        $this->middleware('can:productos_list')->only(['index']);
        $this->middleware('can:productos_create')->only(['store']);
        $this->middleware('can:productos_edit')->only(['show', 'update']);
        $this->middleware('can:productos_delete')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Producto::select(
                ['id','unidad_codigo',
                'afectacion_tipo_codigo',
                'codigo',
                'nombre',
                'costo_unitario',
                'stock',  
                'precio_venta',
                'almacen',
                'utilidad',
                'ubicacion_columna',
                'ubicacion_fila',
                'imagen',
                'porcentaje5',
                'porcentaje10',
                'porcentaje50',
                'porcentaje100' 
                ]);

            if ($request->filled('stock_status')) {
                match ($request->stock_status) {
                    'available' => $data->where('stock', '>', 5),
                    'critical'  => $data->where('stock', '>', 0)->where('stock', '<=', 5),
                    'out'       => $data->where('stock', '<=', 0),
                    default     => null,
                };
            }

            $data->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addColumn('action', function ($row) {                 
                    $editButton ='';
                    if(auth()->user()->can('productos_edit')){
                        $editButton = view('components.button-edit', ['id' => $row->id])->render();
                    }
                    $deleteButton = '';
                    if(auth()->user()->can('productos_delete')){
                        $deleteButton = view('components.button-delete', ['id' => $row->id])->render();
                    }
                    return $editButton . $deleteButton;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('productos.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateProducto($request);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            //$filename = time() . '_' . $file->getClientOriginalName();
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/productos/'), $filename);
            $data['imagen'] = $filename;
        }

        Producto::create($data);

        return response()->json([
            'success'=> true,
            'message'=>'Registro creado satisfactoriamente'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $registro = Producto::findOrFail($id);
            return response()->json($registro);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $this->validateProducto($request, $id);
        $registro = Producto::findOrFail($id);
        
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');            
            //$filename = time() . '_' . $file->getClientOriginalName();
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/productos/'), $filename);
            $data['imagen'] = $filename;
            
            $old_image = 'uploads/productos/'.$registro->imagen;
            if (file_exists($old_image)) {
                @unlink($old_image);
            }
        }
        $registro->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado correctamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $registro = Producto::findOrFail($id);
            $old_image = 'uploads/productos/'.$registro->imagen;
            if (file_exists($old_image)) {
                @unlink($old_image);
            }

            $registro->delete();

            return response()->json([
                'success' => true,
                'message' => 'Registro eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el registro.'
            ], 500);
        }
    }

    protected function validateProducto(Request $request)
    {
        return $request->validate([
            'unidad_codigo' => 'required|string|max:3',
            'afectacion_tipo_codigo' => 'required|string|max:2',
            'codigo' => 'required|string|max:50',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:255',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:102400',
            'costo_unitario' => 'required|numeric|between:0,9999.99',
            'utilidad' => 'required|numeric',
            'stock' => 'required|numeric',
            'precio_venta' => 'required|numeric|between:0,9999.99',
            'almacen' => 'required|string|max:50',
            'ubicacion_columna' => 'required|string|max:50',
            'ubicacion_fila' => 'required|string|max:50',
            'porcentaje5' => 'required|numeric|between:0,99.99',
            'porcentaje10' => 'required|numeric|between:0,99.99',
            'porcentaje50' => 'required|numeric|between:0,99.99',
            'porcentaje100' => 'required|numeric|between:0,99.99'
        ]);
    }

    /* public function buscar(Request $request)
    {
        $q = $request->input('q');
        return Producto::with('afectacionTipo:codigo,porcentaje')
                    ->where('nombre', 'like', "%{$q}%")
                    ->orWhere('codigo', 'like', "%{$q}%")
                    ->select('id','codigo', 'nombre','costo_unitario' ,'precio_venta','afectacion_tipo_codigo')
                    ->limit(10)
                    ->get();
    } */

public function buscar(Request $request)
{
    $productos = Producto::where('nombre', 'like', "%{$request->q}%")
        ->orWhere('codigo', 'like', "%{$request->q}%")
        ->limit(10)
        ->get();

    return response()->json(
        $productos->map(function ($p) {
            return [
                'id'            => $p->id,
                'codigo'        => $p->codigo,
                'nombre'        => $p->nombre,
                'precio_venta'  => $p->precio_venta,
                'precio_base'   => $p->precio_venta,
                'costo_unitario'   =>$p->costo_unitario,

                // 🔥 CORREGIDO
                'descuento_5'   => $p->porcentaje5 ?? 0,
                'descuento_10'  => $p->porcentaje10 ?? 0,
                'descuento_50'  => $p->porcentaje50 ?? 0,
                'descuento_100' => $p->porcentaje100 ?? 0,
            ];
        })
    );
}
    
    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ProductosImport, $request->file('archivo'));

        return redirect()->back()->with('success', 'Productos importados correctamente');
    }


}
