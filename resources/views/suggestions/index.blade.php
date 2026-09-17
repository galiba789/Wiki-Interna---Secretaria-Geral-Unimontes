<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notificações - Wiki</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <header class="bg-white shadow-sm">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4">
            <a href="{{ url('/') }}" class="font-bold text-indigo-600">Voltar para a Wiki</a>
            <x-suggestion-notifications />
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-10">
        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Caixa de entrada</p>
            <h1 class="text-3xl font-bold text-gray-900">Sugestões e conversas</h1>
            <p class="mt-2 text-gray-600">As conversas são privadas entre os participantes e administradores.</p>
        </div>

        <div class="space-y-4">
            @forelse($notifications as $notification)
                @php($rootSuggestion = $notification->suggestion->parent ?? $notification->suggestion)
                <a href="{{ route('suggestions.show', [$rootSuggestion->post->slug, $rootSuggestion->id]) }}" class="block rounded-xl border border-gray-100 bg-white p-6 shadow-sm {{ $notification->read_at ? '' : 'ring-2 ring-indigo-100' }}">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="font-bold text-gray-900">{{ $rootSuggestion->post->title }}</h2>
                                @if(!$notification->read_at)
                                    <span class="rounded-full bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-700">Nova</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Mensagem de {{ $notification->suggestion->user->name ?? 'usuário removido' }} em {{ $notification->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="text-sm font-medium text-indigo-600">Abrir conversa</span>
                    </div>
                    <p class="mt-4 line-clamp-2 text-gray-700">{{ $notification->suggestion->content }}</p>
                </a>
            @empty
                <div class="rounded-xl bg-white p-10 text-center shadow-sm">
                    <p class="text-lg text-gray-700">Nenhuma sugestão recebida.</p>
                    <p class="mt-1 text-sm text-gray-500">Quando alguém sugerir uma melhoria, ela aparecerá aqui.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $notifications->links() }}</div>
    </main>
</body>
</html>