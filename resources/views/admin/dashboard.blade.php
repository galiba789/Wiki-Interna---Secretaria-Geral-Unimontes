<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel do Admin - Wiki</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <h1 class="text-xl font-bold text-indigo-600">Painel Administrativo</h1>
            <div class="flex items-center space-x-4">
                <x-theme-toggle />
                <x-suggestion-notifications />
                <a href="{{ url('/') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Ver Wiki (Home)</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Sair</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Seção de Cadastro de Categorias -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-10">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Adicionar Nova Categoria</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="flex gap-4">
                @csrf
                <input type="text" name="name" required placeholder="Nome da Categoria (Ex: Licitações, Atendimento...)" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none text-sm">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition whitespace-nowrap">Criar Categoria</button>
            </form>
        </div>
        
        <!-- Seção de Usuários -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-gray-800">Gerenciamento de Usuários (Servidores)</h2>
                <a href="{{ route('admin.users.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">+ Cadastrar Novo Servidor</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b text-xs text-gray-500 uppercase">
                            <th class="py-3 px-4">Nome</th>
                            <th class="py-3 px-4">E-mail</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        @foreach($users as $user)
                            <tr>
                                <td class="py-3 px-4 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $user->email }}</td>
                                <td class="py-3 px-4">
                                    @if($user->is_admin)
                                        <span class="bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full text-xs font-semibold">Admin</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full text-xs font-semibold">Servidor</span>
                                    @endif
                                </td>
                               <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Editar</a>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Excluir</button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-xs">(Sua conta)</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- Seção de Posts -->
        <!-- Seção de Posts -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-gray-800">Gerenciamento de Posts / Tutoriais</h2>
                <!-- BOTÃO ADICIONADO AQUI -->
                <a href="{{ route('posts.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">+ Escrever Novo Tutorial</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
            <!-- ... o resto da tabela continua igual ... -->
                    <thead>
                        <tr class="border-b text-xs text-gray-500 uppercase">
                            <th class="py-3 px-4">Título</th>
                            <th class="py-3 px-4">Categoria</th>
                            <th class="py-3 px-4">Autor</th>
                            <th class="py-3 px-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        @foreach($posts as $post)
                            <tr>
                                <td class="py-3 px-4 font-medium text-gray-900">{{ $post->title }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $post->category->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $post->user->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <!-- Botão de Visualizar -->
                                    <a href="{{ route('posts.show', $post->slug) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs" target="_blank">Ver</a>
                                    
                                    <!-- Botão de Editar -->
                                    <a href="{{ route('posts.edit', $post->slug) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Editar</a>
                                    
                                    <!-- Botão de Excluir -->
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 mb-10">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Gerenciar Categorias</h2>
            
            <!-- Formulário de Criar -->
            <form action="{{ route('admin.categories.store') }}" method="POST" class="flex gap-4 mb-6">
                @csrf
                <input type="text" name="name" required placeholder="Nova Categoria (Ex: Licitações, Atendimento...)" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none text-sm">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition whitespace-nowrap">Criar Categoria</button>
            </form>
    
            <!-- Tabela de Categorias -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b text-xs text-gray-500 uppercase">
                            <th class="py-3 px-4">Nome</th>
                            <th class="py-3 px-4">Slug (URL)</th>
                            <th class="py-3 px-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        @foreach($categories as $category)
                            <tr>
                                <td class="py-3 px-4 font-medium text-gray-900">{{ $category->name }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $category->slug }}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Editar</a>
                                    
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria? Os posts vinculados a ela ficarão sem categoria.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>

    </main>

    <!-- Seção de Gerenciamento de Categorias -->
</body>
</html>
