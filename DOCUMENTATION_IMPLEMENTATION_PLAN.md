# DOCUMENTATION_IMPLEMENTATION_PLAN.md — Siwayut Catering Framework

> Documentation architecture plan generated from reconstructed repository model.
> This file defines HOW documentation should be written. It does NOT contain documentation.

---

## 1. Documentation Objective

### Target Audience

```
PRIMARY:
  FRAMEWORK USERS     — Developers building features on top of this vanilla PHP framework.
                        Expected skill: intermediate PHP, understands MVC, PSR-4, PDO basics.

SECONDARY:
  CONTRIBUTORS        — Developers implementing the TODO stubs or extending the framework.
                        Expected skill: advanced PHP 8.2+, understands DI containers,
                        reflection API, middleware patterns.

TERTIARY:
  ARCHITECTURE REVIEWERS — Technical leads evaluating framework design decisions.
                           Expected skill: senior-level, framework design experience.

OUT OF SCOPE:
  BEGINNER USERS      — Absolute PHP beginners. This framework has no training wheels.
                        Documentation assumes working PHP/Composer/MySQL knowledge.
```

### Documentation Purpose

```
1. Enable a new developer to install, configure, and run the application within 15 minutes.
2. Explain the exact request lifecycle so developers know WHERE to place code.
3. Document every contract (method signatures, return types, behavioral invariants)
   so implementations match the scaffolded architecture precisely.
4. Provide copy-paste usage examples for every framework subsystem.
5. Define security boundaries and mandatory practices.
6. Guide contributors through the TODO implementation backlog with explicit constraints.
```

### Expected Technical Depth

```
CONFIGURATION     — Step-by-step with exact commands
ARCHITECTURE      — ASCII diagrams + prose explaining WHY, not just WHAT
CORE COMPONENTS   — Full API reference with signatures, parameters, return types, contracts
USAGE EXAMPLES    — Runnable code blocks showing real use cases from this codebase
SECURITY          — Prescriptive rules, not advisory suggestions
```

### Onboarding Expectations

```
READING PATH (linear):
  README → INSTALLATION → QUICKSTART → ARCHITECTURE → (topic docs as needed)

TIME TO FIRST REQUEST:       < 10 minutes (after prerequisites met)
TIME TO UNDERSTAND LIFECYCLE: < 30 minutes (reading ARCHITECTURE)
TIME TO ADD A NEW ROUTE:      < 5 minutes (after reading ROUTING + QUICKSTART)
```

---

## 2. Documentation Dependency Graph

```
README.md
 ├── INSTALLATION.md
 ├── QUICKSTART.md
 │     └── (requires INSTALLATION)
 └── ARCHITECTURE.md
       ├── CONTAINER.md
       │     └── (standalone, no doc deps)
       ├── ROUTING.md
       │     └── (requires CONTAINER knowledge)
       ├── MIDDLEWARE.md
       │     ├── (requires ROUTING)
       │     └── (requires CONTAINER)
       ├── DATABASE.md
       │     └── (standalone, no doc deps)
       ├── VALIDATION.md
       │     └── (requires DATABASE for unique rules)
       ├── VIEWS.md
       │     └── (standalone, no doc deps)
       ├── ERROR_HANDLING.md
       │     ├── (requires VIEWS for error templates)
       │     └── (requires ARCHITECTURE for exception handler)
       └── SECURITY.md
             ├── (requires MIDDLEWARE for CSRF/Auth)
             ├── (requires DATABASE for prepared statements)
             ├── (requires VIEWS for XSS escaping)
             └── (requires VALIDATION for input sanitization)

 CONVENTIONS.md
   └── (standalone, inferred from full codebase scan)

 EXAMPLES.md
   └── (requires ALL topic docs)

 CONTRIBUTING.md
   └── (requires CONVENTIONS + ARCHITECTURE)
```

### Reverse Dependency Map (what blocks what)

```
INSTALLATION    blocks → QUICKSTART
ARCHITECTURE    blocks → all topic docs (ROUTING, CONTAINER, MIDDLEWARE, etc.)
ROUTING         blocks → MIDDLEWARE, EXAMPLES
CONTAINER       blocks → ROUTING, MIDDLEWARE
MIDDLEWARE      blocks → SECURITY
DATABASE        blocks → VALIDATION
ALL TOPIC DOCS  block  → EXAMPLES
CONVENTIONS     blocks → CONTRIBUTING
```

---

## 3. Documentation Writing Phases

---

### PHASE 0 — Project Positioning

**Objective**: Establish project identity, scope, and top-level navigation.

**Documents produced**:
- `README.md` (skeleton only — feature list, badges, doc index)

**Required prior knowledge**: Repository reconstruction model (BRIEF_RECONSTRUCTED.md)

**Validation criteria**:
- README contains project name, one-line description, feature bullet list
- README contains documentation index linking to all other docs
- README does NOT contain installation steps (deferred to INSTALLATION.md)
- All linked doc filenames match the exact filenames in the checklist (Section 4)

**Completion checklist**:
- [ ] Project name and description present
- [ ] Feature list reflects actual implemented + scaffolded capabilities
- [ ] Tech stack stated (PHP 8.2+, MySQL, vanilla MVC, no third-party deps)
- [ ] Documentation index with relative links to all 14 other docs
- [ ] License mention (or placeholder if none exists in repo)
- [ ] No dead links

---

### PHASE 1 — Entry Documentation

**Objective**: Get a developer from zero to running dev server.

**Documents produced**:
- `INSTALLATION.md`
- `QUICKSTART.md`

**Required prior knowledge**: `.env` structure, `composer.json` scripts, `public/index.php` entry point, `config/database.php`

**Validation criteria**:
- INSTALLATION covers: prerequisites, clone, `composer install`, `.env` setup, database creation, migration, seeding, `composer run dev`
- QUICKSTART covers: creating a route, creating a controller, rendering a view — end-to-end minimal example
- Every command is copy-pasteable
- Every file path is relative to project root and matches actual repo structure

**Completion checklist**:
- [ ] INSTALLATION lists PHP 8.2+, Composer, MySQL as prerequisites
- [ ] INSTALLATION shows exact `.env` variables with explanations
- [ ] INSTALLATION shows `composer run dev` and expected output
- [ ] INSTALLATION mentions `php -S localhost:8000 -t public` as underlying server
- [ ] QUICKSTART demonstrates adding a route in `config/routes.php`
- [ ] QUICKSTART demonstrates creating a controller extending `BaseController`
- [ ] QUICKSTART demonstrates creating a view template
- [ ] QUICKSTART demonstrates rendering with layout
- [ ] QUICKSTART links back to ARCHITECTURE for deeper understanding

---

### PHASE 2 — Setup Documentation

**Objective**: N/A — merged into Phase 1. This phase validates Phase 1 outputs against the actual bootstrap chain.

**Documents produced**: None new. Phase 1 docs are audited.

**Required prior knowledge**: Bootstrap lifecycle from BRIEF_RECONSTRUCTED.md (11-step sequence in `public/index.php` + `bootstrap/app.php`)

**Validation criteria**:
- INSTALLATION's setup steps match the actual bootstrap order
- `.env` variables documented match the actual variables read in `config/app.php` and `config/database.php`
- No step references a file or constant that does not exist

**Completion checklist**:
- [ ] `.env` variables match: APP_NAME, APP_ENV, APP_DEBUG, APP_TIMEZONE, APP_URL, DB_DRIVER, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- [ ] Bootstrap order in docs matches code: autoload → .env → config/app.php → bootstrap/app.php → bindings → routes → dispatch
- [ ] No invented configuration keys

---

### PHASE 3 — Architectural Documentation

**Objective**: Explain the full request lifecycle, component relationships, and data flow.

**Documents produced**:
- `ARCHITECTURE.md`

**Required prior knowledge**: Full repository model — entry point, bootstrap chain, container, router dispatch, middleware pipeline, controller resolution, view rendering, error propagation

**Validation criteria**:
- Contains request lifecycle diagram (ASCII)
- Contains error propagation diagram (ASCII)
- Contains component relationship diagram (ASCII)
- Every component mentioned maps to an actual class in `src/Core/` or `src/`
- No speculative "planned" features — only what exists in code

**Completion checklist**:
- [ ] Request lifecycle: HTTP → .htaccess → index.php → bootstrap → router → middleware → controller → service → model → view → response
- [ ] Error propagation: throw → set_exception_handler → Logger → HTTP status → error view or debug dump
- [ ] Component map: Container, Router, Request, Response, View, Database, Session, Logger, Csrf, Validator
- [ ] Layer diagram: Controllers → Services → Models → Database
- [ ] Config loading order documented
- [ ] Binding registration order documented
- [ ] Links to each component's dedicated doc

---

### PHASE 4 — Core Component Documentation

**Objective**: Full API reference for each framework subsystem.

**Documents produced**:
- `CONTAINER.md`
- `ROUTING.md`
- `MIDDLEWARE.md`
- `DATABASE.md`
- `VALIDATION.md`
- `VIEWS.md`
- `ERROR_HANDLING.md`

**Required prior knowledge**: ARCHITECTURE.md must be complete. Each doc requires the specific class source from `src/Core/`, `src/Middleware/`, `src/Models/`, `src/Exceptions/`.

**Validation criteria**:
- Every public method signature documented matches code exactly
- Every behavioral contract (from `// CONTRACT:` comments) is documented
- Every return type matches code
- Code examples demonstrate actual usage patterns from this codebase (not generic PHP)
- TODO methods are documented with their signatures but marked as `[NOT YET IMPLEMENTED]`

**Completion checklist**:
- [ ] CONTAINER: `bind()`, `make()`, `makeNew()`, `has()` — with singleton vs fresh semantics
- [ ] CONTAINER: Auto-wiring explanation with reflection
- [ ] CONTAINER: `config/bindings.php` registration example (from actual file)
- [ ] ROUTING: `get()`, `post()`, `put()`, `patch()`, `delete()` signatures
- [ ] ROUTING: `group()` nesting with prefix + middleware merging
- [ ] ROUTING: Route parameter syntax `{param}` and regex conversion
- [ ] ROUTING: `dispatch()` flow — first-match-wins, NotFoundException on miss
- [ ] ROUTING: Actual route table from `config/routes.php`
- [ ] MIDDLEWARE: `MiddlewareInterface` contract — `handle(Request): bool`
- [ ] MIDDLEWARE: Boolean gate semantics (true=continue, false=halt)
- [ ] MIDDLEWARE: Parameterized alias resolution (`role:admin` → `new RoleMiddleware('admin')`)
- [ ] MIDDLEWARE: Registration in `index.php` via `addMiddleware()`
- [ ] DATABASE: Singleton pattern — `getInstance()`
- [ ] DATABASE: `config/database.php` structure
- [ ] DATABASE: PDO options (ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES=false)
- [ ] DATABASE: BaseModel CRUD API — all method signatures
- [ ] DATABASE: `$table`, `$primaryKey`, `$sortableColumns` properties
- [ ] DATABASE: `query()` and `execute()` protected methods
- [ ] VALIDATION: Constructor `(?PDO $db = null)` — optional DB for unique rules
- [ ] VALIDATION: Rule string format — `explode(':', $rule, 3)`
- [ ] VALIDATION: `in:` rule argument format — `explode(',', $argument)`
- [ ] VALIDATION: `validate()`, `errors()`, `error()`, `fails()` API
- [ ] VIEWS: `View` constructor, `render()`, `partial()`, `e()` API
- [ ] VIEWS: Layout composition — `$content` variable injection
- [ ] VIEWS: Template path resolution — `src/Views/{template}.php`
- [ ] VIEWS: Layout path resolution — `src/Views/layouts/{layout}.php`
- [ ] VIEWS: Partials and directory structure (auth, errors, layouts, partials, user)
- [ ] ERROR_HANDLING: Exception hierarchy diagram
- [ ] ERROR_HANDLING: `HttpException` — constructor, `getStatusCode()`, `defaultMessage()` match table
- [ ] ERROR_HANDLING: `NotFoundException` — fixed 404
- [ ] ERROR_HANDLING: `ValidationException` — promoted `$errors`, `getErrors()`
- [ ] ERROR_HANDLING: Global exception handler behavior (APP_DEBUG branching)
- [ ] ERROR_HANDLING: Error view resolution (404.php, 500.php, inline fallback)

---

### PHASE 5 — Domain Usage Documentation

**Objective**: Show developers how to build features using the framework — practical patterns, not API signatures.

**Documents produced**:
- `EXAMPLES.md`

**Required prior knowledge**: ALL Phase 4 docs must be complete.

**Validation criteria**:
- Each example is a self-contained walkthrough
- File paths in examples match actual repo structure
- Controller, Service, Model naming follows actual CONVENTIONS
- Examples reference actual existing classes (e.g., `UserController`, `AuthService`)

**Completion checklist**:
- [ ] Example: Adding a new CRUD resource (full Controller + Service + Model + Views + Routes)
- [ ] Example: Adding authentication-protected routes
- [ ] Example: Adding role-based route restrictions
- [ ] Example: Form submission with CSRF protection and validation
- [ ] Example: File upload via FileUploadService
- [ ] Example: Flash messages and old input repopulation
- [ ] Example: Custom error pages
- [ ] Example: Adding a new middleware
- [ ] Example: Registering a new service binding in `config/bindings.php`
- [ ] Each example uses actual framework classes, not pseudocode

---

### PHASE 6 — Security & Contracts

**Objective**: Document all security mechanisms, mandatory practices, and framework invariants.

**Documents produced**:
- `SECURITY.md`

**Required prior knowledge**: MIDDLEWARE.md (CSRF, Auth), VIEWS.md (XSS escaping), DATABASE.md (SQL injection), VALIDATION.md (input sanitization), ERROR_HANDLING.md (error disclosure)

**Validation criteria**:
- Every security mechanism maps to an actual code implementation or contract
- Prescriptive (MUST/MUST NOT), not advisory
- Covers all 9 security contracts identified in BRIEF_RECONSTRUCTED.md
- No speculative threats — only threats the codebase is designed to handle

**Completion checklist**:
- [ ] XSS: `View::e()` contract — `htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')`
- [ ] XSS: `e()` helper function delegation
- [ ] CSRF: `Csrf::token()`, `Csrf::verify()`, `Csrf::field()` — session key `_csrf_token`
- [ ] CSRF: `CsrfMiddleware` enforcement on POST routes
- [ ] CSRF: `csrf_field()` helper in forms
- [ ] SQL Injection: PDO prepared statements, `EMULATE_PREPARES=false`
- [ ] SQL Injection: BaseModel `query()` / `execute()` binding pattern
- [ ] Auth: Session-based authentication via `AuthMiddleware`
- [ ] Auth: `Session::regenerate()` for fixation prevention
- [ ] Auth: `Session::destroy()` for logout
- [ ] RBAC: `RoleMiddleware` with constructor-injected role string
- [ ] RBAC: Route-level enforcement via middleware alias `role:admin`
- [ ] Error Disclosure: `APP_DEBUG` controls output — production shows generic pages
- [ ] Directory Traversal: `.htaccess` Options -Indexes
- [ ] Password Storage: `password_hash()` / `password_verify()` contract (from AuthService)
- [ ] Singleton Protection: Database `__wakeup()` throws, private constructor/clone

---

### PHASE 7 — Contributor Documentation

**Objective**: Guide developers implementing TODO stubs and extending the framework.

**Documents produced**:
- `CONVENTIONS.md`
- `CONTRIBUTING.md`

**Required prior knowledge**: Full codebase conventions (from BRIEF_RECONSTRUCTED.md naming conventions table), ARCHITECTURE.md

**Validation criteria**:
- CONVENTIONS matches actual patterns observed in code — not aspirational
- CONTRIBUTING references the actual IMPLEMENTATION_PLAN.md phases and TODO locations
- Style rules verified against existing implemented code

**Completion checklist**:
- [ ] CONVENTIONS: Namespace mapping (App\ → src/, Database\ → database/)
- [ ] CONVENTIONS: `declare(strict_types=1)` mandatory on every PHP file
- [ ] CONVENTIONS: Controller naming — `{Entity}Controller extends BaseController`
- [ ] CONVENTIONS: Model naming — `{Entity} extends BaseModel`
- [ ] CONVENTIONS: Service naming — `{Entity}Service` or `{Feature}Service`
- [ ] CONVENTIONS: Middleware naming — `{Feature}Middleware implements MiddlewareInterface`
- [ ] CONVENTIONS: Exception naming — `{Type}Exception`
- [ ] CONVENTIONS: View directory structure — `{entity}/{action}.php`
- [ ] CONVENTIONS: Migration naming — `{NNN}_{description}.sql`
- [ ] CONVENTIONS: Seeder naming — `{Name}Seeder.php`
- [ ] CONVENTIONS: Property visibility defaults (private for Core, protected for Models)
- [ ] CONVENTIONS: Promoted constructor properties usage
- [ ] CONVENTIONS: Return type rules — `never` for terminating methods, explicit types everywhere
- [ ] CONVENTIONS: Static facade pattern (Session, Logger, Csrf, Response, Database)
- [ ] CONTRIBUTING: How to find TODO stubs — `grep -rn 'TODO: implement' src/`
- [ ] CONTRIBUTING: Build phase ordering from IMPLEMENTATION_PLAN.md
- [ ] CONTRIBUTING: Contract compliance rules — preserve signatures, honor return types
- [ ] CONTRIBUTING: Testing expectations — verification pipeline commands
- [ ] CONTRIBUTING: Pull request checklist

---

### PHASE 8 — Validation & Cross-link Audit

**Objective**: Verify all documentation is internally consistent, externally accurate, and fully cross-linked.

**Documents produced**: None new. All existing docs are audited and patched.

**Required prior knowledge**: All Phases 0–7 complete.

**Validation criteria**:
- Every relative link resolves to an existing file
- Every class name, method signature, and file path matches current codebase
- Every code example is syntactically valid PHP
- No document references a feature marked as TODO without noting `[NOT YET IMPLEMENTED]`
- Documentation index in README lists every doc file

**Completion checklist**:
- [ ] All inter-document links verified (no 404s)
- [ ] All code examples pass `php -l` syntax check
- [ ] All file paths verified against directory tree
- [ ] All method signatures verified against source files
- [ ] All CONTRACT comments from source are reflected in docs
- [ ] TODO-status features marked consistently across all docs
- [ ] README doc index is complete and ordered
- [ ] No contradictions between docs (e.g., ROUTING says X, MIDDLEWARE says Y about same behavior)

---

## 4. Required Documentation Files

Ordered by dependency (write top-to-bottom):

```
- [ ] docs/README.md
- [ ] docs/INSTALLATION.md
- [ ] docs/QUICKSTART.md
- [ ] docs/ARCHITECTURE.md
- [ ] docs/CONTAINER.md
- [ ] docs/DATABASE.md
- [ ] docs/VIEWS.md
- [ ] docs/ROUTING.md
- [ ] docs/MIDDLEWARE.md
- [ ] docs/VALIDATION.md
- [ ] docs/ERROR_HANDLING.md
- [ ] docs/SECURITY.md
- [ ] docs/CONVENTIONS.md
- [ ] docs/EXAMPLES.md
- [ ] docs/CONTRIBUTING.md
```

**Total**: 15 documents.
**Location**: `docs/` directory at project root.
**Format**: Markdown (.md).
**Root README.md**: Symlink or copy of `docs/README.md`, or a minimal pointer to `docs/`.

---

## 5. Per-Document Content Contracts

---

### 5.1 README.md

**Purpose**: Project landing page — first thing a developer reads.

**Required sections**:
```
├── Project Title + One-Line Description
├── Badges (PHP version, license)
├── Features
│     ├── Implemented features (bullet list)
│     └── Scaffolded/planned features (marked clearly)
├── Tech Stack
├── Quick Install (3-line summary, link to INSTALLATION.md)
├── Quick Start (3-line summary, link to QUICKSTART.md)
├── Documentation Index
│     └── Ordered list of all 14 doc links
├── Project Structure (condensed tree — top 2 levels only)
└── License
```

**Mandatory code examples**: None (deferred to QUICKSTART).
**Diagrams required**: Condensed directory tree (2 levels).
**Cross-links required**: Every doc file in Section 4.
**Validation criteria**: Every link resolves. Feature list matches codebase. No implementation details.

---

### 5.2 INSTALLATION.md

**Purpose**: Zero-to-running setup guide.

**Required sections**:
```
├── Prerequisites
│     ├── PHP version (^8.2)
│     ├── Composer
│     ├── MySQL / MariaDB
│     └── PHP extensions (pdo, pdo_mysql, mbstring)
├── Clone & Install
│     ├── git clone command
│     └── composer install
├── Environment Configuration
│     ├── cp .env.example .env
│     ├── Variable reference table (all 11 vars)
│     └── APP_DEBUG explanation
├── Database Setup
│     ├── CREATE DATABASE command
│     ├── Run migrations
│     └── Run seeders
├── Start Development Server
│     ├── composer run dev
│     └── Expected output + URL
├── Verify Installation
│     └── Expected welcome page description
└── Troubleshooting
      ├── Common errors
      └── PHP extension checks
```

**Mandatory code examples**: Shell commands for every step (clone, install, env, db, serve).
**Diagrams required**: None.
**Cross-links required**: QUICKSTART.md (next step), config/database.php reference, .env.example.
**Validation criteria**: A fresh clone following these steps reaches the welcome page.

---

### 5.3 QUICKSTART.md

**Purpose**: Build a minimal feature end-to-end in 5 minutes.

**Required sections**:
```
├── Goal Statement (what you'll build)
├── Step 1: Define a Route
│     └── Edit config/routes.php
├── Step 2: Create a Controller
│     └── New file in src/Controllers/
├── Step 3: Create a View Template
│     └── New file in src/Views/
├── Step 4: Register Container Binding (if needed)
│     └── Edit config/bindings.php
├── Step 5: Test in Browser
│     └── Expected result
├── What's Next
│     └── Links to ARCHITECTURE, ROUTING, VIEWS
└── Full File Listing
      └── All files created/modified in this guide
```

**Mandatory code examples**: Complete PHP files for controller, view template, route registration.
**Diagrams required**: None.
**Cross-links required**: INSTALLATION.md (prerequisite), ARCHITECTURE.md, ROUTING.md, VIEWS.md, CONTAINER.md.
**Validation criteria**: Following steps produces a working page at the specified URL.

---

### 5.4 ARCHITECTURE.md

**Purpose**: Explain the full system design — request lifecycle, component relationships, data flow.

**Required sections**:
```
├── System Overview
│     └── One-paragraph summary of the MVC architecture
├── Request Lifecycle
│     └── ASCII pipeline diagram (from BRIEF_RECONSTRUCTED.md)
├── Bootstrap Sequence
│     ├── Numbered step list
│     └── File-by-file explanation (index.php → bootstrap/app.php → config/*)
├── Component Map
│     └── ASCII diagram showing all Core classes and their relationships
├── Layer Architecture
│     ├── Controller Layer
│     ├── Service Layer
│     ├── Model Layer
│     └── Database Layer
├── Configuration System
│     ├── .env loading mechanism (parse_ini_file → $_ENV)
│     ├── config/app.php (constants)
│     ├── config/database.php (PDO config array)
│     ├── config/bindings.php (IoC registration)
│     └── config/routes.php (route closure)
├── Error Propagation
│     └── ASCII diagram (throw → handler → log → render)
├── Static Facades vs Injected Dependencies
│     ├── Facades: Session, Logger, Csrf, Response, Database
│     └── Injected: Services into Controllers, Models into Services
└── Component Reference Index
      └── Table: Class → File → Doc Link
```

**Mandatory code examples**: Bootstrap sequence (annotated index.php), config/bindings.php excerpt.
**Diagrams required**: Request lifecycle (ASCII), Error propagation (ASCII), Component map (ASCII), Layer diagram (ASCII).
**Cross-links required**: Every Phase 4 component doc.
**Validation criteria**: Every component mentioned exists in `src/`. Every diagram matches code flow.

---

### 5.5 CONTAINER.md

**Purpose**: Full reference for the IoC container.

**Required sections**:
```
├── Overview
│     └── What the container does and why
├── API Reference
│     ├── bind(string $abstract, callable $factory): void
│     ├── make(string $abstract): object
│     │     └── Singleton semantics explanation
│     ├── makeNew(string $abstract): object
│     │     └── Fresh instance, no cache
│     └── has(string $abstract): bool
├── Auto-Wiring
│     ├── How reflection resolves constructor deps
│     ├── Non-builtin types → recursive make()
│     ├── Builtin types → default values
│     └── Error cases (non-instantiable, missing defaults)
├── Registration
│     └── config/bindings.php walkthrough
├── Singleton vs Transient
│     └── make() caches, makeNew() does not
├── Usage in Router
│     └── How Router uses Container to resolve controllers and middleware
└── Gotchas
      └── Stateful singletons across request (not typical in PHP FPM)
```

**Mandatory code examples**: `bind()` call, `make()` call, auto-wiring scenario.
**Diagrams required**: Resolution flowchart (binding → cache → reflection → error).
**Cross-links required**: ARCHITECTURE.md (system context), ROUTING.md (handler resolution), MIDDLEWARE.md (middleware resolution).
**Validation criteria**: Every method signature matches `src/Core/Container.php`.

---

### 5.6 DATABASE.md

**Purpose**: Full reference for database connectivity and the ActiveRecord-style model layer.

**Required sections**:
```
├── Configuration
│     └── config/database.php structure + PDO options
├── Database Singleton
│     ├── getInstance() behavior
│     ├── Singleton enforcement (private constructor, clone, wakeup)
│     └── Connection lifecycle (one per request)
├── BaseModel API
│     ├── Protected properties: $db, $table, $primaryKey, $sortableColumns
│     ├── Constructor contract
│     ├── Read methods: all(), find(), findWhere(), where(), count(), exists(), paginate()
│     ├── Write methods: create(), update(), delete()
│     └── Raw methods: query(), execute()
├── Creating a Model
│     ├── Extend BaseModel
│     ├── Set $table in constructor
│     ├── Add custom query methods
│     └── Example: User model
├── Sort Column Validation
│     └── $sortableColumns whitelist + validateSortColumn()
├── Pagination
│     └── paginate() signature and return structure
└── Migration Format
      └── SQL file naming: NNN_description.sql
```

**Mandatory code examples**: Model class definition, `find()` usage, `create()` usage, `paginate()` usage.
**Diagrams required**: None.
**Cross-links required**: ARCHITECTURE.md, VALIDATION.md (unique rules need PDO).
**Validation criteria**: Every method signature matches `src/Models/BaseModel.php` and `src/Core/Database.php`.

---

### 5.7 VIEWS.md

**Purpose**: Full reference for the template rendering system.

**Required sections**:
```
├── View System Overview
│     └── PHP template files, no template engine
├── View Class API
│     ├── Constructor: __construct(string $viewsPath)
│     ├── render(string $template, array $data, string $layout): void
│     ├── partial(string $template, array $data): string
│     └── static e(mixed $value): string
├── Template Path Resolution
│     ├── Templates: src/Views/{template}.php
│     └── Layouts: src/Views/layouts/{layout}.php
├── Layout System
│     ├── How $content is injected
│     ├── How $data is extracted into layout scope
│     ├── Default layout: 'main'
│     ├── No layout: pass empty string ''
│     └── Available layouts: main.php, auth.php
├── Partials
│     └── partial() method, src/Views/partials/ directory
├── Output Escaping
│     ├── View::e() contract
│     ├── e() helper function
│     └── MANDATORY: all user data must be escaped
├── Directory Structure
│     └── auth/, errors/, layouts/, partials/, user/
├── BaseController Integration
│     └── $this->render() delegates to View::render()
└── Gotchas
      ├── $content variable name collision in layout
      └── extract() overwrites existing variables
```

**Mandatory code examples**: Template file, layout file with `$content`, `partial()` include, `View::e()` usage.
**Diagrams required**: Template rendering flowchart (render → partial → ob_start → layout → output).
**Cross-links required**: ARCHITECTURE.md, SECURITY.md (XSS escaping).
**Validation criteria**: Template path examples match actual `src/Views/` structure.

---

### 5.8 ROUTING.md

**Purpose**: Full reference for route definition, matching, and dispatch.

**Required sections**:
```
├── Route Definition
│     ├── HTTP verb methods: get(), post(), put(), patch(), delete()
│     ├── Handler formats: [ControllerClass, 'method'] or callable
│     ├── Route parameters: {paramName} syntax
│     └── Path normalization (rtrim, default '/')
├── Route Groups
│     ├── group(array $options, callable $callback)
│     ├── Prefix concatenation
│     ├── Middleware merging (array_merge)
│     ├── Nesting behavior (save/restore state)
│     └── Example from config/routes.php
├── Route Parameters
│     ├── Regex conversion: {id} → (?P<id>[^/]+)
│     ├── Full match: #^pattern$#
│     ├── Access via Request::param()
│     └── setRouteParams() injection
├── Dispatch Flow
│     ├── Sequential iteration (first-match-wins)
│     ├── Method + URI match
│     ├── Middleware pipeline execution
│     ├── Handler execution
│     └── No match → NotFoundException
├── Middleware Assignment
│     ├── Per-route (via group)
│     ├── Alias registration: addMiddleware(alias, class)
│     └── Parameterized aliases: 'role:admin'
├── Handler Resolution
│     ├── Callable → call_user_func
│     └── Array → Container::make(class), then ->method(request)
└── Complete Route Table
      └── Table from BRIEF_RECONSTRUCTED.md
```

**Mandatory code examples**: Route definition, group with middleware, parameterized route, `config/routes.php` full listing.
**Diagrams required**: Dispatch flowchart (match → middleware → handler → response).
**Cross-links required**: CONTAINER.md (handler resolution), MIDDLEWARE.md (pipeline), ARCHITECTURE.md.
**Validation criteria**: Route table matches `config/routes.php`. Parameter regex matches `Router::matchRoute()`.

---

### 5.9 MIDDLEWARE.md

**Purpose**: Full reference for the middleware system.

**Required sections**:
```
├── Middleware Contract
│     └── MiddlewareInterface: handle(Request $request): bool
├── Boolean Gate Semantics
│     ├── true → pipeline continues to next middleware / handler
│     └── false → pipeline halts, handler NOT executed
├── Pipeline Execution
│     └── Sequential, all must pass
├── Registration
│     └── Router::addMiddleware(alias, class) in index.php
├── Resolution
│     ├── Without argument → Container::make(class) — singleton
│     └── With argument → new Class(argument) — direct instantiation
├── Built-in Middleware
│     ├── AuthMiddleware
│     │     ├── Alias: 'auth'
│     │     ├── No constructor args
│     │     └── Expected behavior: check session for authenticated user
│     ├── RoleMiddleware
│     │     ├── Alias: 'role'
│     │     ├── Constructor: (string $requiredRole) — promoted
│     │     ├── Usage: 'role:admin'
│     │     └── Expected behavior: check session user role matches
│     └── CsrfMiddleware
│           ├── Alias: 'csrf'
│           ├── No constructor args
│           └── Expected behavior: verify CSRF token on POST
├── Creating Custom Middleware
│     ├── Implement MiddlewareInterface
│     ├── Register alias in index.php
│     └── Apply via route group
└── Current Implementation Status
      ├── AuthMiddleware: returns true always [STUB]
      ├── CsrfMiddleware: returns false always [STUB]
      └── RoleMiddleware: returns false always [STUB]
```

**Mandatory code examples**: Custom middleware class, registration, route group application.
**Diagrams required**: Pipeline flow (request → MW1 → MW2 → ... → handler or halt).
**Cross-links required**: ROUTING.md (group middleware), CONTAINER.md (resolution), SECURITY.md (CSRF, Auth).
**Validation criteria**: Interface signature matches `src/Middleware/MiddlewareInterface.php`. Alias table matches `public/index.php`.

---

### 5.10 VALIDATION.md

**Purpose**: Full reference for the data validation system.

**Required sections**:
```
├── Validator API
│     ├── Constructor: __construct(?PDO $db = null)
│     ├── validate(array $data, array $rules): bool
│     ├── errors(): array
│     ├── error(string $field): ?string
│     └── fails(): bool
├── Rule Syntax
│     ├── Format: 'field' => 'rule1|rule2|rule3:arg'
│     ├── Parsing contract: explode(':', $rule, 3)
│     └── Expected rules: required, email, min, max, unique, in, etc.
│           [UNVERIFIED — rule set not implemented]
├── The in: Rule
│     └── Argument parsing: explode(',', $argument)
├── Database-Dependent Rules
│     └── PDO injection for 'unique' rule validation
├── ValidationException
│     ├── throw with errors array
│     └── getErrors() returns promoted $errors
├── Usage Pattern
│     ├── Instantiate in controller
│     ├── Call validate() with request data
│     ├── Check fails()
│     ├── Handle errors or proceed
│     └── Integration with Session::setOld() for form repopulation
└── Error Message Format
      └── [UNVERIFIED — format not implemented]
```

**Mandatory code examples**: Validator usage in controller, rule definition array, error handling.
**Diagrams required**: None.
**Cross-links required**: DATABASE.md (PDO for unique), ERROR_HANDLING.md (ValidationException), VIEWS.md (displaying errors).
**Validation criteria**: Method signatures match `src/Core/Validator.php`. CONTRACT comments on `applyRule()` reflected.

---

### 5.11 ERROR_HANDLING.md

**Purpose**: Full reference for exception hierarchy and error handling.

**Required sections**:
```
├── Exception Hierarchy
│     └── ASCII tree diagram
├── AppException
│     └── Empty extension of \RuntimeException
├── HttpException
│     ├── Constructor: (int $code, string $message = '')
│     ├── getStatusCode(): int
│     ├── defaultMessage(): match expression table
│     │     └── 400, 401, 403, 404, 405, 419, 422, 429, 500
│     └── Usage: throw new HttpException(403)
├── NotFoundException
│     ├── Constructor: (string $message = 'Resource not found')
│     ├── Hardcoded status 404
│     └── Thrown by Router on no match
├── ValidationException
│     ├── Constructor: (array $errors, string $message = 'Validation failed')
│     ├── getErrors(): array
│     └── Promoted $errors property
├── Global Exception Handler
│     ├── Location: bootstrap/app.php
│     ├── Flow: log → status code → render
│     ├── APP_DEBUG=true: detailed HTML dump (class, message, file, line, trace)
│     ├── APP_DEBUG=false: friendly error page
│     ├── 404 → Views/errors/404.php
│     ├── other → Views/errors/500.php
│     └── fallback → inline HTML
├── Error View Templates
│     ├── 404.php: $message variable, Indonesian default text
│     └── 500.php: $message variable, Indonesian default text
├── Logger Integration
│     └── Logger::error() called with message, file, line, trace
└── Creating Custom Exceptions
      └── Extend AppException or HttpException
```

**Mandatory code examples**: Throwing HttpException, catching ValidationException, custom exception class.
**Diagrams required**: Exception hierarchy tree (ASCII), error handler decision flowchart.
**Cross-links required**: ARCHITECTURE.md (handler in bootstrap), VIEWS.md (error templates), SECURITY.md (error disclosure).
**Validation criteria**: Hierarchy matches `src/Exceptions/`. Handler code matches `bootstrap/app.php`.

---

### 5.12 SECURITY.md

**Purpose**: Prescriptive security reference — MUST/MUST NOT rules.

**Required sections**:
```
├── XSS Prevention
│     ├── View::e() — mandatory for all user output
│     ├── e() helper function
│     └── htmlspecialchars contract: ENT_QUOTES, UTF-8
├── CSRF Protection
│     ├── Csrf class API (token, verify, field, regenerate)
│     ├── Session key: _csrf_token
│     ├── CsrfMiddleware enforcement
│     ├── csrf_field() helper in forms
│     └── Mandatory on all state-changing routes
├── SQL Injection Prevention
│     ├── PDO prepared statements
│     ├── EMULATE_PREPARES=false
│     └── BaseModel binding pattern
├── Authentication
│     ├── Session-based auth
│     ├── AuthMiddleware enforcement
│     ├── Session::regenerate() on login
│     ├── Session::destroy() on logout
│     └── password_verify() for credential check
├── Authorization
│     ├── RoleMiddleware with route-level enforcement
│     └── Parameterized alias: 'role:admin'
├── Error Disclosure
│     ├── APP_DEBUG=false in production
│     ├── Generic error pages for end users
│     └── Detailed errors only in development
├── Directory Listing
│     └── .htaccess Options -Indexes
├── Database Singleton Security
│     ├── Private constructor prevents external instantiation
│     └── __wakeup() prevents deserialization attacks
└── Security Checklist
      └── Pre-deployment audit checklist
```

**Mandatory code examples**: CSRF form field usage, View::e() in templates, prepared statement in model.
**Diagrams required**: None.
**Cross-links required**: MIDDLEWARE.md, DATABASE.md, VIEWS.md, ERROR_HANDLING.md, VALIDATION.md.
**Validation criteria**: Every rule maps to an actual code mechanism. No speculative threats.

---

### 5.13 CONVENTIONS.md

**Purpose**: Codify all naming, structure, and style conventions observed in the codebase.

**Required sections**:
```
├── Namespace Conventions
│     ├── App\ → src/
│     └── Database\ → database/
├── File Naming
│     ├── Controllers, Models, Services, Middleware, Exceptions
│     ├── Views: {entity}/{action}.php
│     ├── Layouts: layouts/{name}.php
│     ├── Migrations: {NNN}_{description}.sql
│     └── Seeders: {Name}Seeder.php
├── Class Design
│     ├── declare(strict_types=1) on every file
│     ├── Promoted constructor properties
│     ├── Explicit return types on all methods
│     ├── never for terminating methods
│     └── Visibility defaults: private (Core), protected (Models)
├── Architectural Patterns
│     ├── Static facades: Session, Logger, Csrf, Response, Database
│     ├── Dependency injection: Services into Controllers, Models into Services
│     ├── Interface contract: MiddlewareInterface
│     └── Abstract base classes: BaseModel, BaseController
├── Config File Conventions
│     ├── Returns array (database.php) or closure (routes.php)
│     └── Uses $container variable (bindings.php)
└── PHP Version Features Used
      ├── Promoted properties
      ├── never return type
      ├── match expressions
      ├── Union types (array|callable)
      ├── Intersection of nullable (?PDO)
      └── Named arguments (not observed, but available)
```

**Mandatory code examples**: One example of each convention pattern.
**Diagrams required**: None.
**Cross-links required**: CONTRIBUTING.md.
**Validation criteria**: Every convention observed in at least one source file.

---

### 5.14 EXAMPLES.md

**Purpose**: Copy-paste recipes for common development tasks.

**Required sections**:
```
├── Example 1: New CRUD Resource
│     └── Controller + Service + Model + Views + Routes + Bindings
├── Example 2: Protected Routes
│     └── Route group with 'auth' middleware
├── Example 3: Role-Based Access
│     └── Nested group with 'role:admin'
├── Example 4: Form with CSRF + Validation
│     └── View form + CSRF field + controller validation + error display
├── Example 5: File Upload
│     └── FileUploadService usage
├── Example 6: Flash Messages
│     └── Session::flash() + redirectWithFlash() + partials/flash.php
├── Example 7: Custom Middleware
│     └── Implement, register, apply
├── Example 8: Custom Error Page
│     └── New error template in Views/errors/
└── Example 9: New Service Binding
      └── config/bindings.php addition
```

**Mandatory code examples**: Complete, runnable PHP files for every example.
**Diagrams required**: None.
**Cross-links required**: Every topic doc (ROUTING, CONTAINER, MIDDLEWARE, VIEWS, etc.).
**Validation criteria**: File paths match repo. Class names follow CONVENTIONS. Code is syntactically valid.

---

### 5.15 CONTRIBUTING.md

**Purpose**: Guide contributors through implementing TODOs and extending the framework.

**Required sections**:
```
├── Project Status
│     └── Summary: ~30% implemented, majority TODO stubs
├── Finding Work
│     ├── grep command: grep -rn 'TODO: implement' src/
│     └── IMPLEMENTATION_PLAN.md phase reference
├── Build Phase Ordering
│     └── Summary of 9 phases from IMPLEMENTATION_PLAN.md
├── Contract Compliance Rules
│     ├── Preserve all method signatures exactly
│     ├── Honor return types
│     ├── Follow CONTRACT comments
│     ├── Maintain singleton semantics where specified
│     └── never methods MUST exit;
├── Code Style Requirements
│     └── Reference CONVENTIONS.md
├── Testing Requirements
│     ├── php -l on all files
│     ├── composer dump-autoload
│     ├── Manual browser testing
│     └── Verification pipeline commands
├── Pull Request Checklist
│     ├── Signatures unchanged
│     ├── strict_types declared
│     ├── Namespaces correct
│     ├── No new dependencies without discussion
│     └── TODO comment removed after implementation
└── Architecture Decision Records
      └── How to propose changes to contracts
```

**Mandatory code examples**: grep command, verification commands.
**Diagrams required**: None.
**Cross-links required**: CONVENTIONS.md, ARCHITECTURE.md, IMPLEMENTATION_PLAN.md.
**Validation criteria**: grep command returns actual TODO locations. Phase ordering matches IMPLEMENTATION_PLAN.md.

---

## 6. Documentation Style System

### Technical Tone

```
REGISTER:        Technical, direct, declarative
VOICE:           Second person ("you") for guides, impersonal for reference
TENSE:           Present tense for behavior ("The router dispatches..."),
                 imperative for instructions ("Create a file...")
CERTAINTY:       Definitive for implemented code ("returns true"),
                 qualified for TODO code ("[NOT YET IMPLEMENTED]")
AVOID:           Marketing language, superlatives, hedging ("might", "could")
```

### Explanation Depth Matrix

```
┌───────────────┬────────────────────────────────────────┐
│ Audience      │ Depth                                  │
├───────────────┼────────────────────────────────────────┤
│ BEGINNER      │ N/A — out of scope                     │
│ FRAMEWORK     │ Conceptual overview + practical usage  │
│ USER          │ with code examples. WHY before HOW.    │
├───────────────┼────────────────────────────────────────┤
│ CONTRIBUTOR   │ Exact signatures, behavioral contracts,│
│               │ invariants. No ambiguity.              │
├───────────────┼────────────────────────────────────────┤
│ ARCHITECTURE  │ System diagrams, data flow, design     │
│ REVIEWER      │ rationale, trade-off documentation.    │
└───────────────┴────────────────────────────────────────┘
```

### Example Density

```
README         — 0 code examples (links only)
INSTALLATION   — 5-8 shell commands
QUICKSTART     — 3-4 complete PHP files
ARCHITECTURE   — 2-3 annotated code excerpts
COMPONENT DOCS — 3-5 examples per doc (signatures + usage)
EXAMPLES       — 9 complete multi-file walkthroughs
SECURITY       — 3-5 prescriptive examples
CONVENTIONS    — 1 example per convention
CONTRIBUTING   — 2-3 command examples
```

### Code Block Conventions

```
PHP code:           ```php
Shell commands:     ```bash
SQL:                ```sql
Config files:       ```php (they are PHP)
ASCII diagrams:     ```text or ``` (plain)
File paths:         inline code `path/to/file`
Class names:        inline code `ClassName`
Method names:       inline code `methodName()`
```

### Diagram Format

```
ALL DIAGRAMS:       ASCII art using box-drawing characters
                    ┌─ ─ ├── ─── └── │ ▼ → ← ↓
NO:                 Mermaid, PlantUML, or image embeds
RATIONALE:          Viewable in any text editor, no rendering deps
```

### Terminology Consistency

```
CANONICAL TERMS (use these exactly):
  "container"      — NOT "service locator", "DI container", "IoC"
  "route"          — NOT "endpoint", "URL"
  "middleware"      — NOT "filter", "interceptor"
  "handler"        — NOT "action" (except in view context: {action}.php)
  "model"          — NOT "entity", "record"
  "service"        — NOT "manager", "handler"
  "view"           — NOT "template" (template = the .php file, view = the class)
  "layout"         — NOT "master template", "wrapper"
  "partial"        — NOT "component", "fragment"
  "binding"        — NOT "registration", "definition"
  "dispatch"       — NOT "handle", "process"
  "promote"        — for constructor property promotion
  "facade"         — for static-method classes (Session, Logger, etc.)

TODO STATUS MARKERS:
  [NOT YET IMPLEMENTED] — for documented but unimplemented features
  [STUB]                — for methods with placeholder returns
  [UNVERIFIED CONTRACT] — for inferred behavior without code evidence
```

---

## 7. Risk Surface Analysis

```
┌─────────────────────────────────────────┬──────────┬────────────────────────────────────────────────┐
│ Risk                                    │ Severity │ Mitigation                                     │
├─────────────────────────────────────────┼──────────┼────────────────────────────────────────────────┤
│ Docs describe TODO methods as if        │ CRITICAL │ Every TODO method MUST be marked               │
│ they are working — user follows docs    │          │ [NOT YET IMPLEMENTED] with its stub return     │
│ and gets null/false/empty               │          │ value documented.                              │
├─────────────────────────────────────────┼──────────┼────────────────────────────────────────────────┤
│ Method signature in docs drifts from    │ CRITICAL │ Phase 8 audit: verify every signature against  │
│ actual code after implementation        │          │ source via grep/reflection.                    │
├─────────────────────────────────────────┼──────────┼────────────────────────────────────────────────┤
│ Code examples reference classes/files   │ HIGH     │ Every file path in examples validated against  │
│ that don't exist                        │          │ actual directory tree.                         │
├─────────────────────────────────────────┼──────────┼────────────────────────────────────────────────┤
│ Inferred behavior documented as fact    │ HIGH     │ Use [UNVERIFIED CONTRACT] marker for any       │
│ (e.g., Validator rules not defined)     │          │ behavior inferred but not in code.             │
├─────────────────────────────────────────┼──────────┼────────────────────────────────────────────────┤
│ Security docs give false sense of       │ HIGH     │ Security doc MUST note which mechanisms are    │
│ security when middleware is stubbed     │          │ stubs. Include "CURRENT STATUS" per feature.   │
├─────────────────────────────────────────┼──────────┼────────────────────────────────────────────────┤
│ QUICKSTART example doesn't work         │ MEDIUM   │ Validate against running dev server.           │
│ because dependent code is TODO          │          │ Use WelcomeController (implemented) as base.   │
├─────────────────────────────────────────┼──────────┼────────────────────────────────────────────────┤
│ Cross-links between docs are broken     │ MEDIUM   │ Phase 8 link audit — automated.               │
│ after file renames                      │          │                                                │
├─────────────────────────────────────────┼──────────┼────────────────────────────────────────────────┤
│ Indonesian text in error views not      │ LOW      │ Note in VIEWS and ERROR_HANDLING docs that     │
│ documented — confuses English readers   │          │ default error messages are in Indonesian.      │
├─────────────────────────────────────────┼──────────┼────────────────────────────────────────────────┤
│ Missing examples for edge cases         │ LOW      │ Focus examples on happy paths. Note edge       │
│ (e.g., nested groups, parameter         │          │ cases in Gotchas sections.                     │
│ collisions)                             │          │                                                │
└─────────────────────────────────────────┴──────────┴────────────────────────────────────────────────┘
```

---

## 8. Validation Pipeline

```
                    ┌─────────────────────┐
                    │    Draft Written     │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │  Contract Audit      │
                    │                     │
                    │  For each doc:      │
                    │  • Every method     │
                    │    signature matches│
                    │    source file      │
                    │  • Every return     │
                    │    type matches     │
                    │  • Every CONTRACT   │
                    │    comment from     │
                    │    source reflected │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │  Cross-Link Audit    │
                    │                     │
                    │  • Every [link]()   │
                    │    resolves to      │
                    │    existing file    │
                    │  • No orphan docs  │
                    │    (every doc linked│
                    │    from README)     │
                    │  • No circular deps│
                    │    in writing order │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │ Code Consistency     │
                    │ Audit               │
                    │                     │
                    │  • Every PHP code   │
                    │    block passes     │
                    │    php -l           │
                    │  • Every file path  │
                    │    exists in repo   │
                    │  • Every class name │
                    │    exists in src/   │
                    │  • TODO status      │
                    │    markers are      │
                    │    accurate         │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │ Completeness Audit   │
                    │                     │
                    │  • All 15 docs      │
                    │    exist            │
                    │  • All required     │
                    │    sections present │
                    │  • All mandatory    │
                    │    code examples    │
                    │    included         │
                    │  • All required     │
                    │    diagrams present │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │  Final Approval      │
                    │                     │
                    │  • No contradiction │
                    │    between docs     │
                    │  • Onboarding path  │
                    │    walkable end-to- │
                    │    end              │
                    │  • Style system     │
                    │    consistently     │
                    │    applied          │
                    └─────────────────────┘
```

### Automated Validation Commands

```bash
# 1. Verify all doc files exist
ls docs/README.md docs/INSTALLATION.md docs/QUICKSTART.md \
   docs/ARCHITECTURE.md docs/CONTAINER.md docs/DATABASE.md \
   docs/VIEWS.md docs/ROUTING.md docs/MIDDLEWARE.md \
   docs/VALIDATION.md docs/ERROR_HANDLING.md docs/SECURITY.md \
   docs/CONVENTIONS.md docs/EXAMPLES.md docs/CONTRIBUTING.md

# 2. Check for broken internal links
grep -roh '\[.*\](.*\.md)' docs/ | grep -oP '\(.*?\)' | tr -d '()' | \
  while read f; do [ ! -f "docs/$f" ] && echo "BROKEN: $f"; done

# 3. Extract PHP code blocks and lint them
# (manual process — extract fenced php blocks, write to temp, php -l)

# 4. Verify file paths mentioned in docs exist in repo
grep -roh '`[a-zA-Z/]*\.php`' docs/ | tr -d '`' | \
  while read f; do [ ! -f "$f" ] && echo "MISSING: $f"; done

# 5. Verify TODO status markers are accurate
grep -rn 'NOT YET IMPLEMENTED' docs/ | \
  while read line; do echo "VERIFY: $line"; done
```

---

## 9. Definition of Done

### Document Completeness

- [ ] All 15 documentation files exist in `docs/`
- [ ] README.md contains documentation index linking to all 14 other docs
- [ ] Every doc contains all required sections per Section 5 contracts
- [ ] Every doc contains all mandatory code examples
- [ ] Every doc contains all required diagrams
- [ ] Every doc contains all required cross-links

### Code Accuracy

- [ ] Every method signature in docs matches source code exactly
- [ ] Every return type in docs matches source code
- [ ] Every file path in docs resolves to an existing file
- [ ] Every class name in docs resolves to an existing class
- [ ] Every CONTRACT comment from source code is reflected in docs
- [ ] Every PHP code block passes `php -l` syntax validation
- [ ] TODO-status features consistently marked as `[NOT YET IMPLEMENTED]`
- [ ] `[UNVERIFIED CONTRACT]` used for inferred behavior without code evidence

### Architecture Accuracy

- [ ] Request lifecycle diagram matches actual code flow in `public/index.php` → `bootstrap/app.php` → `Router::dispatch()`
- [ ] Error propagation diagram matches `set_exception_handler` in `bootstrap/app.php`
- [ ] Exception hierarchy matches `src/Exceptions/` class files
- [ ] Route table matches `config/routes.php`
- [ ] Binding table matches `config/bindings.php`
- [ ] Middleware alias table matches `public/index.php`

### Onboarding Path

- [ ] README → INSTALLATION → QUICKSTART reading path is linear and unblocked
- [ ] INSTALLATION produces a running dev server
- [ ] QUICKSTART produces a visible result in browser
- [ ] ARCHITECTURE provides understanding sufficient to navigate all topic docs

### Cross-Link Integrity

- [ ] Every inter-document link resolves (no broken links)
- [ ] Every doc is reachable from README documentation index
- [ ] No orphan docs (unreachable from any other doc)
- [ ] Writing phase dependencies (Section 3) were followed — no doc references content from a later phase

### Style Consistency

- [ ] Terminology from Section 6 used consistently across all docs
- [ ] Code block language tags applied per Section 6 conventions
- [ ] All diagrams are ASCII (no external renderers required)
- [ ] Tone is technical and direct per Section 6 register
- [ ] `[NOT YET IMPLEMENTED]`, `[STUB]`, `[UNVERIFIED CONTRACT]` markers used consistently

### Contributor Readiness

- [ ] CONTRIBUTING.md references correct grep command for finding TODOs
- [ ] CONTRIBUTING.md phase ordering matches IMPLEMENTATION_PLAN.md
- [ ] CONVENTIONS.md conventions match observed patterns in at least one source file each
- [ ] Pull request checklist is actionable and specific

### Security Documentation

- [ ] All 9 security contracts from BRIEF_RECONSTRUCTED.md are covered
- [ ] Each security mechanism notes its current implementation status (implemented vs stub)
- [ ] No false claims of protection from stubbed middleware
