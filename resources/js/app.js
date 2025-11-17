import './bootstrap';
import $ from 'jquery';
import Alpine from 'alpinejs';

window.$ = $;
window.jQuery = $;
window.Alpine = Alpine;

Alpine.start();

// --- INICIO: Lógica Específica de Páginas ---
// Todo debe ir dentro de DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {

    // --- INICIO: Lógica de SweetAlert2 (Global) ---
    // (Manejador para 'data-swal-form' en botones de Cerrar Sesión, Deshabilitar, etc.)
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
                    // Redirige a la primera pregunta (página de votación)
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
    if (envelopePodioTwo && verResultadosBtn) { // Si existe el sobre Y el botón de siguiente
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
                    }, 4000); // Muestra el botón de Siguiente/Finalizar
                }, 3000);
            }, 2000);
        });
    }

    
    // --- Lógica para 'presentaciontwo.blade.php' (Página de votación) ---
    // (El código que te redirigía automáticamente ha sido ELIMINADO)
    // (Ahora esta página simplemente cargará y esperará a que el usuario vote)


    // --- Lógica para 'resultadostwo.blade.php' (Resultados finales múltiples) ---
    const toggleButtons = document.querySelectorAll('.toggle-btn');
    if (toggleButtons.length > 0) { // Si encontramos botones de 'toggle'
        
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
            
            // Botón "Repetir Animación"
            repeatBtnTwo.addEventListener('click', (e) => {
                e.preventDefault();
                
                const pathArray = window.location.pathname.split('/');
                const encuestaId = pathArray[pathArray.length - 1];

                // Redirige al inicio del podio animado (pregunta 0)
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

});