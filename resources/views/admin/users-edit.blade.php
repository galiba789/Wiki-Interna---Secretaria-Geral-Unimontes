<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Servidor - Painel Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-sm w-full max-w-md">
        <h1 class="text-xl font-bold text-gray-800 mb-6">Editar Servidor: {{ $user->name }}</h1>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm">
                <strong class="font-bold">Atenção!</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail Profissional</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha <span class="text-xs text-gray-400">(Deixe em branco para não alterar)</span></label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Perfil de Acesso</label>
                <select name="role" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    <option value="leitor" {{ (!$user->is_admin && !$user->is_editor) ? 'selected' : '' }}>
                        Leitor (Apenas pesquisa e lê os tutoriais)
                    </option>
                    <option value="editor" {{ (!$user->is_admin && $user->is_editor) ? 'selected' : '' }}>
                        Servidor (Pode criar e editar seus próprios tutoriais)
                    </option>
                    <option value="admin" {{ $user->is_admin ? 'selected' : '' }}>
                        Administrador (Acesso total ao sistema)
                    </option>
                </select>
            </div>


            <div class="flex justify-between items-center">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:underline">Voltar</a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Atualizar Usuário</button>
            </div>
        </form>
    </div>

</body>
</html>