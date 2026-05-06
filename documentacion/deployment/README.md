# Deployment

## Environment Variables

| Variable | Required | Description |
|---|---|---|
| `DATABASE_URL` | Yes | PostgreSQL connection string |
| `APP_SECRET` | Yes | Symfony app secret (random string) |
| `APP_ENV` | Yes | `prod` / `dev` / `test` |
| `MESSENGER_TRANSPORT_DSN` | Yes | Async bus (Pub/Sub / Redis / AMQP) |
| `MESSENGER_TOPIC_PREFIX` | Yes | Format: `$EDITION.$CI_ENVIRONMENT.$SERVICE` |
| `GAME_CREATED_TOPIC` | Yes | Pub/Sub topic for game created events |
| `GAME_UPDATED_TOPIC` | Yes | Pub/Sub topic for game updated events |
| `GAME_DELETED_TOPIC` | Yes | Pub/Sub topic for game deleted events |

Copy `.env.dev` to `.env.local` for local development.

---

## Docker

### Build

```bash
docker build -t game-service .
```

The image uses `php:8.3-fpm-alpine` with `pdo_pgsql` and OPcache. Composer installs production dependencies only (`--no-dev --optimize-autoloader`). Exposes port `9000` (PHP-FPM).

### Run

```bash
docker run -p 9000:9000 \
  -e APP_ENV=prod \
  -e APP_SECRET=your-secret \
  -e DATABASE_URL="postgresql://user:pass@db:5432/games?serverVersion=16&charset=utf8" \
  game-service
```

A reverse proxy (Nginx / Caddy) must sit in front to serve static assets and forward PHP requests to FPM on port 9000.

---

## Database

### Migrations

```bash
# Generate a migration after schema changes
php bin/console doctrine:migrations:diff

# Apply pending migrations
php bin/console doctrine:migrations:migrate

# Check migration status
php bin/console doctrine:migrations:status
```

### Fixtures (dev only)

```bash
php bin/console doctrine:fixtures:load
```

Loads 25 sample games (5 per genre). Only available when `APP_ENV=dev` or `APP_ENV=test`.

---

## CI/CD (Bitbucket Pipelines)

The pipeline runs on every push:

```yaml
# bitbucket-pipelines.yml
image: php:8.3-cli

pipelines:
  default:
    - step:
        name: Test & Lint
        script:
          - composer install
          - composer lint      # phpcs + php-cs-fixer + phpstan level 8
          - composer test      # PHPUnit
```

**Requirements for merge:**
- `composer lint` passes (zero errors)
- `composer test` passes (all tests green)

---

## Health Check

```bash
# Verify the container is responding
curl http://localhost:8000/catalog

# Verify GraphQL endpoint
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ game(id: \"test\") { id } }"}'
```
