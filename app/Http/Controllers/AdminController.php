<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        // Paginação de 5 em 5, cada um com seu próprio parâmetro na URL
        $users = User::paginate(5, ['*'], 'users_page');
        
        $categories = Category::paginate(5, ['*'], 'categories_page');
        
        $posts = Post::with(['user', 'category'])
                    ->latest()
                    ->paginate(5, ['*'], 'posts_page');
        
        return view('admin.dashboard', compact('users', 'categories', 'posts'));
    }

    public function createUser()
    {
        return view('admin.users-create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Insira um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado no sistema.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'name.required' => 'O campo nome é obrigatório.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->role === 'admin' ? 1 : 0,
            'is_editor' => $request->role === 'editor' ? 1 : 0,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Usuário cadastrado com sucesso!');
    }

        public function editUser(User $user)
    {
        return view('admin.users-edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8'],
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Insira um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado por outro usuário.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'name.required' => 'O campo nome é obrigatório.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->role === 'admin' ? 1 : 0,
            'is_editor' => $request->role === 'editor' ? 1 : 0,
        ];

        // Só atualiza a senha se o admin preencheu o campo
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroyUser(User $user)
    {
        // Evita que o admin delete a si mesmo
        if ($user->id === auth()->id()) {
            back()->with('error', 'Você não pode excluir sua própria conta.');
        }

        $user->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Usuário deletado com sucesso!');
    }

    public function destroyPost(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Post deletado com sucesso!');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
        ], [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Esta categoria já existe.',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Categoria criada com sucesso!');
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Categoria deletada com sucesso!');
    }
    public function editCategory(Category $category)
    {
        return view('admin.categories-edit', compact('category'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
        ], [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome.',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Categoria atualizada com sucesso!');
    }
}