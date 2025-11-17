<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Encuestas y Opiniones</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="min-h-screen w-full flex flex-col items-center justify-center
            bg-purple-800 px-4 py-8 space-y-6">

    <div class="text-center w-full max-w-md">
        <h1 class="text-2xl xs:text-3xl sm:text-4xl font-bold text-white leading-tight">
            Nueva Contraseña
        </h1>
        <p class="text-purple-200 mt-2 text-xs xs:text-sm sm:text-base">
            Ingresa tu nueva contraseña para tu cuenta
        </p>
    </div>

    <div class="w-full max-w-xs xs:max-w-sm sm:max-w-md md:max-w-lg
                bg-white rounded-2xl shadow-lg p-4 xs:p-5 sm:p-6 md:p-8
                animate-fade-slide hover:shadow-xl transition-shadow duration-300">

        <x-msj-alert />

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ request()->token }}">
            <input type="hidden" name="email" value="{{ request()->email }}">

            <div>
                <label for="password"
                    class="block text-sm sm:text-base font-medium text-gray-700 mb-1">
                    Nueva Contraseña
                </label>
                <input type="password" id="password" name="password" required
                    class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3
                           rounded-lg shadow-sm focus:outline-none
                           focus:ring-2 focus:ring-purple-600 focus:border-purple-600
                           text-sm sm:text-base">
            </div>

            <div>
                <label for="password_confirmation"
                    class="block text-sm sm:text-base font-medium text-gray-700 mb-1">
                    Confirmar Contraseña
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3
                           rounded-lg shadow-sm focus:outline-none
                           focus:ring-2 focus:ring-purple-600 focus:border-purple-600
                           text-sm sm:text-base">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-purple-600 text-white font-semibold
                           px-4 py-2 sm:py-3 rounded-lg shadow-md
                           hover:bg-purple-700 focus:outline-none
                           focus:ring-2 focus:ring-purple-500
                           transition duration-200
                           text-sm sm:text-base md:text-lg">
                    Restablecer Contraseña
                </button>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}"
                   class="text-xs sm:text-sm text-gray-600 hover:underline">
                    Volver al inicio de sesión
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

</body>
</html>