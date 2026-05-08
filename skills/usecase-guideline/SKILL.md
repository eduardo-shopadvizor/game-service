---
name: usecase-guideline
description: >
  Rules for creating use cases in the Application layer (Commands and Queries)
  following hexagonal architecture and CQRS. Use this whenever you need to
  create a new use case — whether it's a write operation (Create, Update, Delete)
  or a read operation (Get, List, Search). Also applies when modifying existing
  use cases to ensure they stay consistent with the established patterns.
---

# Use Case Guidelines

## Why this structure exists

Every use case in this project follows the same three-part pattern:
**Command/Query → Handler → UseCase**. This separation exists for three
reasons:

1. **Testability.** Each piece can be unit-tested in isolation. The UseCase
   tests business rules, the Handler tests primitive-to-ValueObject conversion,
   and the Command/Query is a plain DTO that needs no tests.
2. **Flexibility.** Handlers act as an adapter layer. If a Value Object
   constructor changes, only the Handler needs updating — the UseCase stays
   untouched.
3. **CQRS clarity.** By keeping Commands (writes) separate from Queries
   (reads), the code makes its intent obvious. You never accidentally
   modify state in a read operation.

## Structure

### Command / Query — Input DTO

A plain PHP class with `public readonly` properties. Contains only primitives
(strings, ints, bools) — no Value Objects, no business logic.

**Input:** A user request (HTTP, CLI, message queue) with raw values.
**Output:** A DTO the Handler can unpack.

```php
final class CreateGameCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
    ) {}
}
```

```php
final class FindGameQuery
{
    public function __construct(
        public readonly string $id,
    ) {}
}
```

### Handler — Primitive-to-Value Object conversion

The Handler is the **only** place that converts primitives to Value Objects.
It contains zero business logic — it delegates everything to the UseCase.

**Naming:** `{Action}{Entity}CommandHandler` or `{Action}{Entity}QueryHandler`

```php
final class CreateGameCommandHandler
{
    public function __construct(
        private readonly CreateGameUseCase $useCase,
    ) {}

    public function __invoke(CreateGameCommand $command): void
    {
        $this->useCase->create(
            id: new GameId($command->id),
            name: new GameName($command->name),
            description: new GameDescription($command->description),
        );
    }
}
```

### Use Case — Business logic

Contains the actual application logic: validation, coordination between
repositories and services, event dispatching. Works exclusively with
Value Objects and Domain interfaces.

**Naming:** `{Action}{Entity}` (no suffix)

```php
final class CreateGameUseCase
{
    public function __construct(
        private readonly GameRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
    ) {}

    public function create(
        GameId $id,
        GameName $name,
        GameDescription $description,
    ): void {
        $game = Game::create(
            id: $id,
            name: $name,
            description: $description,
        );

        $this->repository->save($game);
        $this->eventBus->dispatch(new GameCreatedEvent($id));
    }
}
```

## Command vs Query

| | Command | Query |
|---|---|---|
| **Purpose** | Modify state | Read data |
| **Suffix** | `Command` | `Query` |
| **Handler suffix** | `CommandHandler` | `QueryHandler` |
| **Return** | `void` (or event) | Returns data |
| **Example** | `CreateGameCommand` | `FindGameQuery` |

## Critical rules

- **UseCase receives Value Objects, never primitives.** If a Handler passes
  raw strings to the UseCase, business rules (length limits, format checks)
  end up scattered across the Handler instead of living in the Value Object
  where they belong.

- **No business logic in Handlers.** Handlers are adapters — their only job
  is converting primitives to Value Objects. Logic there can't be reused by
  other delivery mechanisms (CLI, async workers) and is invisible to domain
  tests.

- **Commands don't return data; Queries don't modify state.** Mixing the two
  makes it impossible to reason about side effects and breaks any future
  attempt to route reads to a read-replica or cache layer.

- **Suffix classes with `Command`, `Query`, `CommandHandler`, `QueryHandler`.**
  Consistent naming means you can navigate to any use case by name alone,
  without reading the implementation to understand its intent.
