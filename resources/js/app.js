import './bootstrap';
import $ from 'jquery';
import Alpine from 'alpinejs';

window.$ = $;
window.jQuery = $;
window.Alpine = Alpine;

Alpine.start();

$(document).ready(function () {
    import('./sweetalert.js').then(() => console.log('sweetalert.js cargado'));
    import('./funciones.js').then(() => console.log('funciones.js cargado'));
});
