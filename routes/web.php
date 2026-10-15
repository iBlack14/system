<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AfectacionTipoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\DocumentoTipoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComprobanteTipoController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ComprobanteSerieController;
use App\Http\Controllers\OrdenesServicioController;
use App\Http\Controllers\InformesController;
use App\Http\Controllers\TecnicosController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\ManoObraController;
use App\Http\Controllers\ServiciosController;
use App\Http\Controllers\CondicionesController;
use App\Http\Controllers\BancosController;
use App\Http\Controllers\TransporteController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\NotaCreditoController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SedesController;
use App\Http\Controllers\ResponsablesController;
use App\Http\Controllers\SalidasAlmacenController;
use App\Http\Controllers\TPersonalController;
use App\Http\Controllers\WooCommerceIntegrationController;


use App\Exports\VentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;


Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('autenticacion.login');
    });
    Route::get('login', function(){
        return view('autenticacion.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/woocommerce', [WooCommerceIntegrationController::class, 'index'])->name('woocommerce.index');
    Route::post('/woocommerce/categories/import', [WooCommerceIntegrationController::class, 'importCategories'])->name('woocommerce.categories.import');
    Route::post('/woocommerce/categories', [WooCommerceIntegrationController::class, 'storeCategory'])->name('woocommerce.categories.store');
    Route::post('/woocommerce/products/{producto}/sync', [WooCommerceIntegrationController::class, 'syncProduct'])->name('woocommerce.products.sync');
    Route::post('/woocommerce/products/sync-all', [WooCommerceIntegrationController::class, 'syncAll'])->name('woocommerce.products.sync-all');
    Route::put('/woocommerce/products/{producto}/category', [WooCommerceIntegrationController::class, 'updateProductCategory'])->name('woocommerce.products.category');
    Route::get('/afectacion-tipos/select', [AfectacionTipoController::class, 'select'])->name('afectacion-tipos.select');
    Route::resource('afectacion-tipos', AfectacionTipoController::class)->except(['create', 'edit']);
    Route::get('/unidades/select', [UnidadController::class, 'select'])->name('unidades.select');
    Route::get('/salidas/select', [SalidasAlmacenController::class, 'select'])->name('salidas.select');
    Route::get('/servicios/select', [ServiciosController::class, 'select'])->name('servicios.select');
    Route::get('/bancos/select', [BancosController::class, 'select'])->name('bancos.select');
    Route::get('/condiciones/select', [CondicionesController::class, 'select'])->name('condiciones.select');
    Route::get('/manoobra/select', [ManoObraController::class, 'select'])->name('manoobra.select');
    Route::get('/notacredito/select', [NotaCreditoController::class, 'select'])->name('notacredito.select');
    Route::get('/permisos/select', [RoleController::class, 'permisos'])->name('permisos.select');
    Route::get('/roles/select', [RoleController::class, 'roles'])->name('roles.select');
    Route::get('/departamentos/select', [DepartamentoController::class, 'select'])->name('departamentos.select');
    Route::get('/provincias/select', [ProvinciaController::class, 'select'])->name('provincias.select');
    //Route::get('/distritos/select', [DistritoController::class, 'select'])->name('distritos.select');
    Route::get('/sedes/select', [SedesController::class, 'sedes'])->name('sedes.select');
    // routes/web.php

    Route::get('ordenestrabajo/por-cliente-multi/{id}',[OrdenTrabajoController::class, 'porClienteMultiEstado']);
    Route::get('informes/{id}/imprimir', [InformesController::class, 'imprimir'] )->name('informes.imprimir');


    

    Route::get('sedes/buscar-por-cliente', [SedesController::class, 'buscarPorCliente'])->name('sedes.buscarPorCliente');
    Route::get('ordenestrabajo/detalle/{id}',[OrdenTrabajoController::class, 'detalle']);
    Route::get('ordenestrabajo/por-cliente-nota/{cliente}/{nota}', [OrdenTrabajoController::class, 'porClienteNota']);
    Route::get('/responsables/select', [ResponsablesController::class, 'responsables'])->name('responsables.select');
    Route::get('/documento-tipos/select', [DocumentoTipoController::class, 'select'])->name('documento-tipos.select');
    Route::get('ordenestrabajo/estado/{estado}',[OrdenTrabajoController::class, 'porEstado'])->name('ordenestrabajo.porEstado');

    Route::get('ordenestrabajo/{id}/timeline',[OrdenTrabajoController::class, 'timeline'])->name('ordenes.timeline');
    
    Route::get('/ordenestrabajo/por-cliente/{cliente}',[OrdenTrabajoController::class, 'porCliente'])->name('ordenestrabajo.porCliente');
    Route::get('sedes/por-cliente/{cliente}', [SedesController::class, 'porCliente']);
    Route::get('responsables/por-cliente/{cliente}', [ResponsablesController::class, 'porCliente']);

    Route::put('ordenestrabajo/{id}/ejecutar',[OrdenTrabajoController::class, 'ejecutar'])->name('ordenestrabajo.ejecutar');
    Route::put('ordenestrabajo/{id}/documentacion',[OrdenTrabajoController::class, 'documentacion'])->name('ordenestrabajo.documentacion');
    Route::put('ordenestrabajo/{id}/facturacion',[OrdenTrabajoController::class, 'facturacion'])->name('ordenestrabajo.facturacion');
    Route::put('ordenestrabajo/{id}/notacredito',[OrdenTrabajoController::class, 'notacredito'])->name('ordenestrabajo.notacredito');
    Route::put('ordenestrabajo/{id}/cancelado',[OrdenTrabajoController::class, 'cancelado'])->name('ordenestrabajo.cancelado');
    Route::put('ordenestrabajo/{id}/procesopago',[OrdenTrabajoController::class, 'procesopago'])->name('ordenestrabajo.procesopago');
    Route::put('ordenesservicio/{id}/conformidad',[OrdenesServicioController::class, 'conformidad'])->name('ordenesservicio.conformidad');
    Route::put('/ordenestrabajo/{id}/estado',[OrdenTrabajoController::class, 'cambiarEstado'])->name('ordenestrabajo.cambiarEstado');


    Route::resource('documento-tipos', DocumentoTipoController::class)->except(['create', 'edit']);
    Route::get('/comprobante-tipos/select', [ComprobanteTipoController::class, 'select'])->name('comprobante-tipos.select');
    Route::resource('comprobante-tipos', ComprobanteTipoController::class)->except(['create', 'edit']);
    Route::resource('comprobante-series', ComprobanteSerieController::class)->except(['create', 'edit']);
    Route::get('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
    Route::get('/ordenesservicio/select', [OrdenesServicioController::class, 'ordenesservicio'])->name('ordenesservicio.select');
    Route::get('/informes/select', [InformesController::class, 'informes'])->name('informes.select');
    Route::get('/tecnicos/select', [TecnicosController::class, 'tecnicos'])->name('tecnicos.select');
    
    Route::get('/transporte/select', [TransporteController::class, 'transporte'])->name('transporte.select');
    Route::get('/transporte/buscar', [TransporteController::class, 'buscar'])->name('transporte.buscar');
    Route::get('/tpersonal/select', [TPersonalController::class, 'tpersonal'])->name('tpersonal.select');
    Route::get('/tpersonal/buscar', [TPersonalController::class, 'buscar'])->name('tpersonal.buscar');
    Route::get('/servicios/buscar', [ServiciosController::class, 'buscar'])->name('servicios.buscar');
    Route::get('/condiciones/buscar', [CondicionesController::class, 'buscar'])->name('condiciones.buscar');
    Route::get('/manoobra/buscar', [ManoObraController::class, 'buscar'])->name('manoobra.buscar');
    Route::get('/cotizaciones/select', [CotizacionesController::class, 'cotizaciones'])->name('cotizaciones.select');
    Route::get('/ordenestrabajo/select', [OrdenTrabajoController::class, 'ordenestrabajo'])->name('ordenestrabajo.select');
    Route::get('/ordenestrabajo/por-cliente/{id}', [OrdenTrabajoController::class, 'porCliente'])->name('ordenestrabajo.porCliente');
    Route::get('/provincias/por-departamento/{department_id}', [ProvinciaController::class, 'porDepartamento'])
    ->name('provincias.por-departamento');
    Route::get('distritos/por-provincia/{provinceId}', [DistritoController::class, 'porProvincia'])
    ->where('provinceId', '.*');

    
    Route::get('cotizaciones/por-cliente/{cliente}', [CotizacionesController::class, 'porCliente'])->name('cotizaciones.porCliente');

    Route::resource('unidades', UnidadController::class)->except(['create', 'edit']);
    Route::resource('salidas', SalidasAlmacenController::class)->except(['create', 'edit']);
    Route::resource('servicios', ServiciosController::class)->except(['create', 'edit']);
    Route::resource('notacredito', NotaCreditoController::class)->except(['create', 'edit']);
    Route::resource('condiciones', CondicionesController::class)->except(['create', 'edit']);

    Route::resource('productos', ProductoController::class)->except(['create', 'edit']);
    Route::resource('manoobra', ManoObraController::class)->except(['create', 'edit']);
    Route::resource('transporte', TransporteController::class)->except(['create', 'edit']);
      Route::resource('tpersonal', TPersonalController::class)->except(['create', 'edit']);
    Route::resource('roles', RoleController::class)->except(['create', 'edit']);
    Route::resource('usuarios', UserController::class)->except(['create', 'edit']); 
    Route::resource('ordenesservicio', OrdenesServicioController::class)->except(['create', 'edit']); 
    Route::resource('informes', InformesController::class)->except(['create', 'edit']); 
    Route::get('/tecnicos/buscar', [TecnicosController::class, 'buscar'])->name('tecnicos.buscar');
    Route::resource('tecnicos', TecnicosController::class)->except(['create', 'edit']); 
   // Route::resource('cotizaciones', CotizacionesController::class)->except(['create', 'edit']); 
   Route::get('/sedes/buscar', [SedesController::class, 'buscar'])->name('sedes.buscar');
    Route::resource('sedes', SedesController::class)->except(['create', 'edit']); 
    
    Route::prefix('reportes')->group(function () {

        // 🔵 VENTAS
        Route::get('/ventas', [ReporteController::class, 'ventas'])
        ->name('reportes.ventas');

        Route::get('/ordenes',[ReporteController::class,'reporteOrdenes'])
        ->name('reportes.ordenes');

        Route::get('/ventas/exportar', [ReporteController::class, 'exportarVentas'])
        ->name('reportes.ventas.exportar');

        // 🟠 COMPRAS
        Route::get('/compras', [ReporteController::class, 'compras'])
        ->name('reportes.compras');

        // 🟠 COMPRAS
        Route::get('/pagos', [ReporteController::class, 'pagos'])
        ->name('reportes.pagos');

        Route::get('/compras/exportar', [ReporteController::class, 'exportarCompras'])
        ->name('reportes.compras.exportar');

        Route::get('/pagos/exportar', [ReporteController::class, 'exportarPagos'])
        ->name('reportes.pagos.exportar');

    });
        
    Route::get('/responsables/buscar', [ResponsablesController::class, 'buscar'])->name('responsables.buscar');
    Route::resource('responsables', ResponsablesController::class)->except(['create', 'edit']); 

    Route::resource('departamentos', DepartamentoController::class)->except(['create', 'edit']); 
    Route::resource('provincias', ProvinciaController::class)->except(['create', 'edit']); 
    Route::resource('distritos', DistritoController::class)->except(['create', 'edit']); 

    Route::get('/ordenestrabajo/{id}/imprimir', [OrdenTrabajoController::class, 'printTicket'])->name('ordenestrabajo.imprimir');
    Route::resource('ordenestrabajo', OrdenTrabajoController::class)->except(['create', 'edit']); 

    Route::get('/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
    Route::resource('clientes', ClienteController::class)->except(['create', 'edit']);
   
    Route::get('/ventas/{id}/imprimir', [VentasController::class, 'printTicket'])->name('ventas.imprimir');
    Route::get('/ventas/serie-correlativo', [VentasController::class, 'getSerie'])->name('ventas.get-serie'); 
    Route::resource('ventas', VentasController::class)->except(['create', 'edit']);

    Route::get('/proveedores/buscar', [ProveedorController::class, 'buscar'])->name('proveedores.buscar');
    Route::get('/bancos/getbancos', [BancosController::class, 'getBancos']);
    Route::resource('proveedores', ProveedorController::class)->except(['create', 'edit']);
    Route::resource('bancos', BancosController::class)->except(['create', 'edit']);

    Route::get('/compras/{id}/imprimir', [CompraController::class, 'printTicket'])->name('compras.imprimir');
    Route::resource('compras', CompraController::class)->except(['create', 'edit']);
    Route::get('cotizaciones/imprimir/{id}', [CotizacionesController::class, 'imprimir'])->name('cotizaciones.imprimir');
    Route::get('cotizaciones/imprimirsinigv/{id}', [CotizacionesController::class, 'imprimirsinigv'])->name('cotizaciones.imprimirsinigv');
    //Route::get('ventas/{id}/imprimir', [VentasController::class, 'printTicket'])->name('ventas.imprimir');
    Route::get('ventas/imprimirsinigv/{id}', [VentasController::class, 'imprimirsinigv'])->name('ventas.imprimirsinigv');
    Route::post('/cotizaciones/enviar-correo', [CotizacionesController::class, 'enviarCorreo'])->name('cotizaciones.enviarCorreo');

    
    Route::resource('cotizaciones', CotizacionesController::class)->except(['create', 'edit']);
    

   Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

    
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');

    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');  
 
    Route::post('/productos/importar', [ProductoController::class, 'import'])->name('productos.importar');

});
