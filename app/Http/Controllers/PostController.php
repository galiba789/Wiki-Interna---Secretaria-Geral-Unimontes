<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpWord\IOFactory;

class PostController extends Controller
{
    public function create(): View
    {
        if (! auth()->user()->is_admin && ! auth()->user()->is_editor) {
            abort(403, 'Acesso restrito. Você tem permissão apenas de leitura.');
        }

        $categories = Category::all();

        return view('posts.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (! auth()->user()->is_admin && ! auth()->user()->is_editor) {
            abort(403, 'Acesso restrito. Você tem permissão apenas de leitura.');
        }
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            // O conteúdo só é obrigatório se o arquivo Word NÃO for enviado
            'content' => ['required_without:word_file', 'nullable', 'string'],
            // Valida para aceitar apenas arquivos Word
            'word_file' => ['nullable', 'file', 'mimes:docx,doc', 'max:10240'],
        ], [
            'title.required' => 'O título do tutorial é obrigatório.',
            'category_id.required' => 'Selecione uma categoria.',
            'content.required_without' => 'Você precisa digitar o conteúdo ou anexar um arquivo Word.',
            'word_file.mimes' => 'O arquivo precisa ser do formato Word (.doc ou .docx).',
            'word_file.max' => 'O arquivo Word não pode ter mais de 10MB.',
        ]);

        $finalContent = $request->content;

        // Se o usuário fez upload de um arquivo Word, converte para HTML
        if ($request->hasFile('word_file')) {
            $phpWord = IOFactory::load($request->file('word_file')->path());
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

            // Salva o HTML gerado em um arquivo temporário e pega o conteúdo
            $tempFile = tempnam(sys_get_temp_dir(), 'word_html');
            $htmlWriter->save($tempFile);
            $finalContent = file_get_contents($tempFile);

            // Remove as tags base do HTML do Word para não quebrar o layout do nosso site
            $finalContent = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<html>', '</html>', '<body>', '</body>'], ['', '', '', ''], $finalContent));

            unlink($tempFile);
        }

        $post = DB::transaction(function () use ($request, $finalContent): Post {
            $post = Post::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title).'-'.uniqid(),
                'content' => $finalContent,
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,
            ]);

            $this->createVersion($post, 'Versão inicial');

            return $post;
        });

        // Ao invés de redirecionar para o admin.dashboard, mandamos para a rota do post
        return redirect()->route('posts.show', $post->slug)->with('success', 'Tutorial publicado com sucesso!');
    }

    public function show(Post $post): View
    {
        $pinnedSuggestions = $post->pinnedSuggestions()->with('user')->get();

        return view('posts.show', compact('post', 'pinnedSuggestions'));
    }

    public function archived(Request $request): View
    {
        abort_unless(auth()->user()->is_admin || auth()->user()->is_editor, 403);

        $posts = Post::with(['category', 'user'])
            ->where('status', 'archived')
            ->latest('archived_at')
            ->paginate(10)
            ->withQueryString();
        $categories = Category::all();

        return view('welcome', ['posts' => $posts, 'categories' => $categories, 'archived' => true]);
    }

    public function edit(Post $post): View
    {
        // Trava de segurança: Só o dono do post ou um Admin podem editar
        if (auth()->id() !== $post->user_id && ! auth()->user()->is_admin) {
            abort(403, 'Você não tem permissão para editar este tutorial.');
        }

        $categories = Category::all();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        // Trava de segurança no envio dos dados
        if (auth()->id() !== $post->user_id && ! auth()->user()->is_admin) {
            abort(403, 'Você não tem permissão para editar este tutorial.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'content' => ['required_without:word_file', 'nullable', 'string'],
            'word_file' => ['nullable', 'file', 'mimes:docx,doc', 'max:10240'],
        ]);

        $finalContent = $request->content;

        // Se o usuário subir um Word novo na edição, ele substitui o texto atual
        if ($request->hasFile('word_file')) {
            $phpWord = IOFactory::load($request->file('word_file')->path());
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

            $tempFile = tempnam(sys_get_temp_dir(), 'word_html');
            $htmlWriter->save($tempFile);
            $finalContent = file_get_contents($tempFile);

            $finalContent = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<html>', '</html>', '<body>', '</body>'], ['', '', '', ''], $finalContent));

            unlink($tempFile);
        }

        DB::transaction(function () use ($post, $request, $finalContent): void {
            $post->update([
                'title' => $request->title,
                'content' => $finalContent,
                'category_id' => $request->category_id,
            ]);

            $this->createVersion($post, 'Atualização do tutorial');
        });

        return redirect()->route('posts.show', $post->slug)->with('success', 'Tutorial atualizado com sucesso!');
    }

    public function versions(Post $post): View
    {
        $versions = $post->versions()->with(['user', 'category'])->paginate(10);

        return view('posts.versions', compact('post', 'versions'));
    }

    public function compare(Post $post, PostVersion $version): View
    {
        abort_unless($version->post_id === $post->id, 404);

        $currentVersion = $post->versions()->first();

        return view('posts.compare', compact('post', 'version', 'currentVersion'));
    }

    public function restore(Post $post, PostVersion $version): RedirectResponse
    {
        abort_unless($version->post_id === $post->id, 404);

        if (auth()->id() !== $post->user_id && ! auth()->user()->is_admin) {
            abort(403, 'Você não tem permissão para restaurar este tutorial.');
        }

        DB::transaction(function () use ($post, $version): void {
            $post->update([
                'title' => $version->title,
                'content' => $version->content,
                'category_id' => $version->category_id,
            ]);

            $this->createVersion($post, 'Restaurada a versão '.$version->version_number);
        });

        return redirect()->route('posts.show', $post->slug)->with('success', 'Versão restaurada com sucesso.');
    }

    public function archive(Post $post): RedirectResponse
    {
        $this->authorizePostManagement($post);

        $post->update([
            'status' => 'archived',
            'archived_at' => now(),
            'archived_by' => auth()->id(),
        ]);

        return back()->with('success', 'Tutorial arquivado com sucesso.');
    }

    public function unarchive(Post $post): RedirectResponse
    {
        $this->authorizePostManagement($post);

        $post->update([
            'status' => 'published',
            'archived_at' => null,
            'archived_by' => null,
        ]);

        return back()->with('success', 'Tutorial desarquivado com sucesso.');
    }

    private function createVersion(Post $post, string $changeSummary): PostVersion
    {
        $versionNumber = ((int) $post->versions()->max('version_number')) + 1;

        return $post->versions()->create([
            'user_id' => auth()->id(),
            'category_id' => $post->category_id,
            'version_number' => $versionNumber,
            'title' => $post->title,
            'content' => $post->content,
            'change_summary' => $changeSummary,
        ]);
    }

    private function authorizePostManagement(Post $post): void
    {
        if (auth()->id() !== $post->user_id && ! auth()->user()->is_admin) {
            abort(403, 'Você não tem permissão para gerenciar este tutorial.');
        }
    }
}
