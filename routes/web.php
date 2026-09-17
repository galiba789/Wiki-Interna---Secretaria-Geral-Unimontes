<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        $query = Post::with(['category', 'user'])->where('status', 'published')->latest();

        if (request('search')) {
            $query->where('title', 'like', '%'.request('search').'%')
                ->orWhere('content', 'like', '%'.request('search').'%');
        }

        if (request('category')) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', request('category'));
            });
        }

        // Paginação de 10 em 10, mantendo os filtros na URL
        $posts = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('welcome', compact('posts', 'categories'));
    })->name('home');
});

Route::get('/dashboard', function () {
    // Se o usuário logado for admin, manda para o painel admin
    if (auth()->user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    // Se for um servidor comum, manda para a Home da Wiki
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');
use App\Http\Controllers\SuggestionController;

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [SuggestionController::class, 'index'])->name('suggestions.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas para usuários logados (Servidores criarem e editarem posts)
Route::middleware(['auth'])->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

    // Novas rotas de edição
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::get('/posts/archived', [PostController::class, 'archived'])->name('posts.archived');
    Route::post('/posts/{post:slug}/suggestions', [SuggestionController::class, 'store'])->name('suggestions.store');
    Route::get('/posts/{post:slug}/suggestions/{suggestion}', [SuggestionController::class, 'show'])->name('suggestions.show');
    Route::post('/suggestions/{suggestion}/reply', [SuggestionController::class, 'reply'])->name('suggestions.reply');
    Route::post('/suggestions/{suggestion}/ignore', [SuggestionController::class, 'ignore'])->name('suggestions.ignore');
    Route::post('/suggestions/{suggestion}/pin', [SuggestionController::class, 'pin'])->name('suggestions.pin');
    Route::post('/suggestions/{suggestion}/unpin', [SuggestionController::class, 'unpin'])->name('suggestions.unpin');
    Route::get('/posts/{post:slug}/versions', [PostController::class, 'versions'])->name('posts.versions');
    Route::get('/posts/{post:slug}/versions/{version}', [PostController::class, 'compare'])->name('posts.versions.compare');
    Route::post('/posts/{post:slug}/versions/{version}/restore', [PostController::class, 'restore'])->name('posts.versions.restore');
    Route::post('/posts/{post:slug}/archive', [PostController::class, 'archive'])->name('posts.archive');
    Route::post('/posts/{post:slug}/unarchive', [PostController::class, 'unarchive'])->name('posts.unarchive');
    Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
});

// Rotas públicas de posts

// Rotas do Admin (Gerenciar categorias e exclusões)
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Usuários
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

    // Categorias
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [AdminController::class, 'editCategory'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('admin.categories.destroy');

    // Posts
    Route::delete('/posts/{post}', [AdminController::class, 'destroyPost'])->name('admin.posts.destroy');
});

require __DIR__.'/auth.php';
