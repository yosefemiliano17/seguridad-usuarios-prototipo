<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md text-center">
        @if(session('usuario'))
            <h1 class="text-3xl font-bold text-gray-800 mb-2">¡Bienvenido de nuevo!</h1>
            <p class="text-gray-600 mb-6 text-lg">
                Has iniciado sesión como:
                <span class="font-semibold text-blue-600">{{ session('usuario.email') }}</span>
            </p>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full sm:w-auto inline-block bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg">
                    Cerrar Sesión
                </button>
            </form>
        @else
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Acceso denegado</h1>
            <p class="text-gray-600 mb-6">Necesitas iniciar sesión para ver esta página.</p>
            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Ir a Iniciar Sesión</a>
        @endif
    </div>
</body>
</html>