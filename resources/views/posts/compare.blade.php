<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comparar versões - {{ $post->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <header class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('posts.versions', $post->slug) }}" class="font-bold text-indigo-600">Voltar ao histórico</a>
            <div class="flex items-center gap-4"><x-suggestion-notifications /><span class="text-sm text-gray-500">{{ $post->title }}</span></div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between mb-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Comparação</p>
                <h1 class="text-3xl font-bold text-gray-900">Versão {{ $version->version_number }} e versão atual</h1>
                <p class="mt-2 text-gray-600">A versão à esquerda é um snapshot preservado; a direita representa o conteúdo publicado.</p>
            </div>
            @if(auth()->user()->is_admin || auth()->id() === $post->user_id)
                <form method="POST" action="{{ route('posts.versions.restore', [$post->slug, $version->id]) }}" onsubmit="return confirm('Restaurar esta versão criará uma nova versão do post. Continuar?');">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">Restaurar versão {{ $version->version_number }}</button>
                </form>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <article class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold border-b pb-4 mb-6">Versão {{ $version->version_number }}</h2>
                <h3 class="text-2xl font-bold mb-4">{{ $version->title }}</h3>
                <div class="prose max-w-none text-gray-700">{!! $version->content !!}</div>
            </article>
            <article class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold border-b pb-4 mb-6">Versão atual</h2>
                <h3 class="text-2xl font-bold mb-4">{{ $currentVersion?->title ?? $post->title }}</h3>
                <div class="prose max-w-none text-gray-700">{!! ($currentVersion?->content ?? $post->content) !!}</div>
            </article>
        </div>
    </main>
</body>
</html>