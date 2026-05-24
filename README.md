# SaaS API Monitor

[![CI](https://github.com/OleksiiNenko/saas-api-monitor/actions/workflows/ci.yml/badge.svg)](https://github.com/OleksiiNenko/saas-api-monitor/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

A small Laravel service for monitoring the availability of HTTP/API endpoints.
You register **monitors** (a URL, the HTTP method, the expected status code and a
check interval) and the service keeps track of them. This first milestone ships
the monitor management API; background health checks and a dashboard are planned
next.

## Tech stack

- **PHP 8.3** / **Laravel 12**
- **SQLite** by default (any Laravel-supported DB works)
- **Vite 7 + Tailwind 4** for the frontend
- Tests: **PHPUnit** (backend) and **Vitest** (frontend)
- Code style: **Laravel Pint** (a PHP-CS-Fixer wrapper)

## Requirements

- PHP 8.3+ with `pdo_sqlite`
- Composer
- Node.js 20.19+ (or 22.12+) and npm

## Getting started

```bash
composer setup       # install deps, create .env, key:generate, migrate, build assets
composer dev         # serve + queue + logs + vite (all-in-one dev runner)
```

Or step by step:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve
```

## API

Base prefix: `/api`. No authentication yet (planned for a later milestone).

| Method   | Endpoint              | Description                  |
|----------|-----------------------|------------------------------|
| `GET`    | `/api/monitors`       | List monitors (paginated)    |
| `POST`   | `/api/monitors`       | Create a monitor             |
| `GET`    | `/api/monitors/{id}`  | Show a single monitor        |
| `PATCH`  | `/api/monitors/{id}`  | Update a monitor             |
| `DELETE` | `/api/monitors/{id}`  | Delete a monitor (204)       |

### Monitor fields

| Field              | Type    | Rules / default                              |
|--------------------|---------|----------------------------------------------|
| `name`             | string  | required, max 255                            |
| `url`              | string  | required, valid URL, max 2048                |
| `method`           | string  | one of GET/HEAD/POST/PUT/PATCH/DELETE (`GET`)|
| `expected_status`  | int     | 100–599 (`200`)                              |
| `interval_seconds` | int     | min 30 (`300`)                               |
| `is_active`        | bool    | (`true`)                                     |

### Example

```bash
curl -X POST http://localhost:8000/api/monitors \
  -H 'Accept: application/json' \
  -d 'name=Checkout API&url=https://api.example.com/health&interval_seconds=60'
```

## Testing & quality

```bash
composer test            # PHPUnit
vendor/bin/pint --test   # PHP code style check (PHP-CS-Fixer under the hood)
npm run test             # Vitest (frontend)
npm run build            # ensure assets compile
```

These same checks run in CI on every pull request — see
[`.github/workflows/ci.yml`](.github/workflows/ci.yml).

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

[MIT](LICENSE)
