<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PostController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        $query = Post::with(['category', 'user'])->latest();
        
        if (request('search')) {
            $query->where('title', 'like', '%' . request('search') . '%')
                  ->orWhere('content', 'like', '%' . request('search') . '%');
        }
        
        if (request('category')) {
            $query->whereHas('category', function($q) {
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

Route::middleware('auth')->group(function () {
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
    Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
});

// Rotas públicas de posts



// Rotas do Admin (Gerenciar categorias e exclusões)
Route::middleware(['auth', App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
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
