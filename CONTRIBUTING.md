# Contributing to Laravel Multi-Tenant SaaS Starter Kit

Thank you for your interest in contributing! This guide will help you get started.

## Getting Started

### Prerequisites

- PHP 8.4+
- Composer
- Node.js 18+
- Redis
- MySQL/PostgreSQL (or SQLite for development)

### Local Setup

```bash
# Fork and clone the repository
git clone https://github.com/<your-username>/laravel-multi-tenant-saas-starter.git
cd laravel-multi-tenant-saas-starter

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Run migrations and seed
php artisan migrate --seed

# Start the development server
composer dev
```

### Subdomain Configuration

For local development, add tenant subdomains to your hosts file:

```
127.0.0.1 localhost
127.0.0.1 demo.localhost
```

## How to Contribute

### Reporting Bugs

- Search [existing issues](https://github.com/alihamzahq/laravel-multi-tenant-saas-starter/issues) before creating a new one
- Include steps to reproduce, expected vs actual behavior, and your environment details (PHP version, OS, database)

### Suggesting Features

- Open an issue with the **feature request** label
- Describe the use case and how it fits into the multi-tenant architecture

### Submitting Changes

1. **Fork** the repository
2. **Create a branch** from `develop`:
   ```bash
   git checkout -b fix/your-fix develop
   ```
3. **Make your changes** following the guidelines below
4. **Test your changes**:
   ```bash
   composer test
   ```
5. **Commit** with a clear message:
   ```bash
   git commit -m "fix: resolve tenant migration issue"
   ```
6. **Push** and open a Pull Request against the `develop` branch

## Coding Standards

### PHP

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style
- Use [Laravel Pint](https://laravel.com/docs/pint) for formatting:
  ```bash
  ./vendor/bin/pint
  ```
- Place central logic in `app/Http/Controllers/Central/` and tenant logic in `app/Http/Controllers/Tenant/`
- Use service classes in `app/Services/` for business logic

### JavaScript / React

- Use functional components with hooks
- Keep tenant and central pages separated under `resources/js/Pages/`
- Use Tailwind CSS utility classes for styling

### Migrations

- Central migrations go in `database/migrations/`
- Tenant migrations go in `database/migrations/tenant/`

## Commit Message Convention

Use [Conventional Commits](https://www.conventionalcommits.org/):

| Prefix     | Purpose                          |
|------------|----------------------------------|
| `feat:`    | New feature                      |
| `fix:`     | Bug fix                          |
| `docs:`    | Documentation changes            |
| `refactor:`| Code refactoring                 |
| `test:`    | Adding or updating tests         |
| `chore:`   | Maintenance tasks                |

## Pull Request Guidelines

- Target the `develop` branch (not `main`)
- Keep PRs focused — one feature or fix per PR
- Include a clear description of what changed and why
- Ensure all tests pass before requesting review
- Update documentation if your change affects usage

## Project Architecture

Before contributing, familiarize yourself with the project structure:

- **Multi-tenancy** is handled by [Stancl/Tenancy](https://tenancyforlaravel.com/) with database-per-tenant isolation
- **Frontend** uses React with Inertia.js (no separate SPA — server-driven routing)
- **API** uses Laravel Sanctum with separate central and tenant endpoints
- **Routing** is split across `web.php`, `tenant.php`, `api.php`, and `api-tenant.php`

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](LICENSE).
