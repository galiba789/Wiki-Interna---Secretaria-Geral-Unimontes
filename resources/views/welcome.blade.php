<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wiki Interna - Procedimentos e Demandas</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts (Tailwind CSS do Laravel) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <!-- Header / Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center">
                    <!-- Logo da Unimontes -->
                    <img src="{{ asset('images/logo-unimontes.png') }}" alt="Logo Unimontes" class="h-10 w-auto">
                    <!-- Divisória e Título do Sistema -->
                    <span class="ml-4 pl-4 border-l-2 border-gray-200 text-lg font-bold text-unimontes tracking-tight">
                        Wiki Interna
                    </span>
                </a>
            </div>

           <div class="flex items-center space-x-4">
                @auth
                    <x-suggestion-notifications />
                    <!-- Botão para todos os logados (Admins e Servidores) -->
                    @if(auth()->user()->is_admin || auth()->user()->is_editor)
                        <a href="{{ route('posts.create') }}" class="bg-unimontes text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition shadow-sm">
                            + Novo Tutorial
                        </a>
                    @endif

                    @if(auth()->user()->is_admin)
                        <a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 ml-2">Painel Admin</a>
                    @endif

                    @if(auth()->user()->is_admin || auth()->user()->is_editor)
                        <a href="{{ route('posts.archived') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 ml-2">Arquivados</a>
                    @endif
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline ml-2">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Sair</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Entrar</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Barra de Busca Central -->
        <div class="bg-unimontes rounded-2xl p-8 mb-10 text-center text-white shadow-lg relative overflow-hidden">
            <!-- Efeito visual de fundo (opcional, deixa mais moderno) -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
            
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">Base de Conhecimento Institucional</h1>
                <p class="text-gray-200 mb-6 font-light">Busque por processos, tutoriais e normativas internas da universidade</p>

                <form action="{{ url('/') }}" method="GET" class="max-w-2xl mx-auto flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ex: Como cadastrar um novo aluno, fechamento de folha..." class="w-full px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-unimontes shadow-sm">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition shadow-sm">Buscar</button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

            <!-- Sidebar: Categorias -->
            <aside class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                    <h3 class="font-bold text-gray-700 uppercase text-xs tracking-wider mb-4">Categorias</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request('category') == '' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                📁 Todas as demandas
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ url('/?category=' . $category->slug) }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request('category') == $category->slug ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                    📂 {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <!-- Lista de Artigos / Posts -->
            <section class="md:col-span-3">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">
                        @if($archived ?? false)
                            Processos arquivados
                        @elseif(request('search'))
                            Resultados para "{{ request('search') }}"
                        @else
                            Últimos Tutoriais e Processos
                        @endif
                    </h2>
                    <span class="text-sm text-gray-500">{{ count($posts) }} encontrados</span>
                </div>

                @if($posts->isEmpty())
                    <div class="bg-white rounded-xl p-10 text-center shadow-sm">
                        <p class="text-gray-500 text-lg">Nenhum post encontrado.</p>
                        <p class="text-gray-400 text-sm mt-1">Tente buscar por outro termo ou cadastre o primeiro procedimento!</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($posts as $post)
                            <article class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition border border-gray-100">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                                    <span class="bg-indigo-50 text-indigo-700 font-semibold px-2.5 py-1 rounded-full">
                                        {{ $post->category->name ?? 'Sem Categoria' }}
                                    </span>
                                    <span>{{ $post->isArchived() ? 'Arquivado' : 'Atualizado' }} em {{ ($post->archived_at ?? $post->updated_at)->format('d/m/Y H:i') }}</span>
                                </div>

                                <h3 class="text-lg font-bold text-gray-900 mb-2">
                                    <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-indigo-600 transition">{{ $post->title }}</a>
                                </h3>

                                <p class="text-gray-600 text-sm line-clamp-2 mb-4">
                                    {{ Str::limit(strip_tags($post->content), 150) }}
                                </p>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100 text-xs text-gray-500">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium text-gray-700">Por {{ $post->user->name ?? 'Desconhecido' }}</span>
                                    </div>
                                    <a href="{{ route('posts.show', $post->slug) }}" class="text-indigo-600 font-medium hover:underline">Ler tutorial completo &rarr;</a>
                                </div>
                            </article>
                        @endforeach
                        <div class="mt-8">
                            {{ $posts->links() }}
                        </div>
                    </div>
                @endif
            </section>

        </div>
    </main>

</body>
</html>
