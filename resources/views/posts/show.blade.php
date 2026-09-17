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
                <x-theme-toggle />
                @auth
                    <x-suggestion-notifications />
                    <!-- Aparece só se o usuário for o dono do post ou se for Admin -->
                    @if(auth()->id() === $post->user_id || auth()->user()->is_admin)
                        <a href="{{ route('posts.edit', $post->slug) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">✏️ Editar</a>
                    @endif
                    
                    @if(auth()->user()->is_admin || auth()->user()->is_editor)
                        <a href="{{ route('posts.create') }}" class="bg-unimontes text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition shadow-sm">
                            + Novo Tutorial
                        </a>
                    @endif
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
                @if($post->isArchived())
                    <span class="bg-amber-100 text-amber-800 font-semibold px-3 py-1 rounded-full">Processo arquivado</span>
                    <span>•</span>
                @endif
                <span class="bg-indigo-50 text-indigo-700 font-semibold px-3 py-1 rounded-full">
                    {{ $post->category->name ?? 'Sem Categoria' }}
                </span>
                <span>•</span>
                <span>Criado por <strong>{{ $post->user->name ?? 'Servidor' }}</strong> em {{ $post->created_at->format('d/m/Y H:i') }}</span>
                <span>•</span>
                <span>Atualizado em {{ $post->updated_at->format('d/m/Y H:i') }}</span>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

            <div class="flex flex-wrap gap-3 border-b pb-6 mb-6">
                <a href="{{ route('posts.versions', $post->slug) }}" class="text-sm font-medium text-indigo-600 hover:underline">Ver histórico de versões</a>

                @if(auth()->user()->is_admin || auth()->id() === $post->user_id)
                    @if($post->isArchived())
                        <form method="POST" action="{{ route('posts.unarchive', $post->slug) }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-emerald-700 hover:underline">Desarquivar processo</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('posts.archive', $post->slug) }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-amber-700 hover:underline">Arquivar processo</button>
                        </form>
                    @endif
                @endif
            </div>

            
            <div class="prose max-w-none text-gray-700 leading-relaxed border-t pt-6">
                {!! $post->content !!}
            </div>

            @if($pinnedSuggestions->isNotEmpty())
                <section class="mt-10 border-t pt-8">
                    <div class="mb-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Contribuições da comunidade</p>
                        <h2 class="text-xl font-bold text-gray-900">Comentários fixados</h2>
                        <p class="mt-1 text-sm text-gray-500">Estas observações foram consideradas úteis pelo responsável pelo post.</p>
                    </div>
                    <div class="space-y-4">
                        @foreach($pinnedSuggestions as $pinnedSuggestion)
                            <article class="rounded-xl border border-emerald-100 bg-emerald-50 p-5">
                                <p class="whitespace-pre-line text-gray-800">{{ $pinnedSuggestion->content }}</p>
                                <p class="mt-3 text-xs text-emerald-800">Contribuição de {{ $pinnedSuggestion->user->name ?? 'usuário removido' }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="mt-10 border-t pt-8">
                <h2 class="text-xl font-bold text-gray-900">Sugerir uma melhoria</h2>
                <p class="mt-1 text-sm text-gray-500">Sua sugestão será enviada em privado ao criador deste post.</p>
                <form method="POST" action="{{ route('suggestions.store', $post->slug) }}" class="mt-4">
                    @csrf
                    <textarea name="content" rows="5" required maxlength="5000" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Explique o que poderia ser corrigido, atualizado ou melhor detalhado..."></textarea>
                    @error('content')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    <button type="submit" class="mt-3 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Enviar sugestão privada</button>
                </form>
            </section>
        </article>
    </main>

</body>
</html>