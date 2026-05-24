# Contributing

Thanks for your interest in improving SaaS API Monitor! This guide covers how to
get set up and what we expect in a pull request.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

## Before you open a PR

Run the same checks CI runs — all must pass:

```bash
vendor/bin/pint --test   # PHP code style (PHP-CS-Fixer)
composer test            # backend tests (PHPUnit)
npm run test             # frontend tests (Vitest)
npm run build            # assets compile
```

To auto-fix style issues, run `vendor/bin/pint` (without `--test`).

## Branch & commit conventions

- Branch names: `feat/...`, `fix/...`, `docs/...`, `ci/...`, `refactor/...`,
  `test/...`, `chore/...`.
- Commit messages follow [Conventional Commits](https://www.conventionalcommits.org/),
  e.g. `feat: add monitor pause endpoint`.
- Keep PRs focused; one logical change per PR where possible.

## Pull requests

- Target the `main` branch.
- Fill in the PR template (description, testing, checklist).
- Add or update tests for any behavior you change.
- A PR can be merged once CI is green and it has been reviewed.

## Reporting issues

Use the issue templates (bug report / feature request). Include reproduction
steps and expected vs. actual behavior for bugs.
