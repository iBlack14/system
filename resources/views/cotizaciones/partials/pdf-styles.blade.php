<style>
    @page { 
        margin: 22px 28px 26px; 
    }
    body { 
        font-family: 'Plus Jakarta Sans', 'Inter', DejaVu Sans, Helvetica, Arial, sans-serif; 
        color: #1e293b; 
        font-size: 9px; 
        line-height: 1.45; 
    }
    p { margin: 1px 0; }
    
    /* Header layout */
    .header-table { 
        width: 100%; 
        margin: 0 0 10px; 
        border-collapse: collapse;
    }
    .header-table td { 
        padding: 0; 
        vertical-align: top; 
        border: 0;
        background: #ffffff !important;
    }
    .header-left { width: 58%; }
    .header-right { width: 42%; text-align: right; }
    
    .logo { 
        width: 170px; 
        max-height: 46px; 
        object-fit: contain; 
        margin-bottom: 5px; 
    }
    .company-name { 
        font-size: 9.5px; 
        font-weight: bold; 
        color: #111827; 
        letter-spacing: .2px;
        margin-bottom: 2px; 
    }
    .company-meta { 
        color: #4b5563; 
        font-size: 7.8px; 
        line-height: 1.35; 
    }
    .company-meta strong { color: #111827; }

    /* Cuadro Oficial RUC / Cotización */
    .doc-box { 
        width: 100%; 
        border: 1px solid #1e293b; 
        border-collapse: collapse; 
        margin-left: auto; 
        max-width: 220px; 
    }
    .doc-box-ruc {
        background: #ffffff;
        text-align: center;
        font-size: 8.8px;
        font-weight: bold;
        color: #111827;
        padding: 4px 6px;
        border-bottom: 1px solid #1e293b;
        letter-spacing: .4px;
    }
    .doc-box-title { 
        background: #1e293b; 
        color: #ffffff; 
        text-align: center; 
        font-size: 9px; 
        font-weight: bold; 
        padding: 4px 6px; 
        letter-spacing: .6px; 
    }
    .doc-box-body { 
        background: #ffffff; 
        text-align: center; 
        padding: 5px 6px; 
    }
    .doc-number { 
        font-size: 12px; 
        font-weight: bold; 
        color: #111827; 
        margin-bottom: 2px; 
    }
    .doc-date { 
        font-size: 8px; 
        color: #4b5563; 
        font-weight: 600; 
    }

    .header-divider {
        height: 1px;
        background: #cbd5e1;
        margin: 8px 0 10px;
    }

    /* Datos del cliente */
    .client-section {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-collapse: collapse;
        margin-bottom: 10px;
        page-break-inside: avoid;
    }
    .client-section td {
        padding: 4px 7px;
        font-size: 8.5px;
        vertical-align: top;
        border: 1px solid #e2e8f0;
    }
    .client-label {
        width: 85px;
        font-weight: bold;
        color: #374151;
        font-size: 7.8px;
        text-transform: uppercase;
    }
    .client-val {
        color: #111827;
    }

    /* Secciones y tablas */
    .section-header {
        font-size: 8.5px;
        font-weight: bold;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: .3px;
        padding: 2px 0;
        margin-top: 9px;
        margin-bottom: 4px;
        border-bottom: 1px solid #94a3b8;
        page-break-after: avoid;
    }

    .bloque { 
        margin-bottom: 8px; 
    }

    table.data-table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-bottom: 4px; 
        page-break-inside: auto; 
    }
    table.data-table th {
        color: #1e293b;
        background: #f1f5f9;
        font-size: 7.6px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .2px;
        padding: 4px 6px;
        border: 1px solid #cbd5e1;
        text-align: left;
    }
    table.data-table td { 
        padding: 3.5px 6px; 
        border: 1px solid #e2e8f0; 
        font-size: 8.3px; 
        color: #1f2937;
    }
    table.data-table tr { 
        page-break-inside: avoid; 
    }

    .text-right { text-align: right; white-space: nowrap; }
    .text-center { text-align: center; }

    .subtotal-row td, .total td, tr.total td { 
        color: #1e293b; 
        background: #f8fafc !important; 
        font-weight: bold; 
        border-top: 1px solid #cbd5e1; 
    }

    /* Transporte */
    .transporte-bloque { 
        page-break-inside: avoid; 
        margin-bottom: 8px; 
    }

    /* Resumen fiscal / Totales */
    .resumen-fiscal-bloque { 
        page-break-inside: avoid; 
        margin-top: 6px; 
    }
    .financial-table { 
        width: 44%; 
        margin-left: auto; 
        border-collapse: collapse; 
        margin-bottom: 10px;
    }
    .financial-table td { 
        padding: 3px 6px; 
        font-size: 8.3px; 
        border-bottom: 1px solid #e2e8f0; 
    }
    .financial-table .total-row td {
        font-size: 10.5px;
        font-weight: bold;
        color: #0f172a;
        border-top: 1px solid #0f172a;
        border-bottom: 2px solid #0f172a;
        padding: 5px 6px;
        background: #f8fafc;
    }

    /* Condiciones comerciales */
    .terms-box {
        margin-top: 8px;
        border: 1px solid #cbd5e1;
        border-collapse: collapse;
        width: 100%;
        page-break-inside: avoid;
    }
    .terms-header {
        background: #f1f5f9;
        font-size: 8px;
        font-weight: bold;
        color: #1e293b;
        text-transform: uppercase;
        padding: 3px 6px;
        border-bottom: 1px solid #cbd5e1;
    }
    .terms-body {
        padding: 5px 6px;
        background: #ffffff;
        font-size: 7.8px;
        color: #374151;
        line-height: 1.4;
    }
    .terms-body p {
        margin: 1.5px 0;
    }

    /* Footer */
    .footer { 
        margin-top: 16px; 
        padding-top: 6px; 
        border-top: 1px solid #cbd5e1; 
        font-size: 7.5px; 
        color: #64748b; 
        text-align: center; 
        page-break-inside: avoid; 
    }
</style>
