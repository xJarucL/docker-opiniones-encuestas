import './bootstrap';
import $ from 'jquery';
import Alpine from 'alpinejs';

// Hacer disponibles globalmente
window.$ = $;
window.jQuery = $;
window.Alpine = Alpine;

Alpine.start();

console.log('jQuery disponible:', typeof $);
console.log('app.js cargado correctamente');

// Esperar a que el DOM y jQuery estén listos
$(document).ready(function () {
    console.log('DOM listo, jQuery activo');

    // Cargar scripts dependientes una vez que jQuery está disponible
    import('./sweetalert.js').then(() => console.log('sweetalert.js cargado'));
    import('./funciones.js').then(() => console.log('funciones.js cargado'));
});
