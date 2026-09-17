<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Tutorial - Wiki</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#content-editor',
        plugins: 'lists link table code',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | table link code',
        language: 'pt_BR',
        menubar: false
      });
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <header class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('posts.show', $post->slug) }}" class="font-bold text-indigo-600">← Voltar para o Tutorial</a>
            <x-theme-toggle />
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-10">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Processo: {{ $post->title }}</h1>

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                    <strong class="font-bold">Atenção!</strong>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título do Processo</label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                    <select name="category_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $post->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-lg">
                    <label class="block text-sm font-bold text-indigo-800 mb-2">Substituir conteúdo por arquivo Word (Opcional)</label>
                    <p class="text-xs text-indigo-600 mb-3">Se você enviar um documento novo aqui, ele <strong>apagará</strong> o texto abaixo e extrairá o texto do novo Word.</p>
                    <input type="file" name="word_file" accept=".doc,.docx" class="text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Editor de Texto</label>
                    <textarea id="content-editor" name="content" rows="20" class="w-full px-4 py-2 border rounded-lg">{{ old('content', $post->content) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Atualizar Tutorial</button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>