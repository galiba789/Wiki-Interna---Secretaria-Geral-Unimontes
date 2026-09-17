@auth
    <a href="{{ route('suggestions.index') }}" class="relative inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600" title="Notificações de sugestões">
        <span class="text-lg" aria-hidden="true">&#128276;</span>
        <span>Notificações</span>
        @if($unreadSuggestionCount > 0)
            <span class="ml-1 inline-flex min-w-5 items-center justify-center rounded-full bg-red-600 px-1.5 py-0.5 text-xs font-bold text-white">{{ $unreadSuggestionCount }}</span>
        @endif
    </a>
@endauth