/* Progressive enhancement; existing CRUD selectors and handlers stay intact. */
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.password-toggle');
    toggle?.addEventListener('click', () => {
        const input = document.getElementById('loginPassword');
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(visible));
        toggle.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
        toggle.querySelector('i').className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
    const path = location.pathname.replace(/\/$/, '');
    let accessibleId = 0;
    const newAccessibleId = () => {
        let id;
        do { id = 'acotec-field-' + ++accessibleId; } while (document.getElementById(id));
        return id;
    };
    const prepareForms = () => {
        document.querySelectorAll('.app-content label:not([for]), .acotec-app .modal label:not([for])').forEach(label => {
            // Only associate unambiguous adjacent controls; do not guess grouped fields.
            const field = label.nextElementSibling;
            if (!field?.matches('input:not([type="hidden"]), select, textarea')) return;
            if (!field.id) field.id = newAccessibleId();
            label.htmlFor = field.id;
        });
        document.querySelectorAll('.acotec-app .modal').forEach(modal => {
            const title = modal.querySelector('.modal-title');
            if (title && !document.getElementById(modal.getAttribute('aria-labelledby'))) {
                if (!title.id) title.id = newAccessibleId();
                modal.setAttribute('aria-labelledby', title.id);
            }
            modal.querySelectorAll('.btn-close').forEach(button => {
                if (!button.getAttribute('aria-label') || button.getAttribute('aria-label') === 'Close') {
                    button.setAttribute('aria-label', 'Cerrar');
                }
            });
        });
    };
    const tableControls = new WeakMap();
    // Label existing cells, keeping the actual buttons, inputs and delegated handlers.
    const prepareTable = table => {
        const header = table.tHead?.rows[0];
        if (!header) return;
        const visibleHeaderCells = [...header.cells].filter(cell => {
            return cell.style.display !== 'none' && (!cell.hasAttribute('style') || !cell.style.display.includes('none'));
        });
        const labels = visibleHeaderCells.map(cell => cell.textContent.trim());
        table.classList.add('mobile-records');
        for (const body of table.tBodies) {
            for (const row of body.rows) {
                if (row.cells.length === 1 && (row.cells[0].classList.contains('dataTables_empty') || row.cells[0].colSpan > 1)) continue;
                [...row.cells].forEach((cell, index) => {
                    const label = cell.dataset.mobileLabel || labels[index] || '';
                    if (label) {
                        cell.dataset.mobileLabel = label;
                    }
                    cell.classList.toggle('mobile-record-actions', /^(opciones|acciones)$/i.test(label));
                    cell.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(field => {
                        if (label && !field.labels?.length && !field.hasAttribute('aria-label')) {
                            field.setAttribute('aria-label', label);
                        }
                    });
                });
            }
        }
        let controls = tableControls.get(table);
        if (!controls) {
            controls = document.createElement('div');
            controls.className = 'mobile-record-controls';
            tableControls.set(table, controls);
        }
        if (controls.parentElement !== table.parentElement) {
            table.before(controls);
        }
        if (controls.childElementCount) return;
        controls.className = 'mobile-record-controls';
        const select = document.createElement('select');
        select.className = 'form-select';
        select.setAttribute('aria-label', 'Ordenar registros');
        const placeholder = new Option('Ordenar registros…', '');
        select.add(placeholder);
        const api = window.jQuery?.fn.dataTable?.isDataTable(table)
            ? window.jQuery(table).DataTable() : null;
        if (labels.length >= 8) {
            table.classList.add('wide-data-table');
            if (window.innerWidth >= 768) {
                table.style.minWidth = Math.max(1080, labels.length * 135) + 'px';
            }
            if (api?.responsive && table.dataset.completeColumns !== 'true') {
                api.responsive.disable();
                api.columns().visible(true, false);
                api.columns.adjust();
                table.dataset.completeColumns = 'true';
            }
        }
        if (api) {
            const settings = api.settings()[0];
            labels.forEach((label, index) => {
                if (!settings.aoColumns[index].bSortable) return;
                select.add(new Option(label + ' ↑', index + ':asc'));
                select.add(new Option(label + ' ↓', index + ':desc'));
            });
            select.addEventListener('change', () => {
                if (!select.value) return;
                const [index, direction] = select.value.split(':');
                api.order([Number(index), direction]).draw();
            });
            controls.append(select);
        }
    };
    const refreshTables = () => {
        prepareForms();
        document.querySelectorAll('.app-content table.table').forEach(prepareTable);
        const actionLabels = {
            'bi-pencil-square': 'Editar', 'bi-pencil': 'Editar', 'bi-trash': 'Eliminar',
            'bi-eye': 'Ver detalle', 'bi-funnel-fill': 'Aplicar filtros',
            'bi-arrow-clockwise': 'Restablecer filtros', 'bi-file-earmark-excel': 'Exportar a Excel'
        };
        document.querySelectorAll('.app-content button, .app-content a.btn').forEach(control => {
            if (control.textContent.trim() || control.hasAttribute('aria-label')) return;
            const icon = control.querySelector('.bi');
            const label = control.getAttribute('title') || Object.entries(actionLabels)
                .find(([className]) => icon?.classList.contains(className))?.[1];
            if (label) control.setAttribute('aria-label', label);
        });
    };
    refreshTables();

    // DataTables dispara estos eventos antes de terminar de liberar su estado
    // interno de dibujo. Modificar columnas dentro del mismo callback puede dejar
    // activo indefinidamente el indicador "Procesando...". Ejecutamos la mejora
    // móvil en el siguiente frame, una vez finalizado el dibujo.
    let tableRefreshPending = false;
    const scheduleTableRefresh = () => {
        if (tableRefreshPending) return;
        tableRefreshPending = true;
        requestAnimationFrame(() => {
            tableRefreshPending = false;
            try {
                refreshTables();
            } catch (error) {
                console.error('No se pudo adaptar la tabla:', error);
            }
        });
    };
    window.jQuery?.(document).on('draw.dt init.dt', scheduleTableRefresh);
    // Form detail rows are added dynamically without DataTables.
    const content = document.querySelector('.app-content');
    if (content) {
        let pending = false;
        new MutationObserver(() => {
            if (pending) return;
            pending = true;
            requestAnimationFrame(() => { pending = false; refreshTables(); });
        }).observe(content, {childList: true, subtree: true});
    }
    document.querySelectorAll('.sidebar-menu a[href], .mobile-bottom-nav a[href]').forEach(link => {
        const url = new URL(link.href, location.origin);
        if (url.hash || link.getAttribute('href') === '#' || url.origin !== location.origin) return;
        const target = url.pathname.replace(/\/$/, '');
        if (target && (path === target || path.startsWith(target + '/'))) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
            let parent = link.closest('.nav-treeview');
            while (parent) {
                parent.parentElement.classList.add('menu-open');
                parent = parent.parentElement.parentElement.closest('.nav-treeview');
            }
        }
    });
});
