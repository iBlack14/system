/**
 * Gestor CRUD Base - Funcionalidades comunes para DataTables y Bootstrap Modal
 * @version 2.0
 * @author incanatoapps
 */
class CrudManager {
    /**
     * Constructor base del gestor CRUD
     * @param {string} baseUrl - URL base para las operaciones CRUD
     */
    constructor(baseUrl) {
        this.baseUrl = baseUrl;
        this.tabla = null;
        this.modal = null;
        this.form = null;
        this.isEditing = false;
        this.elements = {};
        
        this.cacheElements();
        this.bindEvents();
    }

   

    /**
     * Cachea elementos del DOM comunes
     * @private
     */
    cacheElements() {
        this.elements = {
            modal: document.getElementById('modalUpdate'),
            form: document.getElementById('formUpdate'),
            btnCreate: document.getElementById('btnCreate'),
            btnSubmit: document.getElementById('btnSubmit'),
            btnCancel: document.getElementById('btnCancelarModal'),
            modalTitle: document.getElementById('modalTitle'),
            methodField: document.getElementById('method_field'),
            table: document.getElementById('listadoTable')
        };
        if (this.elements.modal) {
            this.modal = new bootstrap.Modal(this.elements.modal);
        }
        if (this.elements.form) {
            this.form = this.elements.form;
        }
        if (this.elements.table) {
            this.elements.table.classList.add('mobile-records');
        }
    }

    /**
     * Vincula eventos comunes del DOM
     * @private
     */
    bindEvents() {
        if (this.elements.btnCreate) {
            this.elements.btnCreate.addEventListener('click', () => this.showCreateModal());
        }
        if (this.elements.form) {
            this.elements.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        
        // Delegación de eventos para botones dinámicos
        this.elements.table.addEventListener('click', (e) => {
            const target = e.target.closest('[data-action]');
            if (!target) return;
            
            const action = target.dataset.action;
            const id = target.dataset.id;
            
            if (action === 'edit') {
                this.showEditModal(id);
            } else if (action === 'delete') {
                this.confirmDelete(id);
            }
        });

        // Eventos del modal
        if (this.elements.modal) {
            this.elements.modal.addEventListener('hide.bs.modal', () => {
                const focusedElement = this.elements.modal.querySelector(':focus');
                if (focusedElement) focusedElement.blur();
            });

            this.elements.modal.addEventListener('hidden.bs.modal', () => {
                this.resetForm();
                this.isEditing = false;
            });
        }
        // Botón Cancelar del modal
        if (this.elements.btnCancel) {
            this.elements.btnCancel.addEventListener('click', () => {
                if (!this.isEditing) {
                    this.resetForm(); // limpia solo si NO está editando
                                    }
            });
        }
    }

    /**
     * Muestra modal para crear nuevo registro
     * @public
     */
    showCreateModal() {
        this.isEditing = false;
        this.resetForm();
        
        this.elements.modalTitle.textContent = 'Nuevo registro';
        this.elements.methodField.value = '';
        this.form.action = this.baseUrl;
        
        this.modal.show();
        setTimeout(() => this.focusFirstField(), 150);
    }

    /**
     * Maneja el envío del formulario
     * @param {Event} e - Evento del formulario
     * @private
     */
    async handleSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(this.form);
        this.setSubmitButtonState(true);
        this.clearFormErrors();
        
        try {
            const response = await this.submitForm(formData);
            
            const focusedElement = this.elements.modal.querySelector(':focus');
            if (focusedElement) focusedElement.blur();
            
            this.modal.hide();
            this.tabla.ajax.reload(null, false);
            
            const isSuccess = response.success === true || response.status === true;
            if (isSuccess) {
                const message = this.isEditing 
                    ? (response.message || 'Registro actualizado correctamente')
                    : (response.message || 'Registro creado correctamente');
                this.showNotification('success', message);

                if (this.afterSuccess) {
                    await this.afterSuccess(response, this.isEditing);
                }

            } else {
                this.showNotification('error', response.message || 'Error en la operación');
            }
            
        } catch (error) {
            console.error('Error en submit:', error);
            this.handleFormErrors(error);
        } finally {
            this.setSubmitButtonState(false);
        }
    }

    /**
     * Envía formulario via fetch API
     * @param {FormData} formData - Datos del formulario
     * @returns {Promise<Object>} Respuesta del servidor
     * @private
     */
    async submitForm(formData) {
        const response = await fetch(this.form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw { status: response.status, data: errorData };
        }

        return await response.json();
    }

    /**
     * Obtiene datos del servidor
     * @param {string} url - URL del endpoint
     * @returns {Promise<Object>} Datos del servidor
     * @private
     */
    async fetchData(url) {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);            
        }

        return await response.json();
    }

    /**
     * Confirma eliminación con SweetAlert
     * @param {string|number} id - ID del registro a eliminar
     * @public
     */
    confirmDelete(id) {
        Swal.fire({
            title: '¿Confirmar eliminación?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.deleteRecord(id);
            }
        });
    }

    /**
     * Elimina registro del servidor
     * @param {string|number} id - ID del registro a eliminar
     * @private
     */
    async deleteRecord(id) {
        try {
            const response = await fetch(`${this.baseUrl}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            this.tabla.ajax.reload(null, false);
            this.showNotification('success', data.message || 'Registro eliminado correctamente');
            
        } catch (error) {
            this.showNotification('error', 'Error al eliminar el registro');
            console.error('Error:', error);
        }
    }

    /**
     * Reinicia el formulario a su estado inicial
     * @private
     */
    resetForm() {
        this.form.reset();
        this.clearFormErrors();
        this.elements.methodField.value = '';        
    }

    /**
     * Limpia errores de validación del formulario
     * @private
     */
    clearFormErrors() {
        const invalidElements = this.form.querySelectorAll('.is-invalid');
        invalidElements.forEach(el => el.classList.remove('is-invalid'));
        
        const feedbacks = this.form.querySelectorAll('.invalid-feedback');
        feedbacks.forEach(fb => fb.textContent = '');
    }

    /**
     * Maneja errores de validación del servidor
     * @param {Object} error - Objeto de error con status y data
     * @private
     */
    handleFormErrors(error) {
        if (error.status === 422 && error.data.errors) {
            Object.entries(error.data.errors).forEach(([field, messages]) => {
                let input = document.getElementById(field);
                let feedback = input?.nextElementSibling;

                // Solo buscar alternativa si no tiene feedback válido
                if (!input || !feedback?.classList.contains('invalid-feedback')) {
                    input = document.querySelector(`[data-error-field="${field}"]`);
                    feedback = input?.nextElementSibling;
                }
                
                if (input && feedback && feedback.classList.contains('invalid-feedback')) {
                    input.classList.add('is-invalid');
                    feedback.textContent = messages[0];
                }
            });
        } else {
            const message = error.data?.message || 'Error al procesar la solicitud';
            this.showNotification('error', message);
        }
    }

    /**
     * Controla el estado del botón de envío
     * @param {boolean} loading - Si está en estado de carga
     * @private
     */
    setSubmitButtonState(loading) {
        this.elements.btnSubmit.disabled = loading;
        
        if (loading) {
            this.elements.btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
        } else {
            this.elements.btnSubmit.innerHTML = '<i class="bi bi-check-circle"></i> Guardar';
        }
    }

    /**
     * Muestra notificación toast con SweetAlert
     * @param {string} type - Tipo de notificación (success, error, warning, info)
     * @param {string} message - Mensaje a mostrar
     * @public
     */
    showNotification(type, message) {
        const icons = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        Swal.fire({
            icon: icons[type] || 'info',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    /**
     * Enfoca el primer campo del formulario (debe ser sobrescrito)
     * @protected
     */
    focusFirstField() {
        // Implementar en la clase hija
    }

    /**
     * Muestra modal de edición (debe ser sobrescrito)
     * @param {string|number} id - ID del registro
     * @protected
     */
    async showEditModal(id) {
        // Implementar en la clase hija
    }

    /**
     * Configura un input con búsqueda en vivo para seleccionar valores desde una API.
     * @param {Object} options - Configuración del buscador.
     * @param {string} options.inputId - ID del input visible.
     * @param {string} options.hiddenId - ID del input oculto donde se guarda el ID real.
     * @param {string} options.url - URL para realizar la búsqueda (se agrega ?q=texto).
     * @param {function} [options.template] - Función para renderizar cada resultado (por defecto muestra `item.nombre`).
     */
    setupLiveSearchSelect({
        inputId,
        hiddenId,
        url,
        template = item => item.nombre || item.descripcion || '',
        getId = item => item.id || item.codigo || '',
        minLength = 3,
        delay = 300,
        onSelect
    }) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);

        if (!input || !hidden) return;

        let timeout = null;
        let activeIndex = -1;
        let suggestions = [];

        const clearSuggestions = () => {
            const oldList = input.parentNode.querySelector('ul.search-list');
            if (oldList) oldList.remove();
            activeIndex = -1;
            suggestions = [];
        };

        const renderSuggestions = (data) => {
            clearSuggestions();

            if (!data.length) return;

            const list = document.createElement('ul');
            list.className = 'list-group position-absolute w-100 search-list shadow-lg';
            list.style.zIndex = '1065';
            list.style.top = '100%';
            list.style.maxHeight = '240px';
            list.style.overflowY = 'auto';
            list.style.backgroundColor = '#ffffff';
            list.style.border = '1px solid #cbd5e1';
            list.style.borderRadius = '0.375rem';
            list.style.boxShadow = '0 10px 25px -5px rgba(0,0,0,0.2)';

            suggestions = data;

            data.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'list-group-item list-group-item-action text-dark fw-medium py-2 px-3 border-bottom';
                li.style.cursor = 'pointer';
                li.style.color = '#0f172a';
                li.style.backgroundColor = '#ffffff';
                li.style.fontSize = '0.875rem';
                li.textContent = template(item);
                li.dataset.index = index;

                li.onmouseover = () => {
                    li.style.backgroundColor = '#f1f5f9';
                    li.style.color = '#1d4ed8';
                };
                li.onmouseout = () => {
                    if (activeIndex !== index) {
                        li.style.backgroundColor = '#ffffff';
                        li.style.color = '#0f172a';
                    }
                };

                li.onclick = () => {
                    input.value = template(item);
                    hidden.value = getId(item);
                    clearSuggestions();
                    if (typeof onSelect === 'function') {
                        onSelect(item);
                    }
                };

                list.appendChild(li);
            });

            input.parentNode.style.position = 'relative';
            input.parentNode.appendChild(list);
        };

        const fetchSuggestions = (query) => {
            fetch(url + '?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => renderSuggestions(data))
                .catch(error => console.error('Error:', error));
        };

        input.addEventListener('input', () => {
            const query = input.value.trim();

            if (query.length < minLength) {
                hidden.value = '';
                clearSuggestions();
                return;
            }

            clearTimeout(timeout);
            timeout = setTimeout(() => fetchSuggestions(query), delay);
        });

        input.addEventListener('keydown', (e) => {
            const list = input.parentNode.querySelector('ul.search-list');
            if (!list) return;

            const items = list.querySelectorAll('li');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + items.length) % items.length;
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && suggestions[activeIndex]) {
                    const selectedItem = suggestions[activeIndex];
                    input.value = template(selectedItem);
                    hidden.value = getId(selectedItem);
                    clearSuggestions();

                    if (typeof onSelect === 'function') {
                        onSelect(selectedItem); // ✅ Ejecuta onSelect al presionar Enter
                    }
                }
            } else if (e.key === 'Escape') {
                clearSuggestions();
            }

            items.forEach((item, i) => {
                const isActive = (i === activeIndex);
                item.classList.toggle('active', isActive);
                if (isActive) {
                    item.style.backgroundColor = '#e0e7ff';
                    item.style.color = '#1d4ed8';
                } else {
                    item.style.backgroundColor = '#ffffff';
                    item.style.color = '#0f172a';
                }
            });
        });

        input.addEventListener('blur', () => {
            setTimeout(() => clearSuggestions(), 250);
        });
    }

    /**
     * Pobla un select con datos de una API
     * @param {string} selectId - ID del elemento select
     * @param {string} url - URL para obtener los datos
     * @param {function} [getOption] - Función para generar cada <option>
     * @param {object} [options] - Opciones extra como placeholder
     */
    populateSelect(selectId, url, getOption = item => `<option value="${item.id}">${item.nombre}</option>`, options = {}) {
        const select = document.getElementById(selectId);
        if (!select) {
            console.warn(`No se encontró el select con id "${selectId}"`);
            return;
        }

        const placeholder = (options && options.placeholder) ? options.placeholder : 'Seleccione...';
        // Limpiar el select
        select.innerHTML = `<option value="">${placeholder}</option>`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const list = Array.isArray(data) ? data : (data && Array.isArray(data.data) ? data.data : []);
                list.forEach(item => {
                    select.insertAdjacentHTML('beforeend', getOption(item));
                });

                // Si Select2 está activo para este elemento, refrescarlo
                if (window.jQuery && (jQuery(select).hasClass("select2-hidden-accessible") || jQuery(select).data('select2'))) {
                    jQuery(select).trigger('change.select2');
                    jQuery(select).trigger('change');
                } else {
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }

                if (options && typeof options.onComplete === 'function') {
                    options.onComplete(data);
                }
            })
            .catch(error => {
                console.error(`Error al poblar select "${selectId}":`, error);
            });
    }


}