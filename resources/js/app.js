// ========================================
// FORMULARIO DE USUARIOS (CON SWEETALERT2)
// ========================================
const formUsuario = document.getElementById('formUsuario');
if (formUsuario) {
    console.log('🔍 Formulario encontrado');
    
    formUsuario.addEventListener('submit', async function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        
        console.log('✅ Formulario de usuario interceptado');
        
        const btnSubmit = this.querySelector('button[type="submit"]');
        const originalText = btnSubmit ? btnSubmit.textContent : '';
        
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.textContent = 'Guardando...';
        }
        
        const formData = new FormData(this);
        const isEdit = formData.has('id') && formData.get('id') !== '';
        let url = this.getAttribute('action');
        
        console.log('📤 URL de envío:', url);
        console.log('📝 ¿Es edición?:', isEdit);
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                }
            });
            
            const data = await response.json();
            console.log('📥 Respuesta:', data);
            
            if (data.class === 'success') {
                // SweetAlert de éxito
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.mensaje,
                    confirmButtonColor: '#9333ea', // Morado (purple-600)
                    confirmButtonText: 'Aceptar',
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = data.ruta;
                });
            } else {
                // SweetAlert de error con validaciones
                let errores = '';
                if (typeof data.mensaje === 'object') {
                    errores = '<ul style="text-align: left; padding-left: 20px;">';
                    for (let campo in data.mensaje) {
                        data.mensaje[campo].forEach(error => {
                            errores += `<li>${error}</li>`;
                        });
                    }
                    errores += '</ul>';
                } else {
                    errores = data.mensaje;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    html: errores,
                    confirmButtonColor: '#dc2626', // Rojo (red-600)
                    confirmButtonText: 'Entendido'
                });
                
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = originalText;
                }
            }
        } catch (error) {
            console.error('❌ Error en la petición:', error);
            
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Por favor, intenta nuevamente.',
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Aceptar'
            });
            
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.textContent = originalText;
            }
        }
    });
}