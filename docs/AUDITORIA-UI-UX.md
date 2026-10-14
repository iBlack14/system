# Auditoría UI/UX de Acotec

> Documento de la revisión inicial. La paleta, los datos disponibles y la cobertura de pruebas evolucionaron después de esta auditoría. Para el estado de la adaptación móvil actual, consultar [Validación móvil](VALIDACION-MOVIL.md).

## Alcance y evidencia

Revisión estructural del inventario de 122 vistas Blade, plantillas compartidas, autenticación, navegación, dashboards, componentes de acciones e integración Bootstrap/AdminLTE/DataTables. No equivale a una validación funcional de los 122 archivos: muchos son parciales o documentos de impresión. Verificación visual realizada con Chromium en escritorio (1440 × 1000) y móvil (390 × 844).

## Hallazgos y respuesta implementada

| Área | Hallazgo | Cambio |
| --- | --- | --- |
| Identidad | Marca genérica “Sistema” y logo de AdminLTE | Identidad tipográfica Acotec y símbolo visual propio |
| Navegación | Cabecera con siete accesos sin comprobación de permisos; menú inicial contraído | Cabecera simplificada, menú expandido en escritorio y accesos del inicio filtrados por permisos |
| Orientación | Estado activo dependiente de scripts particulares | Resaltado de ruta actual y apertura del grupo correspondiente |
| Login | Composición genérica, etiquetas en inglés | Diseño de dos columnas, español, autocompletado y control accesible de contraseña |
| Bienvenida | Tarjeta vacía de orientación | Introducción, accesos reales por permisos y enlace al perfil |
| Tablas | Densidad elevada y jerarquía débil | Cabeceras neutras, espaciado, estados de fila, controles y paginación coherentes |
| Formularios | Controles visualmente heterogéneos | Estilo común, foco visible y tamaños consistentes |
| Modales | Presentación genérica | Superficies, cabecera y pie coherentes con el sistema visual |
| Accesibilidad | Idioma HTML incorrecto y botones sin nombre accesible | HTML en español, enlace para saltar al contenido, etiquetas en acciones y movimiento reducido |
| Adaptación | Cabecera saturada en tamaños intermedios | Cabecera compacta, tarjetas adaptables y estilos móviles |

## Sistema visual

- Fondo: #f5f7fa. Superficies: blanco. Texto: #192c3e.
- Acción principal: #156b65. Navegación: #142838.
- Bordes: #e1e7ed. Radios: 8 px en controles, 14–16 px en superficies.
- Tipografía del sistema, jerarquía por tamaño/peso y etiquetas auxiliares.
- Estados semánticos de peligro, advertencia y éxito conservados.
- Implementación: public/css/acotec.css y public/js/acotec.js, cargados después de los estilos existentes.
- Bootstrap, AdminLTE, DataTables, rutas, selectores CRUD y permisos siguen siendo las bases existentes.
- Tickets y PDFs independientes conservan sus formatos; requieren una revisión específica de impresión antes de rediseñarlos.

## Validación ejecutada

- Compilación de todas las plantillas: php8.4 artisan view:cache.
- Build frontend: npm run build.
- Sintaxis JavaScript: node --check public/js/acotec.js.
- Autenticación real con el administrador local y redirección al dashboard.
- Mostrar contraseña cambia correctamente el tipo de campo.
- Clientes, productos, unidades, ventas y compras responden HTTP 200.
- Apertura real del modal de clientes.
- Sin errores JavaScript de página capturados en ese recorrido.
- Sin desbordamiento horizontal en las páginas de escritorio comprobadas y el dashboard móvil.
- Capturas revisadas de login, dashboard y dashboard móvil.

## Problemas previos y límites

1. Las migraciones de cotizaciones están desordenadas y existen creaciones duplicadas de tablas. La base local no contiene todo el esquema del sistema.
2. El administrador de prueba no tiene todos los permisos operativos: no se han ampliado sus privilegios para hacer capturas.
3. El dashboard con indicadores depende del catálogo de estados y órdenes; se mantuvieron sus datos reales y no se inventaron cifras.
4. Existen estilos en línea en vistas especializadas. La capa común mejora su presentación, pero no sustituye una revisión funcional de cada flujo con datos completos.
5. El módulo de usuarios está configurado en /usuarios; /users no es una ruta del sistema.
6. No se han probado operaciones de escritura, cálculos fiscales ni generación de documentos durante la revisión visual.

## Próximos pasos recomendados

Completar el esquema y datos locales, revisar cada flujo operativo con sus roles y realizar validación de impresión y accesibilidad completa. Estos puntos no se presentan como resueltos por un cambio de estilos.
