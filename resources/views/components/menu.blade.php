<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Opiniones y Encuestas')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<style>
    .nav-btn {
        display: flex;
        align-items: center;
        height: 100%;
        padding: 0 20px;
        border-radius: 10px;
        transition: all .25s ease;
    }
    .nav-btn:hover {
        background: rgba(255,255,255,0.18);
        transform: scale(1.07);
    }
    .mobile-btn {
        display: block;
        width: 100%;
        padding: 14px 18px;
        font-size: 1.1rem;
        border-radius: 12px;
        transition: all .25s ease;
    }
    .mobile-btn:hover {
        background: rgba(255,255,255,0.16);
        font-size: 1.24rem;
        transform: translateX(6px);
    }
    .mobile-btn:active {
        background: rgba(255,255,255,0.25);
        transform: translateX(10px);
    }
</style>
<body class="bg-gray-50" x-data="{ openMenu: false, openUser: false }">

    <nav class="bg-purple-600 text-white p-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <button @click="openMenu = !openMenu"
                    class="md:hidden w-full focus:outline-none p-2 rounded-lg transition active:scale-95">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path x-show="!openMenu" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="openMenu" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <h1 class="text-xl font-bold">Encuestas y Opiniones</h1>
        </div>

        <div class="hidden md:flex items-center h-full space-x-2">
            <a href="{{ route('inicio') }}"
            class="nav-btn">Inicio</a>

            @if(auth()->user()->fk_tipo_user == 2)
                <a href="{{ route('compañeros') }}"
                class="nav-btn">Compañeros</a>
                <a href="/encuestas"
                class="nav-btn">Encuestas</a>
            @endif

            @if(auth()->user()->fk_tipo_user == 1)
                <a href="/admin/encuestas"
                class="nav-btn">Encuestas</a>
                <a href="/admin/categorias"
                class="nav-btn">Categorias</a>
                <a href="/admin/comentarios"
                class="nav-btn">Comentarios</a>
                <a href="{{ route('admin.usuarios.lista') }}"
                class="nav-btn">Usuarios</a>
            @endif
        </div>

        <div class="relative">
            <button @click="openUser = !openUser"
                    class="flex items-center focus:outline-none transition">
                <img
                    src="{{ auth()->user()->img_user ? asset('storage/' . auth()->user()->img_user) : asset('img/default.webp') }}"
                    class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-md hover:ring-2 hover:ring-white transition"
                    alt="Usuario"
                />
            </button>

            <div x-show="openUser"
                @click.away="openUser = false"
                x-transition
                class="absolute right-0 mt-2 w-44 bg-white/90 backdrop-blur-md text-gray-800 rounded-xl shadow-xl py-2 z-50"
                style="display:none;">
                <a href="{{ route('perfil') }}"
                class="block px-4 py-2 hover:bg-purple-100 hover:text-purple-700 rounded-lg transition">
                    Perfil
                </a>

                <form id="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button"
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
                    <button type="submit" class="hidden" data-swal-submit-button></button>
                </form>
            </div>
        </div>
    </nav>

    <div class="md:hidden">
        <div
            x-show="openMenu"
            @click.away="openMenu = false"
            x-transition
            class="fixed inset-y-0 left-0 w-64 bg-purple-700 text-white z-50 p-6 space-y-2 shadow-2xl"
            style="display: none;"
        >
            <a href="{{ route('inicio') }}"
            class="mobile-btn">Inicio</a>

            @if(auth()->user()->fk_tipo_user == 2)
                <a href="{{ route('compañeros') }}"
                class="mobile-btn">Compañeros</a>
                <a href="/encuestas"
                class="mobile-btn">Encuestas</a>
            @endif

            @if(auth()->user()->fk_tipo_user == 1)
                <div class="pt-2">
                    <span class="text-xs uppercase text-purple-300 font-semibold">Administrador</span>

                    <a href="{{ route('admin.usuarios.lista') }}"
                    class="mobile-btn mt-1">Gestión de usuarios</a>
                    <a href="/admin/encuestas"
                    class="mobile-btn mt-1">Gestión de encuestas</a>
                    <a href="/admin/categorias"
                    class="mobile-btn mt-1">Gestión de categorías</a>
                    <a href="/admin/comentarios"
                    class="mobile-btn mt-1">Gestión de comentarios</a>
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
