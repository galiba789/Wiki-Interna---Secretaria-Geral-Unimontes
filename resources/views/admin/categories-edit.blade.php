<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Categoria - Painel Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-sm w-full max-w-md">
        <h1 class="text-xl font-bold text-gray-800 mb-6">Editar Categoria: {{ $category->name }}</h1>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                <strong class="font-bold">Atenção!</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Categoria</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>

            <div class="flex justify-between items-center">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:underline">Voltar</a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Atualizar Categoria</button>
            </div>
        </form>
    </div>

</body>
</html>