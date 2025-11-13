import './bootstrap';

import $ from 'jquery';
import Alpine from 'alpinejs'

window.$ = $;
window.jQuery = $;
window.Alpine = Alpine

Alpine.start()

// PRUEBA
console.log('jQuery disponible:', typeof $);
console.log('app.js cargado');

import './sweetalert.js';
import './funciones.js';
