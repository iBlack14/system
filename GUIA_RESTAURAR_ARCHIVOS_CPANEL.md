# Restauración de archivos en cPanel

Este paquete contiene el sistema actualizado, pero no contiene fotografías, PDFs, comprobantes ni otros documentos cargados por los usuarios. La base de datos tampoco está incluida.

## 1. Base de datos

Importa tu copia reciente de MySQL desde **cPanel > phpMyAdmin > Importar**. Haz antes una copia de seguridad de la base que exista en producción.

La base de datos conserva los nombres y rutas de los archivos. Por eso debes copiar los archivos antiguos sin cambiar sus nombres ni la estructura de carpetas.

## 2. Carpetas que debes copiar desde el respaldo

### Archivos públicos

Copia y combina el contenido de estas carpetas del respaldo:

```text
public/uploads/productos/
public/uploads/ordenes_servicio/
public/uploads/ordenes_trabajo/
public/uploads/ot_documentacion/
```

Si la carpeta `public` de Laravel está publicada como `public_html`, sus destinos serán:

```text
public_html/uploads/productos/
public_html/uploads/ordenes_servicio/
public_html/uploads/ordenes_trabajo/
public_html/uploads/ot_documentacion/
```

### Informes y notas de crédito

Copia y combina:

```text
storage/app/public/informes/
storage/app/public/notas_credito/
```

Las fotografías de informes conservan una estructura similar a:

```text
storage/app/public/informes/25/aspectos/3/archivo.jpg
```

No cambies los números de informe, aspecto ni los nombres de archivo.

## 3. Acceso público a `storage`

Laravel normalmente utiliza un enlace desde `public/storage` hacia `storage/app/public`. Si tu cPanel no permite crear ese enlace y no tienes terminal, copia también el contenido de:

```text
storage/app/public/
```

dentro de:

```text
public/storage/
```

o, cuando `public` sea `public_html`:

```text
public_html/storage/
```

Como mínimo deben terminar disponibles estas carpetas:

```text
public_html/storage/informes/
public_html/storage/notas_credito/
```

## 4. Forma segura de restaurar

1. Sube y extrae el ZIP del sistema.
2. Configura el archivo `.env` para el dominio y la base de datos de producción.
3. Importa la copia reciente de MySQL.
4. Sube las carpetas de archivos indicadas anteriormente.
5. Usa **Combinar** al extraer las carpetas; no elimines archivos nuevos.
6. Comprueba un producto con imagen, un informe con fotografías, una orden de servicio y una nota de crédito.

## 5. Contenido excluido del ZIP

El paquete no incluye:

- Fotografías de informes.
- Imágenes de productos.
- PDFs de órdenes de servicio o trabajo.
- Facturas, sustentos, vouchers y documentación cargada.
- Notas de crédito y sus documentos.
- La copia de la base de datos.
- `.git`, `node_modules`, pruebas y archivos temporales.

Las plantillas que generan los PDFs sí están incluidas porque forman parte del código del sistema.
