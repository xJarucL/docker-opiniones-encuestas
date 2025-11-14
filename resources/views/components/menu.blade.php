<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Opiniones y Encuestas')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/funciones.js', 'resources/js/sweetalert.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50" x-data="{ openMenu: false, openUser: false }">

    <nav class="bg-purple-600 text-white p-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <button @click="openMenu = !openMenu" class="md:hidden focus:outline-none">

                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path x-show="!openMenu" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="openMenu" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h1 class="text-xl font-bold">Encuestas y Opiniones</h1>
        </div>

        <div class="relative">
            <button @click="openUser = !openUser" class="flex items-center focus:outline-none transition">
                <img
                    src="{{ auth()->user()->img_user ? asset('storage/' . auth()->user()->img_user) : asset('img/default.webp') }}"
                    alt="Usuario"
                    class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-md hover:ring-2 hover:ring-white transition"
                />
            </button>

            <div
                x-show="openUser"
                @click.away="openUser = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="absolute right-0 mt-2 w-44 bg-white/90 backdrop-blur-md text-gray-800 rounded-xl shadow-xl py-2 z-50 border-0"
                style="display: none;"
            >
                <div class="absolute top-0 right-4 -mt-2 w-3 h-3 bg-white/90 backdrop-blur-md rotate-45 shadow-md"></div>

                <a href="{{ route('inicio') }}"
                   class="block px-4 py-2 hover:bg-purple-100 hover:text-purple-700 rounded-lg transition">
                    Inicio
                </a>

                <a href="{{ route('compañeros') }}"
                   class="block px-4 py-2 hover:bg-purple-100 hover:text-purple-700 rounded-lg transition">
                    Compañeros
                </a>

                @if(auth()->user()->fk_tipo_user == 1)
                    <a href="{{ route('lista_usuarios') }}"
                       class="block px-4 py-2 hover:bg-purple-100 hover:text-purple-700 rounded-lg transition">
                        Gestión de usuarios
                    </a>
                @endif

                <a href="{{ route('perfil') }}"
                   class="block px-4 py-2 hover:bg-purple-100 hover:text-purple-700 rounded-lg transition">
                    Perfil
                </a>

                <form id="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            data-swal-form
                            data-target-form="logout-form"
                            data-swal-title="Cerrar sesión"
                            data-swal-text="¿Deseas cerrar tu sesión actualmente activa?"
                            data-swal-icon="warning"
                            data-swal-confirm="Sí, cerrar sesión"
                            data-swal-cancel="Cancelar"
                            class="w-full text-left px-4 py-2 hover:bg-red-100 hover:text-red-700 rounded-lg transition">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="md:hidden">
        <div
            x-show="openMenu"
            @click.away="openMenu = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-x-10"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 -translate-x-10"
            class="fixed inset-y-0 left-0 w-64 bg-purple-700 text-white z-50 p-6 space-y-4"
            style="display: none;"
        >
            <a href="{{ route('inicio') }}" class="block px-4 py-2 rounded-lg hover:bg-purple-600 transition">
                Inicio
            </a>

            <a href="{{ route('compañeros') }}"
                class="block px-4 py-2 hover:bg-purple-100 hover:text-purple-700 rounded-lg transition">
                Compañeros
            </a>

            @if(auth()->user()->fk_tipo_user == 1)
                <div class="border-t border-purple-600 pt-2 mt-2">
                    <span class="text-xs uppercase text-purple-300 font-semibold">Administrador</span>
                    <a href="{{ route('lista_usuarios') }}" class="block px-4 py-2 rounded-lg hover:bg-purple-600 transition mt-1">
                        Gestión de usuarios
                    </a>
                </div>
            @endif
        </div>
    </div>

    <main class="p-6 md:p-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
