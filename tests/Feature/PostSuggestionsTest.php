<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\PostSuggestion;
use App\Models\SuggestionNotification;
use App\Models\User;

function createSuggestionPost(User $owner): Post
{
    $category = Category::create([
        'name' => 'Processos',
        'slug' => 'processos-'.uniqid(),
    ]);

    return Post::create([
        'title' => 'Processo institucional',
        'slug' => 'processo-institucional-'.uniqid(),
        'content' => '<p>Conteúdo atual</p>',
        'user_id' => $owner->id,
        'category_id' => $category->id,
    ]);
}

test('users can send private suggestions and the post owner receives a notification', function () {
    $owner = User::factory()->create();
    $author = User::factory()->create();
    $post = createSuggestionPost($owner);

    $response = actingAs($author)->post(route('suggestions.store', $post->slug), [
        'content' => 'Inclua um exemplo neste processo.',
    ]);

    $response->assertRedirect();
    $suggestion = PostSuggestion::firstOrFail();

    expect($suggestion->content)->toBe('Inclua um exemplo neste processo.');
    $this->assertDatabaseHas('suggestion_notifications', [
        'suggestion_id' => $suggestion->id,
        'user_id' => $owner->id,
        'read_at' => null,
    ]);
});

test('sending another suggestion for the same post continues the open conversation', function () {
    $owner = User::factory()->create();
    $author = User::factory()->create();
    $post = createSuggestionPost($owner);

    actingAs($author)->post(route('suggestions.store', $post->slug), [
        'content' => 'Primeira mensagem.',
    ])->assertRedirect();

    actingAs($author)->post(route('suggestions.store', $post->slug), [
        'content' => 'Mensagem complementar.',
    ])->assertRedirect();

    expect(PostSuggestion::where('post_id', $post->id)->whereNull('parent_id')->count())->toBe(1)
        ->and(PostSuggestion::where('post_id', $post->id)->whereNotNull('parent_id')->count())->toBe(1);

    $reply = PostSuggestion::whereNotNull('parent_id')->firstOrFail();

    expect($reply->parent_id)->toBe(PostSuggestion::whereNull('parent_id')->value('id'));
});

test('private suggestion conversations are limited to participants and administrators', function () {
    $owner = User::factory()->create();
    $author = User::factory()->create();
    $outsider = User::factory()->create();
    $admin = User::factory()->create();
    $admin->forceFill(['is_admin' => true])->save();
    $post = createSuggestionPost($owner);
    $suggestion = PostSuggestion::create([
        'post_id' => $post->id,
        'user_id' => $author->id,
        'content' => 'Sugestão privada.',
    ]);

    actingAs($outsider)->get(route('suggestions.show', [$post->slug, $suggestion->id]))->assertForbidden();
    actingAs($owner)->get(route('suggestions.show', [$post->slug, $suggestion->id]))->assertSee('Sugestão privada.');
    actingAs($admin)->get(route('suggestions.show', [$post->slug, $suggestion->id]))->assertSee('Sugestão privada.');
});

test('the post owner can reply and pin multiple private messages publicly', function () {
    $owner = User::factory()->create();
    $author = User::factory()->create();
    $post = createSuggestionPost($owner);
    $suggestion = PostSuggestion::create([
        'post_id' => $post->id,
        'user_id' => $author->id,
        'content' => 'Sugestão original.',
    ]);

    actingAs($owner)->post(route('suggestions.reply', $suggestion->id), [
        'content' => 'Resposta detalhada.',
    ])->assertRedirect();

    $reply = $suggestion->replies()->firstOrFail();
    actingAs($owner)->post(route('suggestions.pin', $suggestion->id))->assertRedirect();
    actingAs($owner)->post(route('suggestions.pin', $reply->id))->assertRedirect();

    expect(PostSuggestion::where('is_pinned', true)->count())->toBe(2);
    actingAs($outsider = User::factory()->create())->get(route('posts.show', $post->slug))
        ->assertSee('Sugestão original.')
        ->assertSee('Resposta detalhada.');
});

test('reading a conversation marks the current user notifications as read', function () {
    $owner = User::factory()->create();
    $author = User::factory()->create();
    $post = createSuggestionPost($owner);
    $suggestion = PostSuggestion::create([
        'post_id' => $post->id,
        'user_id' => $author->id,
        'content' => 'Sugestão para leitura.',
    ]);
    $notification = SuggestionNotification::create([
        'suggestion_id' => $suggestion->id,
        'user_id' => $owner->id,
    ]);

    actingAs($owner)->get(route('suggestions.show', [$post->slug, $suggestion->id]))->assertOk();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('administrators see one notification for each conversation', function () {
    $owner = User::factory()->create();
    $author = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $post = createSuggestionPost($owner);
    $suggestion = PostSuggestion::create([
        'post_id' => $post->id,
        'user_id' => $author->id,
        'content' => 'Sugestão original.',
    ]);

    foreach ([$owner, $author, $admin] as $recipient) {
        SuggestionNotification::create([
            'suggestion_id' => $suggestion->id,
            'user_id' => $recipient->id,
        ]);
    }

    $reply = $suggestion->replies()->create([
        'post_id' => $post->id,
        'user_id' => $owner->id,
        'content' => 'Resposta do responsável.',
    ]);
    foreach ([$owner, $author, $admin] as $recipient) {
        SuggestionNotification::create([
            'suggestion_id' => $reply->id,
            'user_id' => $recipient->id,
        ]);
    }

    $response = actingAs($admin)->get(route('suggestions.index'));

    $response->assertOk()
        ->assertSee('Resposta do responsável.')
        ->assertDontSee('Sugestão original.');

    expect(substr_count($response->getContent(), 'Abrir conversa'))->toBe(1);
});
