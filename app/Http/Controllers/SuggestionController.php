<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostSuggestion;
use App\Models\SuggestionNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuggestionController extends Controller
{
    public function index(): View
    {
        $isAdmin = auth()->user()->is_admin;
        $latestNotificationIds = SuggestionNotification::query()
            ->selectRaw('MAX(suggestion_notifications.id)')
            ->join('post_suggestions as grouped_suggestions', 'grouped_suggestions.id', '=', 'suggestion_notifications.suggestion_id')
            ->groupByRaw('COALESCE(grouped_suggestions.parent_id, grouped_suggestions.id)');

        if (! $isAdmin) {
            $latestNotificationIds->where('suggestion_notifications.user_id', auth()->id());
        }

        $notificationsQuery = SuggestionNotification::query()
            ->with(['suggestion.post', 'suggestion.user', 'suggestion.parent'])
            ->whereIn('suggestion_notifications.id', $latestNotificationIds)
            ->latest();

        if (! $isAdmin) {
            $notificationsQuery->where('user_id', auth()->id());
        }

        $notifications = $notificationsQuery->paginate(15);

        return view('suggestions.index', compact('notifications'));
    }

    public function store(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ], [
            'content.required' => 'Escreva a sugestão antes de enviar.',
            'content.max' => 'A sugestão deve ter no máximo 5.000 caracteres.',
        ]);

        $openConversation = $post->suggestions()
            ->whereNull('parent_id')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();

        if ($openConversation !== null) {
            $reply = $openConversation->replies()->create([
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'content' => $validated['content'],
            ]);

            $this->notifyParticipants($reply, $openConversation);

            return back()->with('success', 'Resposta enviada em privado.');
        }

        $suggestion = $post->suggestions()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        $this->notifyParticipants($suggestion);

        return back()->with('success', 'Sugestão enviada em privado ao criador do post.');
    }

    public function show(Post $post, PostSuggestion $suggestion): View
    {
        $this->ensureRootBelongsToPost($post, $suggestion);
        $this->authorizeThread($suggestion);

        $this->markThreadAsRead($suggestion);
        $suggestion->load(['user', 'replies.user']);

        return view('suggestions.show', compact('post', 'suggestion'));
    }

    public function reply(Request $request, PostSuggestion $suggestion): RedirectResponse
    {
        $this->authorizeThread($suggestion);

        if ($suggestion->parent_id !== null) {
            abort(404);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ], [
            'content.required' => 'Escreva a resposta antes de enviar.',
            'content.max' => 'A resposta deve ter no máximo 5.000 caracteres.',
        ]);

        $reply = $suggestion->replies()->create([
            'post_id' => $suggestion->post_id,
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        $this->notifyParticipants($reply, $suggestion);

        return back()->with('success', 'Resposta enviada em privado.');
    }

    public function ignore(PostSuggestion $suggestion): RedirectResponse
    {
        $this->authorizePostOwner($suggestion->post);

        if ($suggestion->parent_id !== null) {
            abort(404);
        }

        $suggestion->update(['status' => 'ignored']);

        return back()->with('success', 'Sugestão ignorada. A conversa continua disponível no histórico.');
    }

    public function pin(PostSuggestion $suggestion): RedirectResponse
    {
        $this->authorizePostOwner($suggestion->post);
        $suggestion->update(['is_pinned' => true]);

        return back()->with('success', 'Comentário fixado publicamente no post.');
    }

    public function unpin(PostSuggestion $suggestion): RedirectResponse
    {
        $this->authorizePostOwner($suggestion->post);
        $suggestion->update(['is_pinned' => false]);

        return back()->with('success', 'Comentário removido da área pública do post.');
    }

    private function authorizeThread(PostSuggestion $suggestion): void
    {
        $userId = auth()->id();
        $isParticipant = $suggestion->parent_id === null
            ? $suggestion->user_id === $userId
            : $suggestion->parent()->where('user_id', $userId)->exists();

        $hasReplied = $suggestion->replies()->where('user_id', $userId)->exists();
        $isPostOwner = $suggestion->post()->where('user_id', $userId)->exists();

        abort_unless(auth()->user()->is_admin || $isParticipant || $hasReplied || $isPostOwner, 403);
    }

    private function authorizePostOwner(Post $post): void
    {
        abort_unless(auth()->user()->is_admin || $post->user_id === auth()->id(), 403);
    }

    private function ensureRootBelongsToPost(Post $post, PostSuggestion $suggestion): void
    {
        abort_unless($suggestion->post_id === $post->id && $suggestion->parent_id === null, 404);
    }

    private function notifyParticipants(PostSuggestion $suggestion, ?PostSuggestion $root = null): void
    {
        $root ??= $suggestion->parent ?? $suggestion;
        $recipientIds = collect([$root->user_id, $root->post->user_id])
            ->merge(User::where('is_admin', true)->pluck('id'))
            ->unique()
            ->reject(fn (int $userId): bool => $userId === auth()->id());

        $recipientIds->each(function (int $userId) use ($suggestion): void {
            SuggestionNotification::firstOrCreate([
                'suggestion_id' => $suggestion->id,
                'user_id' => $userId,
            ]);
        });
    }

    private function markThreadAsRead(PostSuggestion $root): void
    {
        $suggestionIds = $root->replies()->pluck('id')->push($root->id);

        SuggestionNotification::whereIn('suggestion_id', $suggestionIds)
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
