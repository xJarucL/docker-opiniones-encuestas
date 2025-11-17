document.addEventListener('DOMContentLoaded', () => {
    const envelopeWrapper = document.getElementById('envelopeWrapper');
    const section1 = document.getElementById('section1');
    const section2 = document.getElementById('section2');
    const section3 = document.getElementById('section3');

    if (envelopeWrapper) {
        envelopeWrapper.addEventListener('click', () => {
            envelopeWrapper.classList.add('opening');
            
            setTimeout(() => {
                section1.classList.add('hidden');
                section2.classList.add('active');
                
                setTimeout(() => {
                    section2.classList.remove('active');
                    section3.classList.add('active');
                }, 3000);
            }, 2000);
        });
    }
});