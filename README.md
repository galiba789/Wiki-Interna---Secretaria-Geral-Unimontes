# Wiki Sec

> Conhecimento compartilhado para resolver demandas, encontrar respostas e trabalhar melhor.

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3 ou superior">
  <img src="https://img.shields.io/badge/Status-em%20desenvolvimento-F59E0B?style=for-the-badge" alt="Status: em desenvolvimento">
</p>

---

## 📖 Sobre o projeto

O **Wiki Sec** é uma wiki interna criada para centralizar conhecimento e facilitar o dia a dia da equipe.

A plataforma reúne posts com tutoriais, orientações e respostas para ajudar outros leitores a resolver demandas ou simplesmente tirar dúvidas.

A ideia é transformar soluções individuais em conhecimento acessível para todos: quando uma dúvida é resolvida, a resposta pode virar um post útil para a próxima pessoa.

## 👥 Tipos de usuário

| Perfil            | Responsabilidade                                                                            |
| ----------------- | ------------------------------------------------------------------------------------------- |
| **Leitor**        | Consulta os posts, pesquisa informações e filtra conteúdos por categoria.                   |
| **Editor**        | Cria e edita posts para compartilhar tutoriais, soluções e orientações.                     |
| **Administrador** | Gerencia usuários, categorias e conteúdos da wiki, além de possuir as permissões de edição. |

## 🚀 Recursos principais

* 🔐 Autenticação e gerenciamento de perfil
* 📄 Listagem de posts com paginação
* 🔎 Busca por título e conteúdo
* 🗂️ Filtro por categoria
* ✏️ Criação e edição de posts por editores e administradores
* 📚 Organização do conhecimento por categorias
* ⚙️ Painel administrativo para gerenciar usuários, categorias e posts
* 🕘 Versionamento completo dos posts
* 🔁 Comparação e restauração de versões anteriores
* 📦 Arquivamento de processos antigos
* 💡 Sugestões privadas sobre os posts
* 🔔 Notificações de novas sugestões e respostas
* 📌 Fixação pública de sugestões e respostas relevantes
* 🌙 Tema escuro com preferência persistente no navegador

## 🕘 Versionamento e arquivamento

Cada post possui um histórico de versões. Quando um editor ou administrador atualiza um conteúdo, a versão anterior é preservada automaticamente, incluindo título, conteúdo, categoria, responsável e data da alteração.

Na tela de histórico é possível:

* consultar todas as versões de um post;
* comparar uma versão antiga com a versão atual;
* restaurar uma versão anterior sem apagar o histórico existente.

A restauração gera uma nova versão, mantendo a trilha completa de alterações. Posts que não são mais utilizados podem ser arquivados e consultados separadamente, sem aparecer na listagem principal da Wiki.

## 💡 Sugestões e conversas privadas

Qualquer usuário autenticado pode enviar uma sugestão privada ao criador de um post. A conversa fica disponível somente para:

* o autor da sugestão;
* o criador do post;
* usuários que participarem da conversa;
* administradores.

O criador pode responder, ignorar a sugestão ou fixar uma mensagem no post. Mensagens fixadas tornam-se públicas para todos os leitores, e mais de uma sugestão ou resposta pode ser fixada ao mesmo tempo.

As novas mensagens aparecem na área de notificações, com indicação de itens não lidos. Ao abrir a conversa, as notificações correspondentes são marcadas como lidas.

## 🌙 Tema escuro

O sistema possui um botão de alternância entre os temas claro e escuro. A escolha é salva no navegador e permanece entre as sessões. Na primeira visita, o sistema considera a preferência de tema configurada no dispositivo.

## 🛠️ Tecnologias

* [Laravel 13](https://laravel.com/) e PHP 8.3+
* [Laravel Breeze](https://laravel.com/docs/starter-kits) para autenticação
* Blade e Tailwind CSS
* Vite e Alpine.js
* Pest para testes automatizados
* PHPWord para recursos relacionados a documentos

## 💻 Como executar localmente

### Pré-requisitos

Antes de começar, certifique-se de possuir:

* PHP 8.3 ou superior
* Composer
* Node.js e npm
* Banco de dados compatível com a configuração do projeto

### Instalação

Clone o repositório e entre na pasta do projeto:

```bash
git clone https://github.com/galiba789/Wiki-Interna---Secretaria-Geral-Unimontes.git
cd wiki_sec
```

Instale as dependências do PHP:

```bash
composer install
```

Configure o arquivo `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

Configure as credenciais do banco de dados no arquivo `.env`.

Em seguida, execute as migrations:

```bash
php artisan migrate
```

As migrations também criam as tabelas de histórico de versões, sugestões privadas e notificações. Em uma instalação já existente, execute o mesmo comando para aplicar somente as migrations pendentes:

```bash
php artisan migrate
php artisan migrate:status
```

Instale as dependências do frontend:

```bash
npm install
```

Compile os arquivos:

```bash
npm run build
```

### 🔧 Ambiente de desenvolvimento

Para iniciar a aplicação com os processos de desenvolvimento:

```bash
composer run dev
```

A aplicação ficará disponível no endereço informado pelo servidor local.

### 🧪 Testes

Para executar os testes automatizados:

```bash
php artisan test --compact
```

## 🔄 Fluxo da wiki

1. O **leitor** acessa a wiki e encontra um post por meio da busca ou das categorias.
2. Ao identificar uma solução ou orientação útil, qualquer usuário pode enviar uma sugestão privada ao criador do post.
3. O criador pode responder, ignorar ou fixar a sugestão para torná-la pública.
4. Ao atualizar um post, o sistema preserva automaticamente a versão anterior para consulta, comparação e restauração.
5. Um **editor** transforma soluções consolidadas em conteúdo reutilizável, enquanto o **administrador** mantém usuários, categorias e posts organizados.

## 📁 Estrutura do projeto

```text
app/                 Código da aplicação
database/            Migrations, factories e seeders
resources/views/     Interfaces Blade
routes/              Rotas web e de autenticação
tests/               Testes automatizados
```

As principais estruturas relacionadas às novas funcionalidades são:

```text
app/Models/PostVersion.php                 Histórico de versões dos posts
app/Models/PostSuggestion.php              Sugestões e respostas privadas
app/Models/SuggestionNotification.php      Notificações das conversas
app/Http/Controllers/SuggestionController.php  Fluxo de sugestões
resources/views/suggestions/               Caixa de entrada e conversas
```

## 📌 Status

🚧 **Em desenvolvimento**

O projeto está sendo desenvolvido e novas funcionalidades poderão ser adicionadas conforme as necessidades da equipe.

## 📄 Licença

Este projeto está licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).

---

<p align="center">
  Desenvolvido para a organização e compartilhamento de conhecimento da equipe.
</p>
