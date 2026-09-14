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
2. Ao identificar uma solução ou orientação útil, um **editor** transforma esse conhecimento em conteúdo reutilizável.
3. O **administrador** mantém usuários, categorias e posts organizados.

## 📁 Estrutura do projeto

```text
app/                 Código da aplicação
database/            Migrations, factories e seeders
resources/views/     Interfaces Blade
routes/              Rotas web e de autenticação
tests/               Testes automatizados
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
