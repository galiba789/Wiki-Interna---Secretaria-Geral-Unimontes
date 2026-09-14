<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;

class PostController extends Controller
{
    public function create()
    {
        if (!auth()->user()->is_admin && !auth()->user()->is_editor) {
            abort(403, 'Acesso restrito. Você tem permissão apenas de leitura.');
        }

        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->is_admin && !auth()->user()->is_editor) {
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
            $finalContent = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $finalContent));
            
            unlink($tempFile);
        }

      // Salvamos o post em uma variável chamada $post
        $post = Post::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . uniqid(),
            'content' => $finalContent,
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
        ]);

        // Ao invés de redirecionar para o admin.dashboard, mandamos para a rota do post
        return redirect()->route('posts.show', $post->slug)->with('success', 'Tutorial publicado com sucesso!');
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }
    public function edit(Post $post)
    {
        // Trava de segurança: Só o dono do post ou um Admin podem editar
        if (auth()->id() !== $post->user_id && !auth()->user()->is_admin) {
            abort(403, 'Você não tem permissão para editar este tutorial.');
        }

        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        // Trava de segurança no envio dos dados
        if (auth()->id() !== $post->user_id && !auth()->user()->is_admin) {
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
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($request->file('word_file')->path());
            $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
            
            $tempFile = tempnam(sys_get_temp_dir(), 'word_html');
            $htmlWriter->save($tempFile);
            $finalContent = file_get_contents($tempFile);
            
            $finalContent = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $finalContent));
            
            unlink($tempFile);
        }

        // Atualiza os dados no banco (Não mudamos o slug para não quebrar o link antigo)
        $post->update([
            'title' => $request->title,
            'content' => $finalContent,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('posts.show', $post->slug)->with('success', 'Tutorial atualizado com sucesso!');
    }
}