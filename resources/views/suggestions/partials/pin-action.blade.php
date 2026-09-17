@if(auth()->user()->is_admin || auth()->id() === $post->user_id)
    <form method="POST" action="{{ route($suggestionMessage->is_pinned ? 'suggestions.unpin' : 'suggestions.pin', $suggestionMessage->id) }}" class="mt-4">
        @csrf
        <button type="submit" class="text-xs font-medium {{ $suggestionMessage->is_pinned ? 'text-gray-600' : 'text-emerald-700' }} hover:underline">
            {{ $suggestionMessage->is_pinned ? 'Remover do post público' : 'Fixar no post público' }}
        </button>
    </form>
@endif