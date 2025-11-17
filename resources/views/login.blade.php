@vite(['resources/css/app.css', 'resources/js/app.js'])
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="min-h-screen w-full flex flex-col items-center justify-center
            bg-purple-800 px-4 py-8 space-y-6">

    <div class="text-center w-full max-w-md">
        <h1 class="text-2xl xs:text-3xl sm:text-4xl font-bold text-white leading-tight">
            Encuestas y Opiniones
        </h1>
        <p class="text-purple-200 mt-2 text-xs xs:text-sm sm:text-base">
            Inicia sesión para participar y compartir tus opiniones
        </p>
    </div>

    <div class="w-full max-w-xs xs:max-w-sm sm:max-w-md md:max-w-lg
                bg-white rounded-2xl shadow-lg p-4 xs:p-5 sm:p-6 md:p-8
                animate-fade-slide hover:shadow-xl transition-shadow duration-300">

        <x-msj-alert />

        <h2 class="text-lg xs:text-xl sm:text-2xl font-bold text-gray-800
                   text-center mb-4 sm:mb-6">
            Iniciar sesión
        </h2>

        <form id="loginForm" class="space-y-3 xs:space-y-4 sm:space-y-5">
            @csrf

            <div>
                <label for="email"
                    class="block text-sm sm:text-base font-medium text-gray-700 mb-1">
                    Correo electrónico
                </label>
                <input type="text" id="email" name="email" required
                    class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3
                           rounded-lg shadow-sm focus:outline-none
                           focus:ring-2 focus:ring-purple-600 focus:border-purple-600
                           text-sm sm:text-base">
            </div>

            <div>
                <label for="password"
                    class="block text-sm sm:text-base font-medium text-gray-700 mb-1">
                    Contraseña
                </label>
                <input type="password" id="password" name="password" required
                    class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3
                           rounded-lg shadow-sm focus:outline-none
                           focus:ring-2 focus:ring-purple-600 focus:border-purple-600
                           text-sm sm:text-base">
            </div>

            <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-purple-600 text-white font-semibold
                           px-4 py-2 sm:py-3 rounded-lg shadow-md
                           hover:bg-purple-700 focus:outline-none
                           focus:ring-2 focus:ring-purple-500
                           transition duration-200
                           text-sm sm:text-base md:text-lg">
                    Ingresar
                </button>
            </div>

            <div class="text-center mt-2 sm:mt-3">
                <a href="{{ route('recuperar_contraseña') }}"
                   class="text-xs sm:text-sm text-gray-600 hover:underline">
                    ¿No recuerdas tu contraseña? ¡Recupérala!
                </a>
            </div>
        </form>

    </div>
</div>

<style>
@keyframes fadeSlide {
  from { opacity: 0; transform: translateY(-20px); }
  to   { opacity: 1; transform: translateY(0); }
}
.animate-fade-slide { animation: fadeSlide 0.8s ease-out; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorDiv = document.getElementById('error-message');
    
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Ocultar mensaje de error previo
        errorDiv.classList.add('hidden');
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('{{ route('iniciando') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.class === 'success') {
                // ✅ CORRECTO - Redirigir a la ruta que viene del servidor
                console.log('Redirigiendo a:', data.ruta);
                window.location.href = data.ruta;
            } else {
                // Mostrar error
                errorDiv.textContent = data.mensaje || 'Error al iniciar sesión';
                errorDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            errorDiv.textContent = 'Error de conexión. Intenta de nuevo.';
            errorDiv.classList.remove('hidden');
        }
    });
});
</script>