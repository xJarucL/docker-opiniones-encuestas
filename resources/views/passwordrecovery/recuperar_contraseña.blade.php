<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<script src="{{ asset('js/app.js') }}" defer></script>
<script src="{{ asset('js/funciones.js') }}" defer></script>
<script src="{{ asset('js/sweetalert.js') }}" defer></script>

<div class="min-h-screen w-full flex flex-col items-center justify-center
            bg-purple-800 px-4 py-8 space-y-6">

    <div class="text-center w-full max-w-md px-2">
        <h1 class="text-2xl xs:text-3xl sm:text-4xl font-bold text-white leading-tight">
            Encuestas y Opiniones
        </h1>
        <p class="text-purple-200 mt-2 text-xs xs:text-sm sm:text-base md:text-lg">
            Recupera tu contraseña para continuar participando
        </p>
    </div>

    <div class="w-full max-w-xs xs:max-w-sm sm:max-w-md md:max-w-lg
                bg-white rounded-2xl shadow-lg p-4 xs:p-5 sm:p-6 md:p-8
                animate-fade-slide hover:shadow-xl transition-shadow duration-300">

        <x-msj-alert />

        <h2 class="text-lg xs:text-xl sm:text-2xl font-bold text-gray-800
                   text-center mb-4 sm:mb-6">
            Recuperar Contraseña
        </h2>

        <form action="{{ route('password.email') }}" method="POST"
              class="space-y-3 xs:space-y-4 sm:space-y-5">
            @csrf

            <div>
                <label for="email"
                    class="block text-sm sm:text-base font-medium text-gray-700 mb-1">
                    Correo electrónico
                </label>
                <input type="email" name="email" id="email" placeholder="ejemplo@gmail.com" required
                    class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3 rounded-lg shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600
                           text-sm sm:text-base">
            </div>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded text-xs xs:text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex justify-center">
                <div class="g-recaptcha"
                     data-sitekey="6Lf9ltYrAAAAAEVLBf6GEyIIE6pQTXa61RELzjIh"></div>
            </div>

            <div>
                <input type="submit" value="Enviar instrucciones"
                    class="w-full bg-purple-600 text-white font-semibold
                           px-4 py-2 sm:py-3 rounded-lg shadow-md
                           hover:bg-purple-700 focus:outline-none
                           focus:ring-2 focus:ring-purple-500
                           transition duration-200 cursor-pointer
                           text-sm sm:text-base md:text-lg" />
            </div>

            <div class="text-center mt-2 sm:mt-3">
                <a href="{{ route('login') }}"
                   class="text-xs sm:text-sm text-gray-600 hover:underline">
                    ¿Recordaste tu contraseña? Inicia sesión
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
