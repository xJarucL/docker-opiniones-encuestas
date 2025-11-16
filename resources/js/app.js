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
    import('./podio.js').then(() => console.log('podio.js cargado'));
    import('./podiotwo.js').then(() => console.log('podiotwo.js cargado'));
    import('./presentacion-completa.js').then(() => console.log('presentacion-completa.js cargado'));
    import('./presentacion.js').then(() => console.log('presentacion.js cargado'));
    import('./presentacionone.js').then(() => console.log('presentacionone.js cargado'));
    import('./presentaciontwo.js').then(() => console.log('presentaciontwo.js cargado'));
    import('./resultados.js').then(() => console.log('resultados.js cargado'));
    import('./resultadostwo.js').then(() => console.log('resultadostwo.js cargado'));
});
