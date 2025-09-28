# Copilot Instructions for Finance 03 Work (Laravel Project)

## Project Architecture Overview

- **Framework:** Laravel (PHP)
- **Structure:**
  - `app/Http/Controllers/` — Handles HTTP requests, business logic, and view rendering
  - `app/Models/` — Eloquent ORM models for database tables
  - `resources/views/` — Blade templates for UI
  - `routes/web.php` — Route definitions, grouped by feature
  - `config/` — Configuration files (custom options, payment methods, etc.)
  - `database/migrations/` — Schema definitions
  - `database/seeders/` — Initial data population
  - `public/` — Static assets
- **Data Flow:**
  - Controllers receive requests, validate input, interact with models, and return views or redirects.
  - Models define relationships and encapsulate data logic.
  - Blade views use passed variables and localization for rendering.

## Coding Style Guide

- **Controllers:**
  - Use dependency injection for models/services when possible.
  - Validate requests using `$request->validate([...])`.
  - Use Eloquent relationships for data access (`hasMany`, `belongsTo`, etc.).
  - Return views with `compact()` for passing variables.
  - Use named routes for redirects.
  - Use `@csrf` in forms and proper HTTP verbs (`POST`, `DELETE`, etc.).
  - Use `dump()` or `dd()` for debugging, but remove before production.

- **Models:**
  - Use Eloquent relationships and accessors.
  - Keep business logic in models when possible.
  - Use type hints and docblocks for clarity.

- **Blade Views:**
  - Use Blade directives (`@foreach`, `@if`, etc.) for control flow.
  - Use localization (`__()`) for all user-facing text.
  - Use `{{ }}` for output, avoid raw PHP echo.
  - Structure forms with proper labels, accessibility, and CSRF protection.
  - Use custom Blade components for repeated UI patterns.

- **Config Files:**
  - Store custom options (payment methods, app options) in config files for easy access and modification.

- **Database:**
  - Use migrations for schema changes.
  - Use seeders for demo/test data.
  - Use foreign keys and cascading deletes for integrity.

## Common Patterns in Controllers and Services

- **Validation:**
  - Always validate incoming requests in controllers.
  - Use Laravel's built-in validation rules.

- **Eloquent Relationships:**
  - Use `with()` for eager loading related models.
  - Use relationship methods for accessing related data.

- **CRUD Operations:**
  - Standard create, read, update, delete logic in controllers.
  - Use model factories and seeders for test data.

- **Redirects and Flash Messages:**
  - Use named routes for redirects.
  - Use `with('success', ...)` or `with('error', ...)` for user feedback.

- **Localization:**
  - Use translation files for all user-facing text.
  - Support multiple languages via config and Blade.

- **Pagination and Filtering:**
  - Use Eloquent's `paginate()` for lists.
  - Pass query parameters through pagination links.

- **Custom Config and Options:**
  - Store payment method types, app options, and other enums in config files.
  - Access via `config('payment-methods')` or similar.

- **Security:**
  - Use CSRF protection in all forms.
  - Use middleware for authentication and authorization.

## Additional Guidance for Copilot

- Follow Laravel conventions for naming, structure, and code organization.
- Use Blade components for reusable UI.
- Keep controllers focused on request handling; move business logic to models or services.
- Use config files for options that may change or be extended.
- Always validate and sanitize user input.
- Use localization for all text.
- Prefer Eloquent relationships for data access.
- Use migrations and seeders for database changes and test data.

---
This guide summarizes the architecture, style, and patterns of the Finance 03 Work Laravel project for Copilot and contributors. For details, see the source code and comments throughout the project.
