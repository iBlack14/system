# Rediseño móvil — avance y validación

## Alcance

Cambios de presentación en Blade, CSS y JavaScript de interfaz. Sin cambios de controladores, rutas, permisos, autenticación, cálculos, endpoints ni base de datos. Sin nuevas dependencias de ejecución.

La adaptación compartida está en `public/css/acotec-mobile.css` y `public/js/acotec.js`. Por debajo de 768 px los listados se presentan como registros con etiquetas, conservando los mismos elementos y manejadores. DataTables conserva búsqueda, paginación y ordenamiento mediante un selector móvil. En escritorio se mantiene la tabla original. No se duplican filas ni formularios.

## Inventario recorrido

Cada pantalla del menú se abrió autenticada a 390 × 844, comprobando carga de registros, ancho del documento y apertura/cierre de «Nuevo» cuando existe. Esto es una prueba de interfaz, no una certificación de todos los procesos del módulo.

| Grupo | Pantallas |
| --- | --- |
| Inicio | Vista general |
| Almacén | Unidades, Productos, Proveedores, Compras, Salidas de Almacén |
| Ventas | Cotizaciones, Ventas, Notas de Crédito |
| Comercial | Clientes, Sedes, Solicitantes, Servicios, Transporte Personal, Transporte Herramientas, Condiciones |
| Operaciones | Órdenes de Trabajo, Órdenes de Servicio, Informes, Técnicos |
| Reportes | Ventas, Compras, Pagos, Órdenes |
| Configuración | Tipos de Comprobante, Tipos de Documento, Tipos de Afectación, Correlativos, Bancos |
| Seguridad | Roles, Usuarios |

Pantallas adicionales identificadas: Perfil, Mano de Obra (sin entrada en el menú actual), órdenes por estado y cronología de órdenes. Departamentos, Provincias y Distritos tienen rutas con índices vacíos en los controladores; no se inventaron interfaces ni funcionalidades.

## Cambios aplicados

- Navegación inferior móvil restaurada; menú lateral y logo completo al abrirlo.
- Dashboard en dos columnas, sin carrusel horizontal.
- Filas convertidas en tarjetas etiquetadas; acciones táctiles de al menos 44 px.
- Campos de 16 px en móvil para evitar zoom automático al escribir.
- Formularios largos con desplazamiento interno y pie visible; campos a una columna.
- Reportes desplegables respetan el estado cerrado y las tablas anidadas ajustan su ancho.
- Etiquetas accesibles para acciones con iconos y conservación de estilos de foco existentes.
- Recursos CSS/JS versionados por modificación para evitar versiones antiguas en caché.
- Corrección de referencias de foco a campos inexistentes y del resaltado de entradas ausentes del menú. Ventas ya no intenta limpiar un elemento `ot_codigo` inexistente; se conservó su comportamiento cuando el campo está presente.

## Evidencia de pruebas

- 31 pantallas del menú: HTTP 200, registros cargados, ancho del documento 390 px en la pasada móvil.
- Ventas repetida tras corregir la referencia inválida: apertura/cierre sin errores JavaScript.
- Productos: búsqueda de un código existente, orden descendente desde el selector móvil y apertura de edición con datos, sin guardar. Probado a 320, 390, 768 y 1366 px.
- Dashboard, Productos, Cotizaciones y Reporte de Pagos: documento sin desbordamiento a esos cuatro anchos.
- Detalle de pagos abierto/cerrado: sin desbordamiento en los cuatro anchos; cerrado permanece oculto.
- Menú «Más» abre el lateral; navegación inferior visible en móvil y oculta en escritorio.
- Recorridas las 13 listas de órdenes por estado: carga correcta; se ajustó también la paginación de la versión anterior de DataTables utilizada en estas vistas.
- `node --check public/js/acotec.js` y `php artisan view:cache` correctos.

## Validación pendiente

### Segunda entrega: formularios secundarios

- Revisados 15 modales presentes en Órdenes de Trabajo, Órdenes de Servicio, Ventas, Cotizaciones, Compras, Salidas, Notas de Crédito e Informes, incluyendo documentación, facturación, proceso de pago, registro de pago, nota de crédito y conformidad. Apertura para comprobar presentación, sin enviar los formularios.
- Corregido el cierre de `.modal-body` en Órdenes de Servicio: el pie quedaba dentro del contenido desplazable. Ahora «Enviar» y «Cancelar» permanecen visibles.
- El diálogo de conformidad de Órdenes de Servicio se incluye dentro de la sección de contenido, evitando emitir HTML antes de la plantilla principal. Se conserva su condición de acceso existente.
- Formularios secundarios con ancho completo, títulos adaptables, cabeceras internas más compactas y sin sombras superpuestas.
- Etiquetas asociadas a controles adyacentes, nombre accesible en botones de cierre y títulos asociados a los diálogos. Los campos de las filas dinámicas reciben su etiqueta de columna cuando no tienen otra.
- Selección real de un material desde el buscador en Ventas, Cotizaciones, Compras y Salidas: fila agregada solamente en el navegador, sin desbordamiento a 320 px. No se envió ni guardó ninguna operación.
- Apertura desde los botones existentes de «Ejecutar OT» y «Subir documentación OT»: datos de la orden cargados y cierre correcto, sin ejecutar la acción final.
- Cronología real de una orden con tres eventos: HTTP 200, disposición vertical a 320 px y sin desbordamiento. Se mantienen los eventos y su orden original.
- Nueva prueba `tests/browser/mobile-secondary.mjs`: verifica ancho, visibilidad del pie, etiquetas de los diálogos, modo estándar HTML y pertenencia de los controles al formulario a 320 × 740 y 568 × 360. Bloquea peticiones de escritura después del login.

### Límites que siguen pendientes

No se ejecutaron altas, guardados, eliminaciones, importaciones, pagos, cambios de estado, exportaciones ni cambios de permisos sobre los datos reales. Sigue pendiente validar el contenido y la disponibilidad de todos los adjuntos/PDF, los flujos completos con cada rol y pruebas en dispositivos físicos iOS/Android o con lector de pantalla. Abrir un modal directamente comprueba su presentación, no la autorización o el proceso de negocio que lo utiliza.

## Prueba repetible

`tests/browser/mobile-smoke.mjs` recorre el menú y abre/cierra formularios sin enviarlos. Requiere Playwright de pruebas (puede estar instalado fuera del proyecto), navegador Chromium y servidor iniciado. Variables: `UI_BASE_URL`, `UI_EMAIL`, `UI_PASSWORD`; opcionalmente `PLAYWRIGHT_MODULE` con la ruta absoluta al módulo instalado. No almacenar credenciales en este documento ni en el script.
