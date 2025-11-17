// ============================================
// app.js - Versión SIN imports (para public/)
// ============================================

// jQuery y Alpine ya están disponibles globalmente desde los CDNs
// No necesitamos importarlos

document.addEventListener('DOMContentLoaded', () => {

    // --- INICIO: Lógica de SweetAlert2 (Global) ---
    document.addEventListener('click', function (e) {
        const swalButton = e.target.closest('[data-swal-form]');
        if (swalButton) {
            e.preventDefault(); 
            const form = swalButton.closest('form');
            
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 no se ha cargado. El formulario se enviará directamente.');
                const submitButton = form.querySelector('[data-swal-submit-button]');
                if (submitButton) submitButton.click(); else form.submit();
                return;
            }
            
            Swal.fire({
                title: swalButton.dataset.swalTitle || '¿Estás seguro?',
                text: swalButton.dataset.swalText || 'No podrás revertir esta acción.',
                icon: swalButton.dataset.swalIcon || 'warning',
                showCancelButton: true,
                confirmButtonColor: swalButton.dataset.swalColor || '#3085d6',
                cancelButtonColor: '#6e7881',
                confirmButtonText: swalButton.dataset.swalConfirm || 'Sí, continuar',
                cancelButtonText: swalButton.dataset.swalCancel || 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const submitButton = form.querySelector('[data-swal-submit-button]');
                    if (submitButton) {
                        submitButton.click(); 
                    } else {
                        form.submit();
                    }
                }
            });
        }
    });
    // --- FIN: Lógica de SweetAlert2 ---


    // --- Lógica para 'presentacionone.blade.php' (Intro Múltiple) ---
    const envelopeOne = document.getElementById('envelopeWrapper');
    const nomineesOne = document.getElementById('nomineesText');
    if (envelopeOne && nomineesOne && window.location.href.includes('presentacionone')) {
        let hasOpened = false;
        
        function openEnvelopeOne() {
            hasOpened = true;
            envelopeOne.classList.add('opening');
            
            setTimeout(() => {
                envelopeOne.classList.add('exit');
                nomineesOne.classList.add('show');

                setTimeout(() => {
                    const pathArray = window.location.pathname.split('/');
                    const encuestaId = pathArray[pathArray.length - 1];
                    window.location.href = `/presentaciontwo/${encuestaId}/0`; 
                }, 3500);
            }, 2000);
        }

        setTimeout(() => {
            if (!hasOpened) openEnvelopeOne();
        }, 2000);

        envelopeOne.addEventListener('click', () => {
            if (!hasOpened) openEnvelopeOne();
        });
    }

    // --- Lógica para 'presentacion.blade.php' (Intro Simple) ---
    const envelopeSimple = document.getElementById('envelopeWrapper');
    const nomineesSimple = document.getElementById('nomineesText');
    if (envelopeSimple && nomineesSimple && window.location.href.includes('/presentacion/')) {
        let hasOpened = false;

        function openEnvelopeSimple() {
            hasOpened = true;
            envelopeSimple.classList.add('opening');
            
            setTimeout(() => {
                envelopeSimple.classList.add('exit');
                nomineesSimple.classList.add('show');

                setTimeout(() => {
                    const pathArray = window.location.pathname.split('/');
                    const preguntaId = pathArray[pathArray.length - 1];
                    window.location.href = `/podio/${preguntaId}`;
                }, 3500);
            }, 2000);
        }
        
        setTimeout(() => { if (!hasOpened) openEnvelopeSimple(); }, 2000);
        envelopeSimple.addEventListener('click', () => { if (!hasOpened) openEnvelopeSimple(); });
    }


    // --- Lógica para 'podiotwo.blade.php' (Podio animado múltiple) ---
    const envelopePodioTwo = document.getElementById('envelopeWrapper');
    const verResultadosBtn = document.getElementById('verResultadosBtn');
    if (envelopePodioTwo && verResultadosBtn) {
        const section1 = document.getElementById('section1');
        const section2 = document.getElementById('section2');
        const section3 = document.getElementById('section3');

        envelopePodioTwo.addEventListener('click', () => {
            envelopePodioTwo.classList.add('opening');
            
            setTimeout(() => {
                section1.classList.add('hidden');
                section2.classList.add('active');
                
                setTimeout(() => {
                    section2.classList.remove('active');
                    section3.classList.add('active');
                    
                    setTimeout(() => {
                        verResultadosBtn.classList.add('show');
                    }, 4000);
                }, 3000);
            }, 2000);
        });
    }

    
    // --- Lógica para 'presentaciontwo.blade.php' (Página de votación) ---
    // (El código que te redirigía automáticamente ha sido ELIMINADO)


    // --- Lógica para 'resultadostwo.blade.php' (Resultados finales múltiples) ---
    const toggleButtons = document.querySelectorAll('.toggle-btn');
    if (toggleButtons.length > 0) {
        
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const parent = btn.closest('.pregunta-block');
                const hiddenRows = parent.querySelectorAll('.extra');
                hiddenRows.forEach(row => row.classList.toggle('hidden'));

                btn.textContent = btn.textContent.includes('todas')
                    ? 'Ver menos respuestas'
                    : 'Ver todas las respuestas';
            });
        });

        const animateBarsTwo = () => {
            document.querySelectorAll('.progress-bar').forEach(bar => {
                const finalWidth = bar.style.width;
                bar.style.transition = 'none';
                bar.style.width = '0%';
                void bar.offsetHeight; 
                setTimeout(() => {
                    bar.style.transition = 'width 1s ease-out'; 
                    bar.style.width = finalWidth;
                }, 50);
            });
        };
        
        const animateCardsTwo = () => {
            const preguntaBlocks = document.querySelectorAll('.pregunta-block');
            const header = document.querySelector('.header');
            
            if (header) {
                header.style.animation = 'none';
                void header.offsetHeight;
                header.style.animation = 'fadeInDown 0.6s ease-out';
            }
            
            preguntaBlocks.forEach((block, index) => {
                block.style.animation = 'none';
                void block.offsetHeight;
                block.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s both`;
            });
        };

        setTimeout(() => { animateBarsTwo(); }, 300);

        const repeatBtnTwo = document.querySelector('.repeat-button');
        if (repeatBtnTwo) {
            repeatBtnTwo.addEventListener('click', (e) => {
                e.preventDefault();
                
                const pathArray = window.location.pathname.split('/');
                const encuestaId = pathArray[pathArray.length - 1];
                window.location.href = `/podiotwo/${encuestaId}/0`;
            });
        }
    }


    // --- Lógica para 'podio.blade.php' (Podio simple) ---
    const podioSimple = document.getElementById('podio-simple-page');
    if(podioSimple) {
        setTimeout(() => {
            const pathArray = window.location.pathname.split('/');
            const preguntaId = pathArray[pathArray.length - 1];
            window.location.href = `/resultados/${preguntaId}`;
        }, 6000);
    }
    
    // --- Lógica para 'resultados.blade.php' (Resultados simples) ---
    const repeatBtnSimple = document.querySelector('.repeat-button');
    if (repeatBtnSimple && toggleButtons.length === 0) { 

        const animateBarsSimple = () => {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach((bar, index) => {
                const percentage = bar.getAttribute('data-percentage');
                setTimeout(() => {
                    bar.style.setProperty('--percentage', percentage + '%');
                    bar.classList.add('animate');
                }, (index + 1) * 200 + 500);
            });
        };
        
        animateBarsSimple();

        repeatBtnSimple.addEventListener('click', function() {
            const pathArray = window.location.pathname.split('/');
            const preguntaId = pathArray[pathArray.length - 1];
            window.location.href = `/presentacion/${preguntaId}`;
        });
    }

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
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.mensaje,
                        confirmButtonColor: '#9333ea',
                        confirmButtonText: 'Aceptar',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = data.ruta;
                    });
                } else {
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
                        confirmButtonColor: '#dc2626',
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

});