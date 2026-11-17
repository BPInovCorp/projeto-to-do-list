# Inovcorp To-Do — Gestor de Tarefas Diárias

Aplicação web para organização e gestão de tarefas pessoais, desenvolvida com Laravel 12 e Vue 3 para a Inovcorp.

---

## 1. Apresentação do Projeto

O **Inovcorp To-Do** foi concebido para proporcionar uma ferramenta fluida e eficiente na organização das atividades diárias. O sistema assegura o isolamento dos dados de cada utilizador através de autenticação dedicada, disponibilizando um ciclo completo de operações sobre tarefas, filtros combinados de consulta, métricas estatísticas em tempo real e suporte bilingue com comutação entre Português Europeu e Inglês.

### Funcionalidades Principais:
- **Autenticação de Utilizadores:** Registo de utilizadores, início de sessão com proteção contra tentativas excessivas e encerramento de sessão.
- **Gestão de Tarefas (CRUD):** Criação com título, descrição opcional, data limite e prioridades (Baixa, Média, Alta).
- **Consulta e Detalhes:** Visualização dos atributos da tarefa através de caixa de diálogo modal ao selecionar um item da lista.
- **Edição e Atualização:** Modificação de atributos existentes com validação dos campos submetidos.
- **Conclusão Rápida:** Alternância direta entre estado pendente e concluído, com registo automático da data de conclusão.
- **Eliminação de Tarefas:** Remoção individual de tarefas mediante confirmação em diálogo de alerta.
- **Filtros Combinados:** Filtragem cruzada por Estado, Prioridade e Prazos (Para hoje, Próximos 7 dias, Em atraso, Sem data).
- **Painel Estatístico:** Indicadores globais em tempo real (Total, Pendentes, Concluídas, Em Atraso).
- **Suporte Bilingue:** Alternância imediata entre Português Europeu (`pt_PT`) e Inglês (`en`).

---

## 2. Arquitetura e Tecnologias

A aplicação assenta numa arquitetura com backend Laravel e interface reativa Vue 3:

- **Backend:** Laravel 12 (PHP 8.2+) estruturado segundo o padrão MVC, utilizando Form Requests para validação de dados, Policies para controlo de autorização e Eloquent ORM para manipulação relacional.
- **Frontend:** Interface reativa desenvolvida em Vue 3 (Composition API com `<script setup>`), integrada com o Laravel através do ponto de montagem `#app` servido por uma vista Blade mestre (`app.blade.php`).
- **Comunicação HTTP:** Cliente Axios configurado para gestão de sessões, cabeçalhos de segurança CSRF e injeção do cabeçalho de idioma `X-Locale`.
- **Estilização:** Tailwind CSS v4 através de classes utilitárias sem recurso a CSS em linha.
- **Compilador e Empacotador:** Vite com os plugins `@vitejs/plugin-vue` e `@tailwindcss/vite`.
- **Base de Dados:** MySQL com integridade referencial e chaves estrangeiras com remoção em cascata.
- **Testes Automatizados:** Testes funcionais e unitários executados com Pest PHP / PHPUnit.

---

## 3. Sistema de Internacionalização

A aplicação dispõe de internacionalização estruturada em duas camadas:

1. **Frontend (`useI18n`):** O módulo `resources/js/i18n/index.js` gere o idioma ativo através de dicionários simétricos (`locales/pt_PT.js` e `locales/en.js`). O Português Europeu (`pt_PT`) é o idioma padrão, recorrendo ao Inglês (`en`) como idioma alternativo caso alguma chave falte.
2. **Sincronização com o Backend:** A alteração de língua envia um pedido `POST /locale`, persistindo o valor validado na sessão HTTP do Laravel. O middleware `SetLocaleMiddleware` interseta o cabeçalho `X-Locale` quando enviado e emite o cabeçalho HTTP `Content-Language` correspondente (`pt-PT` ou `en`).

---

## 4. Modelação da Base de Dados

A base de dados relacional em MySQL foi estruturada nas seguintes tabelas:

### Tabela `utilizadores`
- `id`: Chave primária auto-incremental (`bigint unsigned`).
- `nome`: Nome completo do utilizador (`varchar(255)`).
- `email`: Endereço de correio eletrónico único (`varchar(255)` com índice `UNIQUE`).
- `password`: Hash da palavra-passe (`varchar(255)`).
- `email_verified_at`: Timestamp de verificação (`timestamp`, nullable).
- `remember_token`: Token para persistência de sessão (`varchar(100)`, nullable).
- `created_at` e `updated_at`: Timestamps de auditoria.

### Tabela `tarefas`
- `id`: Chave primária auto-incremental (`bigint unsigned`).
- `utilizador_id`: Chave estrangeira associada a `utilizadores.id` com remoção em cascata (`cascadeOnDelete`).
- `titulo`: Designação da tarefa (`varchar(255)`).
- `descricao`: Descrição detalhada da atividade (`text`, nullable).
- `data_vencimento`: Data limite de conclusão (`date`, nullable).
- `prioridade`: Nível de urgência (`varchar(20)`, enum: `baixa`, `media`, `alta`, padrão `media`).
- `estado`: Estado da tarefa (`varchar(20)`, enum: `pendente`, `concluida`, padrão `pendente`).
- `concluida_em`: Timestamp de conclusão efetiva (`timestamp`, nullable).
- `created_at` e `updated_at`: Timestamps de auditoria.

### Índices de Otimização no MySQL
Foram definidos três índices compostos com o intuito de melhorar o desempenho das consultas filtradas por utilizador:
- `['utilizador_id', 'estado']`: Otimiza a separação entre pendentes e concluídas.
- `['utilizador_id', 'prioridade']`: Otimiza a filtragem por nível de prioridade.
- `['utilizador_id', 'data_vencimento']`: Otimiza a pesquisa por datas e verificação de prazos.

---

## 5. Segurança e Controlo de Acesso

- **Autenticação por Sessões:** Gestão de identidade com regeneração do identificador de sessão (`session()->regenerate()`) no registo e no login para prevenção de ataques de fixação de sessão.
- **Proteção contra Força Bruta (Rate Limiting):** O controlador `AuthController` limita as tentativas consecutivas de autenticação a 5 por minuto por utilizador e IP via `RateLimiter`, devolvendo HTTP 429 em caso de excesso.
- **Autorização Centralizada (`TarefaPolicy`):** As permissões de acesso (`viewAny`, `view`, `create`, `update`, `delete`) garantem que o utilizador autenticado só pode aceder e alterar as suas próprias tarefas. Tentativas de aceder a registos de outro utilizador devolvem HTTP 403 Forbidden.
- **Proteção CSRF:** Todos os pedidos de mutação exigem o token CSRF. O composable `useAuth.js` atualiza o cabeçalho `X-CSRF-TOKEN` do Axios após cada alteração de sessão (`login`, `register`, `logout`).
- **Hashing de Palavras-passe:** As palavras-passe são protegidas pelo algoritmo `bcrypt` através do cast `hashed` do Laravel no modelo `Utilizador`.

---

## 6. Acessibilidade e Usabilidade

Foram implementadas práticas de acessibilidade na interface:

- **Navegação por Teclado:** Suporte para foco por `Tab`, ativação de tarefas por `Enter` ou `Espaço`, e fecho de caixas de diálogo modais com a tecla `Escape`.
- **Foco Visível:** Aplicação de classes de foco visível com contraste (`focus-visible:ring-2 focus-visible:ring-indigo-500`).
- **Semântica e Atributos ARIA:**
  - Modais com `role="dialog"` e `role="alertdialog"`, acompanhados de `aria-modal="true"`.
  - Associação explícita entre títulos e descrições dos diálogos através de `aria-labelledby` e `aria-describedby`.
  - Botões de seleção de estado sinalizados com `aria-pressed`.
  - Região dinâmica com `aria-live="polite"` para anúncio da contagem de resultados atualizada por filtros.
- **Alvos de Interação:** Alvos de clique dimensionados para pelo menos 44x44px em dispositivos móveis na caixa de seleção de estado e nos botões de ação.

---

## 7. Instalação e Execução

### Pré-requisitos
- PHP 8.2 ou superior (com extensões `pdo_mysql`, `mbstring`, `openssl`).
- Composer 2.x.
- Node.js 18.x ou superior e NPM.
- Servidor de base de dados MySQL 8.x / MariaDB.

### Passo 1: Obter o Projeto
```bash
git clone https://github.com/BPInovCorp/projeto-to-do-list.git
cd projeto-to-do-list
```

### Passo 2: Instalar Dependências
```bash
composer install
npm install
```

### Passo 3: Configurar as Variáveis de Ambiente
Crie o ficheiro `.env` com base no exemplo e configure as credenciais da base de dados MySQL:
```bash
cp .env.example .env
php artisan key:generate
```

Certifique-se das seguintes definições no `.env`:
```env
APP_NAME="Inovcorp To-Do"
APP_LOCALE=pt_PT
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inovcorp_todo
DB_USERNAME=root
DB_PASSWORD=
```

### Passo 4: Executar as Migrações
Com a base de dados `inovcorp_todo` criada no MySQL, execute:
```bash
php artisan migrate
```

### Passo 5: Compilar os Recursos de Frontend
```bash
npm run build
```

### Passo 6: Iniciar o Servidor
```bash
php -S 127.0.0.1:8000 -t public
```
Aceda à aplicação através do navegador em: **`http://127.0.0.1:8000`**

---

## 8. Testes Automatizados

A estabilidade da aplicação é verificada através de testes das principais funcionalidades e regras de negócio.

Para executar a suite de testes:
```bash
php artisan test
```

### Distribuição dos Testes Implementados:
- `Tests\Unit\ExampleTest` (1 teste)
- `Tests\Unit\TarefaUnitTest` (5 testes)
- `Tests\Unit\UtilizadorUnitTest` (3 testes)
- `Tests\Feature\ExampleTest` (1 teste)
- `Tests\Feature\AuthTest` (8 testes)
- `Tests\Feature\LocalizacaoTest` (4 testes)
- `Tests\Feature\TarefaCriacaoListagemTest` (6 testes)
- `Tests\Feature\TarefaCrudCompletoTest` (11 testes)
- `Tests\Feature\TarefaFiltrosTest` (9 testes)

**Total:** 48 testes aprovados (173 asserções).

---

## 9. Versionamento e Convenções de Commits

O versionamento do projeto seguiu a convenção de Commits Convencionais redigidos em Português:

- `feat: configuracao inicial do laravel 12 com vue 3 e tailwind, sistema bilingue e fluxo de autenticacao`
- `feat: modelacao de tarefas, enums, fluxo de criacao e listagem com testes funcionais`
- `feat: ciclo completo de crud, modais acessiveis e alternancia de estado de tarefas`
- `feat: filtros dinamicos combinados, indicadores de resumo e acessibilidade wcag`
- `docs: documentacao tecnica completa do projeto no README`