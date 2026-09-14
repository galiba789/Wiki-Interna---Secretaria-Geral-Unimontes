<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->title }} - Wiki</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-bold text-indigo-600 hover:underline">← Voltar para a Wiki</a>
            
            <div class="flex space-x-2">
                @auth
                    <!-- Aparece só se o usuário for o dono do post ou se for Admin -->
                    @if(auth()->id() === $post->user_id || auth()->user()->is_admin)
                        <a href="{{ route('posts.edit', $post->slug) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">✏️ Editar</a>
                    @endif
                    
                    @if(auth()->user()->is_admin || auth()->user()->is_editor)
                        <a href="{{ route('posts.create') }}" class="bg-unimontes text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition shadow-sm">
                            + Novo Tutorial
                        </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-10">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        <article class="bg-white rounded-2xl shadow-sm p-8 md:p-12">
            <div class="flex items-center space-x-3 text-xs text-gray-500 mb-4">
                <span class="bg-indigo-50 text-indigo-700 font-semibold px-3 py-1 rounded-full">
                    {{ $post->category->name ?? 'Sem Categoria' }}
                </span>
                <span>•</span>
                <span>Criado por <strong>{{ $post->user->name ?? 'Servidor' }}</strong> em {{ $post->created_at->format('d/m/Y H:i') }}</span>
                <span>•</span>
                <span>Atualizado em {{ $post->updated_at->format('d/m/Y H:i') }}</span>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

            
            <div class="prose max-w-none text-gray-700 leading-relaxed border-t pt-6">
                {!! $post->content !!}
            </div>
        </article>
    </main>

</body>
</html>