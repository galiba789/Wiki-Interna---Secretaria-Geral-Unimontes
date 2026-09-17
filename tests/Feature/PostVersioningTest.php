<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\PostVersion;
use App\Models\User;

test('updating a post preserves the previous version and creates a new one', function () {
    $user = User::factory()->create();
    $user->forceFill(['is_editor' => true])->save();
    $category = Category::create(['name' => 'Processos', 'slug' => 'processos']);
    $post = Post::create([
        'title' => 'Versão inicial',
        'slug' => 'versao-inicial',
        'content' => '<p>Conteúdo antigo</p>',
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);
    PostVersion::create([
        'post_id' => $post->id,
        'user_id' => $user->id,
        'category_id' => $category->id,
        'version_number' => 1,
        'title' => 'Versão inicial',
        'content' => '<p>Conteúdo antigo</p>',
    ]);

    $response = actingAs($user)->put(route('posts.update', $post->id), [
        'title' => 'Versão atualizada',
        'category_id' => $category->id,
        'content' => '<p>Conteúdo novo</p>',
    ]);

    $response->assertRedirect(route('posts.show', $post->slug));
    $post->refresh();
    $versions = $post->versions()->get();

    expect($post->title)->toBe('Versão atualizada')
        ->and($versions)->toHaveCount(2)
        ->and($versions->first()->title)->toBe('Versão atualizada')
        ->and($versions->last()->title)->toBe('Versão inicial');
});

test('restoring a version creates a new immutable version', function () {
    $user = User::factory()->create();
    $user->forceFill(['is_editor' => true])->save();
    $category = Category::create(['name' => 'Processos', 'slug' => 'processos']);
    $post = Post::create([
        'title' => 'Versão atual',
        'slug' => 'versao-atual',
        'content' => '<p>Conteúdo atual</p>',
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);
    $version = PostVersion::create([
        'post_id' => $post->id,
        'user_id' => $user->id,
        'category_id' => $category->id,
        'version_number' => 1,
        'title' => 'Versão antiga',
        'content' => '<p>Conteúdo antigo</p>',
    ]);

    $response = actingAs($user)->post(route('posts.versions.restore', [$post->slug, $version->id]));

    $response->assertRedirect(route('posts.show', $post->slug));
    $post->refresh();
    $newVersion = $post->versions()->first();

    expect($post->title)->toBe('Versão antiga')
        ->and($newVersion->version_number)->toBe(2)
        ->and($newVersion->change_summary)->toBe('Restaurada a versão 1');
});

test('post owners can archive and unarchive a post', function () {
    $user = User::factory()->create();
    $user->forceFill(['is_editor' => true])->save();
    $category = Category::create(['name' => 'Processos', 'slug' => 'processos']);
    $post = Post::create([
        'title' => 'Processo antigo',
        'slug' => 'processo-antigo',
        'content' => '<p>Conteúdo</p>',
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $archiveResponse = actingAs($user)->post(route('posts.archive', $post->slug));

    $archiveResponse->assertRedirect();
    expect($post->fresh()->status)->toBe('archived');

    $unarchiveResponse = actingAs($user)->post(route('posts.unarchive', $post->slug));

    $unarchiveResponse->assertRedirect();
    expect($post->fresh()->status)->toBe('published');
});
