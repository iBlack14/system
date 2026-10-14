class CotizacionesCrud extends CrudManager {

    constructor() {
        super('/cotizaciones');
    }

    resetDetallesModal() {
        const config = [
            { chk: 'chkServicio', tabla: 'tablaDetallesServicios', wrapper: 'seccionServicios' },
            { chk: 'chkManoObra', tabla: 'tablaDetallesManoObra', wrapper: 'seccionManoObra' },
            { chk: 'chkTransporte', tabla: 'tablaDetallesTransportes', wrapper: 'seccionTransporte' },
            { chk: 'chkMateriales', tabla: 'tablaDetallesMateriales', wrapper: 'seccionMateriales' }
        ];

        config.forEach(item => {
            const checkbox = document.getElementById(item.chk);
            const tabla = document.getElementById(item.tabla);
            const wrapper = document.getElementById(item.wrapper);

            if (checkbox) checkbox.checked = false;

            if (tabla) {
                const tbody = tabla.querySelector('tbody');
                if (tbody) tbody.innerHTML = '';
            }

            if (wrapper) wrapper.style.display = 'none';
        });

        const total = document.getElementById('total_general');
        if (total) total.textContent = 'S/ 0.00';
    }

    resetForm() {
        super.resetForm();          // limpia inputs base
        this.resetDetallesModal();  // limpia tablas + checkboxes
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.cotizacionesCrud = new CotizacionesCrud();
});
