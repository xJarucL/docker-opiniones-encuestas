@vite(['resources/css/app.css', 'resources/js/app.js'])
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="min-h-screen flex flex-col items-center justify-center bg-purple-800 px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="text-center px-2 sm:px-0">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white">Encuestas y Opiniones</h1>
        <p class="text-purple-200 mt-2 text-sm sm:text-base md:text-lg">
            Inicia sesión para participar y compartir tus opiniones
        </p>
    </div>

    <div class="w-full max-w-sm sm:max-w-md md:max-w-lg bg-white rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 animate-fade-slide hover:shadow-2xl transition-shadow duration-300">
        <x-msj-alert />

        <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 text-center mb-4 sm:mb-6">Iniciar sesión</h2>

        <form id="form-insertar" data-url="{{ route('iniciando') }}" class="space-y-3 sm:space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="text" name="email" id="email" required
                       class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3 rounded-lg shadow-sm
                              focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600
                              text-sm sm:text-base">
            </div>

            <div>
                <label for="password" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" id="password" required
                       class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3 rounded-lg shadow-sm
                              focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600
                              text-sm sm:text-base">
            </div>

            <div>
                <input type="submit" value="Ingresar"
                       class="w-full bg-purple-600 text-white font-semibold px-4 py-2 sm:py-3 rounded-lg shadow-md
                              hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500
                              transition duration-200 cursor-pointer text-sm sm:text-base md:text-lg" />
            </div>

            <div class="text-center mt-2 sm:mt-3">
                <a href="{{ route('recuperar_contraseña') }}" class="text-xs sm:text-sm text-gray-600 hover:underline">
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
