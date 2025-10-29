# Contributing to My Learning Journal

Thank you for your interest in contributing to My Learning Journal! This document provides guidelines and instructions for contributing to this Laravel + Vue.js blog application.

## Table of Contents

-   [Code of Conduct](#code-of-conduct)
-   [Getting Started](#getting-started)
-   [Development Setup](#development-setup)
-   [Project Structure](#project-structure)
-   [Development Guidelines](#development-guidelines)
-   [Testing](#testing)
-   [Pull Request Process](#pull-request-process)
-   [Coding Standards](#coding-standards)
-   [Commit Message Guidelines](#commit-message-guidelines)

## Code of Conduct

By participating in this project, you agree to maintain a respectful and inclusive environment. Please be considerate of others and contribute positively to the community.

## Getting Started

### Prerequisites

Before contributing, ensure you have the following installed:

-   **PHP 8.2+** with extensions: mbstring, intl, pdo, pdo_mysql, xml, zip, bcmath
-   **Composer** (latest version)
-   **Node.js 18+** and **NPM**
-   **MySQL 8+** or **PostgreSQL**
-   **Git**

### Fork and Clone

1. Fork the repository on GitHub
2. Clone your fork locally:
    ```bash
    git clone https://github.com/YOUR-USERNAME/my-learning-journal-task.git
    cd my-learning-journal-task
    ```

## Development Setup

### Option 1: Local Development

1. **Install PHP dependencies:**

    ```bash
    composer install
    ```

2. **Install NPM dependencies:**

    ```bash
    npm install
    ```

3. **Environment setup:**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Configure database in `.env`:**

    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=learning_journal
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

5. **Run migrations and seeders:**

    ```bash
    php artisan migrate --seed
    ```

6. **Start development servers:**

    ```bash
    # Terminal 1: Laravel backend
    php artisan serve

    # Terminal 2: Vite frontend
    npm run dev
    ```

### Option 2: Docker with Laravel Sail

1. **Copy environment file:**

    ```bash
    cp .env.example .env
    ```

2. **Start Sail containers:**

    ```bash
    ./vendor/bin/sail up -d
    ```

3. **Install dependencies:**

    ```bash
    ./vendor/bin/sail composer install
    ./vendor/bin/sail npm install
    ```

4. **Setup application:**

    ```bash
    ./vendor/bin/sail artisan key:generate
    ./vendor/bin/sail artisan migrate --seed
    ```

5. **Start frontend development:**
    ```bash
    ./vendor/bin/sail npm run dev
    ```

## Project Structure

This project follows Laravel conventions with some modern additions:

```
├── app/
│   ├── Http/Controllers/     # Request handling
│   ├── Models/              # Eloquent models (uses ULIDs)
│   ├── Services/            # Business logic layer
│   ├── Observers/           # Model observers
│   └── Policies/            # Authorization policies
├── database/
│   ├── migrations/          # Database schema
│   ├── factories/           # Model factories
│   └── seeders/             # Database seeders
├── resources/
│   ├── js/                  # Vue.js frontend
│   │   ├── Components/      # Vue components
│   │   ├── Pages/          # Inertia.js pages
│   │   └── Layouts/        # Page layouts
│   └── css/                # Styles (Tailwind CSS)
├── tests/
│   ├── Feature/            # Integration tests
│   └── Unit/               # Unit tests
└── routes/                 # Route definitions
```

### Key Technologies

-   **Backend:** Laravel 11, PHP 8.2+
-   **Frontend:** Vue.js 3, Inertia.js, Tailwind CSS
-   **Database:** Uses ULIDs as primary keys
-   **Testing:** Pest framework
-   **Build:** Vite
-   **Formatting:** Prettier, Pint (PHP)

## Development Guidelines

### Feature Development

1. **Create a feature branch:**

    ```bash
    git checkout -b feat/your-feature-name
    ```

2. **Follow the established patterns:**

    - Use service classes for business logic
    - Implement observers for model events
    - Follow ULID usage for primary keys
    - Use proper request validation
    - Include appropriate tests

3. **Key conventions:**
    - Models use `HasUlids` trait
    - Service layer handles business queries
    - Controllers are thin and delegate to services
    - Use proper type declarations (`declare(strict_types=1);`)

### Database Guidelines

-   **Primary Keys:** Always use ULIDs (`HasUlids` trait)
-   **Foreign Keys:** Reference ULID strings, not integers
-   **Migrations:** Use descriptive names and proper rollback methods
-   **Factories:** Provide realistic test data
-   **Seeders:** Create meaningful sample data

### Frontend Guidelines

-   **Components:** Create reusable Vue components
-   **Pages:** Use Inertia.js page components
-   **Styling:** Use Tailwind CSS utility classes
-   **State:** Manage state through props and Inertia shared data

## Testing

This project uses the Pest testing framework. All contributions must include appropriate tests.

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test files
php artisan test tests/Feature/TagTest.php

# Run with coverage (if configured)
php artisan test --coverage
```

### Writing Tests

-   **Feature Tests:** Test complete user workflows
-   **Unit Tests:** Test individual classes/methods
-   **Use factories:** Create test data with model factories
-   **Database:** Tests use `RefreshDatabase` trait

### Test Structure

```php
<?php

declare(strict_types=1);

use App\Models\BlogPost;
use App\Models\User;

test('user can create a blog post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/blog', [
        'title' => 'Test Post',
        'excerpt' => 'Test excerpt',
        'body' => 'Test content',
    ]);

    $response->assertRedirect();
    expect(BlogPost::count())->toBe(1);
});
```

### Frontend Testing

```bash
# Run JavaScript tests
npm run test
```

## Pull Request Process

### Before Submitting

1. **Ensure all tests pass:**

    ```bash
    php artisan test
    npm run test
    ```

2. **Run code formatting:**

    ```bash
    ./vendor/bin/pint  # PHP formatting
    npm run format     # JS/Vue formatting
    ```

3. **Run linting:**

    ```bash
    npm run lint:fix
    ```

4. **Update documentation if needed**

### PR Requirements

-   [ ] All tests pass
-   [ ] Code follows project conventions
-   [ ] Includes appropriate tests
-   [ ] Formatted with Pint and Prettier
-   [ ] Descriptive commit messages
-   [ ] Updated documentation if needed

### PR Description Template

```markdown
## Description

Brief description of changes

## Type of Change

-   [ ] Bug fix
-   [ ] New feature
-   [ ] Breaking change
-   [ ] Documentation update

## Testing

-   [ ] Tests pass locally
-   [ ] Added new tests for feature
-   [ ] Updated existing tests

## Checklist

-   [ ] Code follows project style guidelines
-   [ ] Self-review completed
-   [ ] Documentation updated
```

## Coding Standards

### PHP Standards

-   **PHP Version:** 8.2+ (as required in composer.json)
-   **Style:** Laravel conventions + strict types
-   **Formatting:** Use Laravel Pint
-   **Type Declarations:** Always use `declare(strict_types=1);`

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ExampleModel extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
```

### JavaScript/Vue Standards

-   **ES6+:** Use modern JavaScript features
-   **Formatting:** Prettier with Tailwind plugin
-   **Components:** Single File Components (.vue)
-   **Composition API:** Preferred over Options API

```vue
<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    post: Object,
});

const form = useForm({
    title: props.post?.title || '',
    content: props.post?.content || '',
});
</script>

<template>
    <div class="mx-auto max-w-2xl">
        <!-- Component content -->
    </div>
</template>
```

### CSS Standards

-   **Framework:** Tailwind CSS
-   **Responsive:** Mobile-first approach
-   **Components:** Use @apply for component classes when needed

## Commit Message Guidelines

Follow conventional commits format:

```
type(scope): description

body (optional)

footer (optional)
```

### Types

-   `feat`: New feature
-   `fix`: Bug fix
-   `docs`: Documentation changes
-   `style`: Code style changes
-   `refactor`: Code refactoring
-   `test`: Test additions/modifications
-   `chore`: Maintenance tasks

### Examples

```bash
feat(tags): add tagging system for blog posts

- Add Tag model with ULID support
- Implement many-to-many relationship with BlogPost
- Add tag management in BlogPostController
- Include comprehensive test coverage

Closes #123
```

```bash
fix(auth): resolve login redirect issue

The login redirect was not working correctly for
authenticated users trying to access the login page.

Fixes #456
```

## Getting Help

-   **Documentation:** Check the README.md for setup instructions
-   **Issues:** Search existing issues before creating new ones
-   **Discussions:** Use GitHub Discussions for questions
-   **Code Review:** Request reviews from maintainers

## Recognition

Contributors will be recognized in:

-   GitHub contributors page
-   Release notes for significant contributions
-   Project documentation

Thank you for contributing to My Learning Journal! 🚀
