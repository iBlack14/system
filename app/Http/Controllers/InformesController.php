<?php

namespace App\Http\Controllers;

use App\Models\Informes;
use App\Models\OrdenTrabajo;
use App\Models\InformeAspecto;
use App\Models\InformeAspectoCatalogo;
use App\Models\InformeAspectoImagen;
use App\Models\InformeAspectoItem;
use App\Models\OrdenTrabajoEstado;
use App\Services\OrdenTrabajoEstadoService;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class InformesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('can:informes_list')->only(['index']);
        $this->middleware('can:informes_create')->only(['store']);
        $this->middleware('can:informes_edit')->only(['show', 'update']);
        $this->middleware('can:informes_delete')->only(['destroy']);
    }

public function index(Request $request)
{
    if ($request->ajax()) {

        $data = Informes::with([
                'ordenTrabajo.cliente',
                'ordenTrabajo.sedes',
                'user',                
            ])
            ->whereNotNull('orden_trabajo_id') // 🛡️ extra seguridad
            ->select([
                'id',
                'orden_trabajo_id',
                'fecha_creacion',
                //'tipo',
                'user_id'
            ])
            ->orderBy('id', 'desc');

            return DataTables::of($data)

            ->addColumn('cliente', function ($row) {
            return optional(optional($row->ordenTrabajo)->cliente)->razon_social ?? '-';
            })
            

            ->addColumn('tipo_informe', function ($row) {

            $estado = optional($row->ordenTrabajo)->estado;

            if ($estado >= 1 && $estado <= 4) {
            return '<span class="badge bg-warning">Inicial</span>';
            }

            if ($estado >= 5) {
            return '<span class="badge bg-success">Final</span>';
            }

            return '<span class="badge bg-secondary">-</span>';
            })



            ->addColumn('orden_trabajo_id', function ($row) {
            return $row->ordenTrabajo->codigo_ot_cliente ?? '-';
            })

            ->addColumn('descripcion', function ($row) {
            return $row->ordenTrabajo->descripcion ?? '-';
            })

            ->addColumn('sedes', function ($row) {
            return optional(optional($row->ordenTrabajo)->sedes)->nombre ?? '-';
            })

            ->addColumn('usuario', function ($row) {
                return $row->user->name ?? '-';
            })            

            ->addColumn('action', function ($row) {

                $buttons = '';

                if (auth()->user()->can('informes_edit') && optional($row->ordenTrabajo)->estado < 8) {
                    $buttons .= view('components.button-edit', ['id' => $row->id])->render();
                }


                if (auth()->user()->can('informes_delete')) {
                    $buttons .= view('components.button-delete', ['id' => $row->id])->render();
                }

                

                /* ================= VER PDF INFORME ================= */
                if (auth()->user()->can('informes_edit') && optional($row->ordenTrabajo)->estado >= 2) {
                    $buttons .= '
                        <a href="' . route('informes.imprimir', $row->id) . '" 
                        target="_blank" 
                        class="btn btn-sm btn-primary ms-1" 
                        title="Ver Informe PDF">
                        <i class="bi bi-eye"></i>
                        </a>
                        ';
                }                

                return $buttons;
            })

            ->filterColumn('descripcion', function ($query, $keyword) {
                $query->whereHas('ordenTrabajo', function ($q) use ($keyword) {
                    $q->where('descripcion', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('usuario', function ($query, $keyword) {
            $query->whereHas('user', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            });
        })

            ->filterColumn('sedes', function ($query, $keyword) {
                $query->whereHas('ordenTrabajo.sedes', function ($q) use ($keyword) {
                    $q->where('nombre', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('cliente', function ($query, $keyword) {
                $query->whereHas('ordenTrabajo', function ($q) use ($keyword) {
                    $q->whereHas('cliente', function ($c) use ($keyword) {
                        $c->where('razon_social', 'like', "%{$keyword}%");
                    });
                });
            })

            ->filterColumn('orden_trabajo_id', function ($query, $keyword) {
                $query->whereHas('ordenTrabajo', function ($q) use ($keyword) {
                    $q->where('codigo_ot_cliente', 'like', "%{$keyword}%");
                });
            })          
            
            ->rawColumns(['action', 'tipo_informe'])
            ->make(true);
    }

    // Vista normal
    $aspectosCatalogo = InformeAspectoCatalogo::orderBy('orden')->get();

    return view('informes.index', compact('aspectosCatalogo'));
}


public function imprimir($id)
{
    try {

        $informe = Informes::with([
            'ordenTrabajo.cliente',
            'ordenTrabajo.sedes',            
            'ordenTrabajo.responsables',
            'ordenTrabajo.estadoCatalogo',
            'user',
            'aspectos.aspectoCatalogo',
            'aspectos.items',
            'aspectos.imagenes'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('informes.pdf', compact('informe'));

        return $pdf->stream("Informe_{$informe->id}.pdf");

    } catch (\Exception $e) {

        \Log::error('❌ Error al generar PDF del informe', [
            'error' => $e->getMessage()
        ]);

        abort(500, 'No se pudo generar el PDF del informe');
    }
}

    

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
{
    /* ================= VALIDACIÓN ================= */
    $this->validateData($request);

    /* ================= OT ================= */
    $ordenTrabajo = OrdenTrabajo::findOrFail($request->ot_codigo);

    if ($ordenTrabajo->estado != 1) {
        return response()->json([
            'success' => false,
            'message' => 'Solo se puede crear informe cuando la OT está en estado INICIAL.'
        ], 422);
    }

    if (Informes::where('orden_trabajo_id', $ordenTrabajo->id)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'Ya existe un informe para esta OT.'
        ], 422);
    }

    DB::beginTransaction();

    try {

        /* ================= CREAR INFORME ================= */
        $informe = Informes::create([
            'orden_trabajo_id' => $ordenTrabajo->id,
            'fecha_creacion'   => now(),
            'user_id'          => Auth::id(),
        ]);

        /* ================= ASPECTOS PERMITIDOS ================= */
        $aspectosPermitidos = InformeAspectoCatalogo::where('aplica_inicial', 1)
            ->pluck('id')
            ->toArray();

        /* =====================================================
           ITEMS
        ===================================================== */
        foreach ($request->items ?? [] as $aspectoId => $items) {

            if (!in_array($aspectoId, $aspectosPermitidos)) continue;

            $itemsValidos = collect($items)
                ->map(fn($t) => is_array($t) ? ($t['contenido'] ?? '') : $t)
                ->filter(fn($t) => trim((string)$t) !== '')
                ->values();

            if ($itemsValidos->isEmpty()) continue;

            $aspecto = InformeAspecto::create([
                'informe_id' => $informe->id,
                'aspecto_id' => $aspectoId,
            ]);

            foreach ($itemsValidos as $i => $texto) {
                InformeAspectoItem::create([
                    'informe_aspecto_id' => $aspecto->id,
                    'contenido' => $texto,
                    'orden' => $i + 1,
                ]);
            }
        }

        /* =====================================================
           IMÁGENES (🔥 CORRECTO Y ESTABLE)
        ===================================================== */

        $imagenesRequest = $request->file('imagenes') ?? [];

        foreach ($imagenesRequest as $aspectoId => $grupos) {

            if (!in_array($aspectoId, $aspectosPermitidos)) continue;

            $aspecto = InformeAspecto::firstOrCreate([
                'informe_id' => $informe->id,
                'aspecto_id' => $aspectoId,
            ]);

            foreach ($grupos as $grupoId => $imagenes) {

                $descripcion = $request->descripcion_grupo[$grupoId] ?? null;
                $ordenGrupo  = $request->orden_grupo[$grupoId] ?? 1;

                foreach ($imagenes as $index => $imagen) {

                    if (!$imagen instanceof \Illuminate\Http\UploadedFile) continue;
                    if (!$imagen->isValid()) continue;

                    /* ===== GUARDADO SIMPLE Y SEGURO ===== */
                    $nombre = uniqid() . '.' . $imagen->getClientOriginalExtension();

                    $ruta = $imagen->storeAs(
                        "informes/{$informe->id}/aspectos/{$aspectoId}",
                        $nombre,
                        'public'
                    );

                    /* ===== GUARDAR BD ===== */
                    InformeAspectoImagen::create([
                        'informe_aspecto_id' => $aspecto->id,
                        'ruta_imagen'        => $ruta,
                        'grupo_id'           => $grupoId,
                        'grupo_descripcion'  => $descripcion,
                        'orden_grupo'        => $ordenGrupo,
                        'orden'              => $index + 1,
                    ]);
                }
            }
        }

        /* ================= CAMBIAR ESTADO ================= */
        OrdenTrabajoEstadoService::cambiarEstado($ordenTrabajo, 2);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Informe creado correctamente'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        \Log::error('ERROR STORE INFORME', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al guardar informe',
            'error'   => $e->getMessage(),
        ], 500);
    }
}





    /**
     * Display the specified resource.
     */
public function show($id)
{
    try {

        $informe = Informes::with([
            'ordenTrabajo.cliente',
            'ordenTrabajo.estadoCatalogo',
            'user',
            'aspectos.aspectoCatalogo',
            'aspectos.items',
            'aspectos.imagenes' => function($query) {
                $query->orderBy('orden_grupo')
                      ->orderBy('orden');
            }
        ])->findOrFail($id);

        /* =========================
           FORMATEAR ASPECTOS
        ========================= */
        $aspectos = $informe->aspectos->map(function($aspecto) {

            $imagenes = $aspecto->imagenes ?? collect();

            $grupos = $imagenes
    ->groupBy('grupo_id')
    ->map(function($imagenesGrupo, $grupoId) {
        return [
            'grupo_id'    => $grupoId,
            'orden_grupo' => optional($imagenesGrupo->first())->orden_grupo ?? 0, // 🔥
            'descripcion' => optional($imagenesGrupo->first())->grupo_descripcion,
            'imagenes'    => $imagenesGrupo->values()->map(function($img){
                return [
                    'id'          => $img->id,
                    'ruta_imagen' => $img->ruta_imagen
                ];
            })
        ];
    })
    ->sortBy('orden_grupo') // 🔥 ORDENAR ANTES DE ENVIAR
    ->values();

            return [
                'aspecto_id' => $aspecto->aspecto_id, // 🔥 CLAVE
                'nombre'     => optional($aspecto->aspectoCatalogo)->nombre,
                'items'      => $aspecto->items->map(function($item){
                    return [
                        'id'        => $item->id,
                        'contenido' => $item->contenido
                    ];
                }),
                'grupos'     => $grupos
            ];
        });

        /* =========================
           RESPONSE FINAL
        ========================= */
        return response()->json([
            'id'           => $informe->id,
            'fecha'        => $informe->fecha_creacion,
            'ordenTrabajo' => [
                'id'              => $informe->ordenTrabajo->id ?? null,
                'codigo_ot_cliente' => $informe->ordenTrabajo->codigo_ot_cliente ?? null,
                'descripcion'     => $informe->ordenTrabajo->descripcion ?? null,
                'estado'          => $informe->ordenTrabajo->estado ?? null,
                'estado_catalogo' => [
                    'nombre' => optional($informe->ordenTrabajo->estadoCatalogo)->nombre
                ],
                'cliente' => [
                    'id'            => optional($informe->ordenTrabajo->cliente)->id,
                    'razon_social'  => optional($informe->ordenTrabajo->cliente)->razon_social
                ]
            ],
            'usuario' => [
                'id'   => optional($informe->user)->id,
                'name' => optional($informe->user)->name
            ],
            'aspectos' => $aspectos
        ]);

    } catch (\Throwable $e) {

        \Log::error('❌ ERROR SHOW INFORME', [
            'id'    => $id,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al obtener el informe',
            'error'   => $e->getMessage()
        ], 500);
    }
}




   

    /**
     * Update the specified resource in storage.
     */

   public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $data = $this->validateData($request, $id);

        $informe = Informes::with('aspectos.imagenes', 'aspectos.items')
            ->findOrFail($id);

        $ordenTrabajo = $informe->ordenTrabajo;

        /* =====================================================
           DEFINIR ASPECTOS PERMITIDOS SEGÚN ESTADO OT
        ===================================================== */
        if (in_array($ordenTrabajo->estado, [4, 5, 6, 7])) {
            $aspectosPermitidos = InformeAspectoCatalogo::where(function ($q) {
                $q->where('aplica_inicial', 1)
                  ->orWhere('aplica_final', 1);
            })->pluck('id')->toArray();
        } else {
            $aspectosPermitidos = InformeAspectoCatalogo::where('aplica_inicial', 1)
                ->pluck('id')
                ->toArray();
        }

        /* =====================================================
           ELIMINAR IMÁGENES INDIVIDUALES
        ===================================================== */
        if ($request->filled('imagenes_eliminar')) {
            foreach ($request->imagenes_eliminar as $imagenId) {
                $imagen = InformeAspectoImagen::find($imagenId);
                if ($imagen) {
                    Storage::disk('public')->delete($imagen->ruta_imagen);
                    $imagen->delete();
                }
            }
        }

        /* =====================================================
           ELIMINAR GRUPOS COMPLETOS
        ===================================================== */
        if ($request->filled('grupos_eliminar')) {

            $gruposEliminar = array_unique($request->grupos_eliminar);
            $aspectoIds     = $informe->aspectos->pluck('id');

            $imagenes = InformeAspectoImagen::whereIn('grupo_id', $gruposEliminar)
                ->whereIn('informe_aspecto_id', $aspectoIds)
                ->get();

            foreach ($imagenes as $img) {
                if ($img->ruta_imagen && Storage::disk('public')->exists($img->ruta_imagen)) {
                    Storage::disk('public')->delete($img->ruta_imagen);
                }
                $img->delete();
            }

            \Log::info('GRUPOS ELIMINADOS:', $gruposEliminar);
        }

        /* =====================================================
           🔥 FIX BUG 1 — ACTUALIZAR DESCRIPCIÓN Y ORDEN
           Se recorre orden_grupo directamente (no descripcion_grupo)
           para cubrir también grupos nuevos sin imágenes previas en BD
        ===================================================== */
        if ($request->has('orden_grupo')) {

            foreach ($request->orden_grupo as $grupoId => $ordenGrupo) {

                // Saltar grupos marcados para eliminar
                if (in_array($grupoId, $request->grupos_eliminar ?? [])) {
                    continue;
                }

                $descripcion = $request->descripcion_grupo[$grupoId] ?? null;

                InformeAspectoImagen::where('grupo_id', $grupoId)
                    ->whereHas('informeAspecto', function ($q) use ($informe) {
                        $q->where('informe_id', $informe->id);
                    })
                    ->update([
                        'grupo_descripcion' => $descripcion,
                        'orden_grupo'       => (int) $ordenGrupo,
                    ]);
            }
        }

        /* =====================================================
           ACTUALIZAR ORDEN DE IMÁGENES (DRAG & DROP)
        ===================================================== */
        if ($request->has('orden_imagen_id')) {
            foreach ($request->orden_imagen_id as $grupoId => $ids) {
                foreach ($ids as $index => $imgId) {
                    InformeAspectoImagen::where('id', $imgId)
                        ->update(['orden' => $index + 1]);
                }
            }
        }

        /* =====================================================
           SINCRONIZAR ITEMS
        ===================================================== */
        foreach ($aspectosPermitidos as $aspectoId) {

            $itemsRequest = $request->items[$aspectoId] ?? [];

            $informeAspecto = $informe->aspectos()
                ->where('aspecto_id', $aspectoId)
                ->first();

            if (!$informeAspecto && empty($itemsRequest)) {
                continue;
            }

            if (!$informeAspecto) {
                $informeAspecto = $informe->aspectos()->create([
                    'aspecto_id' => $aspectoId,
                ]);
            }

            $idsEnviados = [];

            foreach ($itemsRequest as $item) {

                $contenido = trim($item['contenido'] ?? '');
                if ($contenido === '') continue;

                if (!empty($item['id'])) {
                    $itemModel = InformeAspectoItem::find($item['id']);
                    if ($itemModel) {
                        $itemModel->update(['contenido' => $contenido]);
                        $idsEnviados[] = $itemModel->id;
                    }
                } else {
                    $maxOrden = $informeAspecto->items()->max('orden') ?? 0;
                    $nuevo = $informeAspecto->items()->create([
                        'contenido' => $contenido,
                        'orden'     => $maxOrden + count($idsEnviados) + 1,
                    ]);
                    $idsEnviados[] = $nuevo->id;
                }
            }

            $informeAspecto->items()
                ->whereNotIn('id', $idsEnviados)
                ->delete();

            // Reordenar sin conflictos
            $itemsFinales = $informeAspecto->items()->orderBy('orden')->get();

            foreach ($itemsFinales as $item) {
                $item->orden = $item->orden + 1000;
                $item->save();
            }

            $orden = 1;
            foreach ($itemsFinales as $item) {
                $item->orden = $orden++;
                $item->save();
            }
        }

        /* =====================================================
           GUARDAR IMÁGENES NUEVAS
        ===================================================== */
        if ($request->file('imagenes')) {

            foreach ($request->file('imagenes') as $aspectoId => $grupos) {

                if (!in_array($aspectoId, $aspectosPermitidos)) continue;

                $informeAspecto = $informe->aspectos()
                    ->where('aspecto_id', $aspectoId)
                    ->first();

                if (!$informeAspecto) {
                    $informeAspecto = $informe->aspectos()->create([
                        'aspecto_id' => $aspectoId,
                    ]);
                }

                foreach ($grupos as $grupoId => $imagenes) {

                    if (in_array($grupoId, $request->grupos_eliminar ?? [])) continue;

                    $descripcion = $request->descripcion_grupo[$grupoId] ?? null;

                    // 🔥 FIX BUG 3 — orden_grupo garantizado para grupos nuevos
                    $ordenGrupo = isset($request->orden_grupo[$grupoId])
                        ? (int) $request->orden_grupo[$grupoId]
                        : 999; // al final si no viene

                    $ultimoOrden = InformeAspectoImagen::where('informe_aspecto_id', $informeAspecto->id)
                        ->where('grupo_id', $grupoId)
                        ->max('orden');

                    $orden = $ultimoOrden ? $ultimoOrden + 1 : 1;

                    foreach ($imagenes as $imagen) {

                        if (!$imagen->isValid()) continue;

                        $info = getimagesize($imagen);
                        $mime = $info['mime'];

                        switch ($mime) {
                            case 'image/jpeg':
                                $imgOriginal = imagecreatefromjpeg($imagen);
                                break;
                            case 'image/png':
                                $imgOriginal = imagecreatefrompng($imagen);
                                break;
                            case 'image/webp':
                                $imgOriginal = imagecreatefromwebp($imagen);
                                break;
                            default:
                                continue 2;
                        }

                        $ancho = imagesx($imgOriginal);
                        $alto  = imagesy($imgOriginal);

                        $maxAncho = 1200;

                        if ($ancho > $maxAncho) {
                            $nuevoAncho = $maxAncho;
                            $nuevoAlto  = intval(($nuevoAncho * $alto) / $ancho);
                        } else {
                            $nuevoAncho = $ancho;
                            $nuevoAlto  = $alto;
                        }

                        $img = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        if ($mime === 'image/png') {
                            imagealphablending($img, false);
                            imagesavealpha($img, true);
                        }

                        imagecopyresampled($img, $imgOriginal, 0, 0, 0, 0,
                            $nuevoAncho, $nuevoAlto, $ancho, $alto);

                        $nombre = uniqid() . '.jpg';
                        $rutaFinal = "informes/{$informe->id}/aspectos/{$aspectoId}/{$nombre}";

                        ob_start();
                        $imagenCodificada = imagejpeg($img, null, 75);
                        $contenidoImagen = ob_get_clean();

                        if (!$imagenCodificada || !$contenidoImagen ||
                            !\Illuminate\Support\Facades\Storage::disk('public')->put($rutaFinal, $contenidoImagen)) {
                            imagedestroy($imgOriginal);
                            imagedestroy($img);
                            throw new \RuntimeException('No se pudo guardar la fotografía del informe.');
                        }

                        imagedestroy($imgOriginal);
                        imagedestroy($img);

                        InformeAspectoImagen::create([
                            'informe_aspecto_id' => $informeAspecto->id,
                            'ruta_imagen'        => $rutaFinal,
                            'grupo_id'           => $grupoId,
                            'grupo_descripcion'  => $descripcion,
                            'orden_grupo'        => $ordenGrupo, // 🔥 garantizado
                            'orden'              => $orden++,
                        ]);
                    }
                }
            }
        }

        /* =====================================================
           SI OT ESTÁ EN 4 → FINALIZAR AUTOMÁTICAMENTE
        ===================================================== */
        if ($ordenTrabajo->estado == 4) {
            OrdenTrabajoEstadoService::cambiarEstado($ordenTrabajo, 5);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Informe actualizado correctamente'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        \Log::error('ERROR UPDATE INFORME', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar',
            'error'   => $e->getMessage()
        ], 500);
    }
}




    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
      try {
            $registro = Informes::findOrFail($id);
            $registro->delete();

            return response()->json([
                'success' => true,
                'message' => 'Registro eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el registro'
            ], 500);
        }
    }

protected function validateData(Request $request, $isUpdate = false)
{
    return $request->validate([

        // Solo obligatorio en store
        'ot_codigo' => [
            $isUpdate ? 'sometimes' : 'required',
            'exists:ordenes_trabajo,id'
        ],

        'items' => 'array',
        'items.*' => 'array',
        'items.*.*' => 'array', // 🔥 IMPORTANTE
        'items.*.*.contenido' => 'required|string|max:5000',
        'items.*.*.id' => 'nullable',

        'imagenes' => ['nullable', 'array'],
        'imagenes.*' => ['nullable', 'array'], // aspecto
        'imagenes.*.*' => ['nullable', 'array'], // grupo
        'imagenes.*.*.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // imagen real

        'descripcion_grupo' => ['nullable', 'array'],
        'descripcion_grupo.*' => ['nullable', 'string'],
    ]);
}

  public function buscar(Request $request)
    {
        $q = $request->input('q');
        return ManoObra::where('descripcion', 'like', "%{$q}%")
                    ->orWhere('precio', 'like', "%{$q}%")
                    ->select('id', 'descripcion','precio')
                    ->limit(10)
                    ->get();
    }



}
