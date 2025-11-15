document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Lógica para "Ver todas/menos respuestas"
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const parent = btn.closest('.pregunta-block');
            const hiddenRows = parent.querySelectorAll('.extra');
            hiddenRows.forEach(row => row.classList.toggle('hidden'));

            btn.textContent = btn.textContent.includes('todas')
                ? 'Ver menos respuestas'
                : 'Ver todas las respuestas';
        });
    });

    // 2. Función para animar las barras de progreso
    const animateBars = () => {
        document.querySelectorAll('.progress-bar').forEach(bar => {
            // Guardar el ancho original
            const finalWidth = bar.style.width;
            
            // Resetear a 0
            bar.style.transition = 'none';
            bar.style.width = '0%';
            
            // Forzar reflow
            void bar.offsetHeight; 
            
            // Aplicar animación
            setTimeout(() => {
                bar.style.transition = 'width 1s ease-out'; 
                bar.style.width = finalWidth;
            }, 50);
        });
    };
    
    // 3. Función para animar las tarjetas y el header
    const animateCards = () => {
        const preguntaBlocks = document.querySelectorAll('.pregunta-block');
        const header = document.querySelector('.header');
        
        // Animar header
        if (header) {
            header.style.animation = 'none';
            void header.offsetHeight;
            header.style.animation = 'fadeInDown 0.6s ease-out';
        }
        
        // Animar cada bloque de pregunta
        preguntaBlocks.forEach((block, index) => {
            block.style.animation = 'none';
            void block.offsetHeight;
            block.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s both`;
        });
    };

    // 4. Ejecutar animaciones al cargar la página
    setTimeout(() => {
        animateBars();
    }, 300);

    // 5. Botón repetir animación - MEJORADO
    const repeatBtn = document.querySelector('.repeat-button');
    if (repeatBtn) {
        repeatBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('🔄 Repitiendo animaciones...'); // Debug
            
            // Rotar el ícono SVG
            const svg = repeatBtn.querySelector('svg');
            if (svg) {
                svg.style.transform = 'rotate(360deg)';
                setTimeout(() => {
                    svg.style.transform = 'rotate(0deg)';
                }, 600);
            }
            
            // Reiniciar animaciones
            animateCards();
            
            // Reiniciar barras después de un pequeño delay
            setTimeout(() => {
                animateBars();
            }, 100);
        });
    } else {
        console.error('❌ No se encontró el botón .repeat-button');
    }
});