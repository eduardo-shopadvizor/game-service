# Setup

## Prerequisites

- PHP >= 8.4
- [Symfony CLI](https://symfony.com/download)
- PostgreSQL 16
- Composer 2.x

## Installation

```bash
# Clone the repository
git clone <repo-url>
cd <project-directory>

# Copy environment file
cp .env.dev .env.local

# Edit .env.local with your database credentials
# DATABASE_URL="postgresql://user:password@127.0.0.1:5432/dbname"

# Install dependencies
composer install

# Create database
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# (Optional) Load dev fixtures
php bin/console doctrine:fixtures:load
```

## Verification

```bash
# Run tests
composer test

# Run linter
composer lint

# Start the dev server
php bin/console server:start
# or
symfony server:start
```

## Environment variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DATABASE_URL` | PostgreSQL DSN | `postgresql://...` |
| `APP_ENV` | Application environment | `dev` |
| `MESSENGER_TRANSPORT_DSN` | Message queue transport | `doctrine://default` |
