# Copilot / AI agent instructions — My Learning Journal

Purpose: short, actionable notes to help an AI coding agent be productive in this Laravel + Inertia (Vue3) repo.

1. Big picture

-   Laravel 11 backend (PHP 8.2) + Inertia.js + Vue 3 frontend. Server-side routing and controllers return Inertia pages under `resources/js/Pages`.
-   Primary domain: blog posts. Data flow: HTTP request -> Controller (e.g. `app/Http/Controllers/BlogPostController.php`) -> Service layer (`app/Services/BlogPostServices.php`) -> Model (`app/Models/BlogPost.php`) -> Spatie Media handling -> Inertia response -> Vue page.

2. Key conventions and patterns (use these exactly)

-   ULID primary keys: models use `HasUlids` (see `app/Models/BlogPost.php`, `app/Models/Media.php`, `app/Models/User.php`). IDs are strings.
-   Observer-driven business logic: `app/Observers/BlogPostObserver.php` contains slug generation, `is_featured` and `published_at` rules. Modify observers for cross-entity invariants instead of scattering logic across controllers.
-   Service layer: business queries / pagination live in `app/Services/BlogPostServices.php`. Controllers inject services via constructor DI (readonly typed properties).
-   Media: Spatie Media Library is used. Blog images use `images` collection and define conversions in `BlogPost::registerMediaConversions()`.
-   Authorization: policies exist (see `app/Policies/BlogPostPolicy.php`) and controllers use `Gate::authorize` or `authorize()`; prefer policy checks (not ad-hoc `auth` checks).

3. Typical change examples (explicit pointers)

-   Add a new field to posts: update migration, `app/Models/BlogPost.php` `$fillable` and casts, update `CreateBlogPostRequest`/`UpdateBlogPostRequest`, update `app/Services/BlogPostServices.php` where queries are built, and update `resources/js/Pages/Blog/*` forms.
-   Make tag feature (where this branch is headed): add pivot model/table, ensure `ulid()` keys in migration, update `BlogPost` relations, and update `BlogPostObserver` if tag-related invariants required.

4. Build / run / test (exact commands)

-   Install PHP deps: `composer install`
-   Install JS deps: `npm install`
-   Run dev server (local): `php artisan serve` (visits `http://localhost:8000`)
-   Using Sail (docker): `./vendor/bin/sail up` then prefix artisan/npm with `./vendor/bin/sail` (e.g. `./vendor/bin/sail artisan migrate --seed`).
-   Run DB migrations + seed: `php artisan migrate --seed` (or via Sail: `./vendor/bin/sail artisan migrate --seed`).
-   Run PHP tests (Pest/phpunit): `./vendor/bin/pest` or `./vendor/bin/phpunit`
-   Run JS build/dev: `npm run dev` / `npm run build`
-   Run JS tests: `npm run test` (vitest)
-   Lint/format: `npm run lint` / `npm run lint:fix` / `npm run format` and PHP formatting with `./vendor/bin/pint`

5. Testing caveats

-   `phpunit.xml` sets test envs but DB in-memory is commented out. For fast, isolated tests prefer setting env `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` or creating `database/database.sqlite` and point `DB_DATABASE` to it. README demonstrates both approaches.
-   Factories and seeders exist (`database/factories`, `database/seeders`). Use them to create fixtures rather than relying on production seed data.

6. Files to open first for most tasks

-   Domain logic: `app/Models/BlogPost.php`, `app/Observers/BlogPostObserver.php`, `app/Services/BlogPostServices.php`
-   Controller surface: `app/Http/Controllers/BlogPostController.php`
-   Frontend pages: `resources/js/Pages/Blog/*` (Inertia components match controller views by name)
-   Requests / validation: `app/Http/Requests/CreateBlogPostRequest.php`, `UpdateBlogPostRequest.php`
-   Policies: `app/Policies/BlogPostPolicy.php`

7. Style & typing

-   Files use `declare(strict_types=1);` and typed properties/return types. Preserve types and add types to new functions.
-   Follow existing resource usage: controllers return `PreviewBlogPostResource` / `FullBlogPostResource` for API shape consistency.

8. Troubleshooting hints

-   When debugging missing images: check `registerMediaCollections()` / `registerMediaConversions()` in `BlogPost` and where `addImage()` is called in `BlogPostController::store` / `update`.
-   When slug collisions occur, `BlogPostObserver::generateSlug()` contains the canonical logic — update that rather than ad-hoc slug code.

9. What not to change without discussion

-   ULID primary keys and the migrations that depend on them. Changing id types affects many files.
-   Observer rules that enforce single `is_featured` — these are global invariants implemented in `BlogPostObserver`.

If anything above is unclear or you'd like me to expand any section (examples for tag implementation, test setup, or editor commands), tell me which area to expand and I'll iterate.
