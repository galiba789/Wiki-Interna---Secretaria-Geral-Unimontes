<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sugestão - {{ $post->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <header class="bg-white shadow-sm">
        <div class="mx-auto flex h-16 max-w-5xl items-center justify-between gap-4 px-4">
            <a href="{{ route('suggestions.index') }}" class="font-bold text-indigo-600">Voltar às notificações</a>
            <x-suggestion-notifications />
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10">
        @if(session('success'))
            <div class="mb-6 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">{{ session('success') }}</div>
        @endif

        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Conversa privada</p>
                <h1 class="text-3xl font-bold text-gray-900">Sugestão para: {{ $post->title }}</h1>
                <p class="mt-2 text-sm text-gray-500">Visível apenas ao criador, participantes e administradores.</p>
            </div>
            @if(auth()->user()->is_admin || auth()->id() === $post->user_id)
                @if($suggestion->status === 'open')
                    <form method="POST" action="{{ route('suggestions.ignore', $suggestion->id) }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-gray-600 hover:text-gray-900">Ignorar conversa</button>
                    </form>
                @else
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600">Ignorada</span>
                @endif
            @endif
        </div>

        <section class="space-y-4">
            <article class="rounded-xl border border-indigo-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $suggestion->user->name ?? 'Usuário removido' }}</p>
                        <p class="text-xs text-gray-500">{{ $suggestion->created_at->format('d/m/Y H:i') }} · Sugestão original</p>
                    </div>
                    @if($suggestion->is_pinned)
                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">Público</span>
                    @endif
                </div>
                <p class="mt-4 whitespace-pre-line text-gray-700">{{ $suggestion->content }}</p>
                @include('suggestions.partials.pin-action', ['suggestionMessage' => $suggestion])
            </article>

            @foreach($suggestion->replies as $reply)
                <article class="ml-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm md:ml-12">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $reply->user->name ?? 'Usuário removido' }}</p>
                            <p class="text-xs text-gray-500">{{ $reply->created_at->format('d/m/Y H:i') }} · Resposta privada</p>
                        </div>
                        @if($reply->is_pinned)
                            <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">Público</span>
                        @endif
                    </div>
                    <p class="mt-4 whitespace-pre-line text-gray-700">{{ $reply->content }}</p>
                    @include('suggestions.partials.pin-action', ['suggestionMessage' => $reply])
                </article>
            @endforeach
        </section>

        @if($suggestion->status === 'open')
            <form method="POST" action="{{ route('suggestions.reply', $suggestion->id) }}" class="mt-8 rounded-xl bg-white p-6 shadow-sm">
                @csrf
                <label for="content" class="block text-sm font-semibold text-gray-700">Responder em privado</label>
                <textarea id="content" name="content" rows="5" required class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Escreva uma resposta para esta conversa..."></textarea>
                @error('content')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="mt-4 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Enviar resposta</button>
            </form>
        @endif
    </main>
</body>
</html>