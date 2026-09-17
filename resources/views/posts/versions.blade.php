<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Histórico - {{ $post->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <header class="bg-white shadow-sm">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('posts.show', $post->slug) }}" class="font-bold text-indigo-600">Voltar para o tutorial</a>
            <div class="flex items-center gap-4"><x-suggestion-notifications /><span class="text-sm text-gray-500">{{ $post->title }}</span></div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-10">
        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Rastreabilidade</p>
            <h1 class="text-3xl font-bold text-gray-900">Histórico de versões</h1>
            <p class="mt-2 text-gray-600">Cada alteração fica preservada para consulta e auditoria.</p>
        </div>

        <div class="space-y-4">
            @foreach($versions as $version)
                <article class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="font-bold text-gray-900">Versão {{ $version->version_number }}</h2>
                            <span class="text-xs text-gray-500">{{ $version->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ $version->change_summary ?: 'Alteração no tutorial' }} por {{ $version->user->name ?? 'usuário removido' }}</p>
                    </div>
                    <a href="{{ route('posts.versions.compare', [$post->slug, $version->id]) }}" class="text-sm font-medium text-indigo-600 hover:underline">Comparar com a versão atual</a>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $versions->links() }}</div>
    </main>
</body>
</html>