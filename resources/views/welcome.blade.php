<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-sm">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Iniciar Sesión</h1>
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" id="email" name="email" required
                       
                       value="{{ old('email') }}" 
                       
                       class="w-full border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                
                @error('email')
                    <p class="text-red-600 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                <input type="password" id="nip" name="nip" required
                       
                       class="w-full border @error('nip') border-red-500 @else border-gray-300 @enderror rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">

                @error('nip')
                    <p class="text-red-600 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition-transform transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Entrar
            </button>
        </form>
    </div>
</body>
</html>