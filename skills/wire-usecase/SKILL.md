---
name: wire-usecase
description: >
  Complete guide for connecting Application use cases through Infrastructure
  (Doctrine repositories, custom types, XML mappings) and UI (GraphQL resolvers,
  schema, ResolverMap). Use this whenever a new use case in Application/ is
  ready and needs end-to-end wiring. Also applies when adding fields to existing
  types, modifying repository queries, or registering new GraphQL mutations.
  Trigger even if the use case is partially wired — this skill validates the
  full chain.
---

# Wiring a Use Case: Infrastructure + GraphQL

Use this guide when a use case in `Application/Game/<Feature>/` is implemented
and you need to connect it **downward** (persistence) and **upward** (GraphQL).

## Before starting

1. **Identify the operation type** — does it write (Command → Mutation) or
   read (Query → Query)?
2. **Check `GameRepositoryInterface`** — does the use case need a method that
   doesn't exist yet? Add it before touching infrastructure.
3. **Check `Game` entity getters** — GraphQL field resolvers call getters on
   the entity. Make sure every field you want to expose has one.

## Layer-by-layer wiring

Each layer has a single responsibility. Infrastructure translates between
domain objects and the database; UI translates between GraphQL wire format
and application commands. Keeping the layers separate means you can change
the database engine or the API protocol without touching business logic.

### 1. Infrastructure — repository

The repository extends the shared `DoctrineRepository` base:

| File | Location |
|------|----------|
| Interface | `src/Domain/Game/Repository/GameRepositoryInterface.php` |
| Implementation | `src/Infrastructure/Repository/DoctrineGameRepository.php` |

Key rules:
- Extend `DoctrineRepository`, pass `Game::class` to parent.
- Use `$this->persist()` / `$this->remove()` from the base class (no direct `flush()`).
- For custom filtering, override `createQueryBuilderForCriteria()`.

### 2. Infrastructure — Doctrine types

Create one Type per Value Object that needs database mapping. Without custom
types, Doctrine stores raw primitives and loses Value Object semantics at the
boundary — meaning invalid values could be read back from the database without
going through domain validation.

| Value Object Type | Base class to extend |
|-------------------|---------------------|
| ID | `IdType` (from shared context) |
| String value | `StringValueObjectType` (from shared context) |
| Backed Enum | `StringType` (Doctrine's built-in) |

Register every custom type in `config/packages/doctrine.yaml`:

```yaml
doctrine:
    dbal:
        types:
            game_id:   Saz\Game\Infrastructure\Doctrine\Types\GameIdType
            game_name: Saz\Game\Infrastructure\Doctrine\Types\GameNameType
```

### 3. Infrastructure — XML mapping

One XML file per entity at `src/Infrastructure/Resources/config/doctrine/`.
XML mappings keep persistence configuration out of the domain entity — the
entity stays a plain PHP object with zero Doctrine annotations, which means
it can be tested without loading the ORM.
- Field `type` must match the Doctrine Type's `getName()` return value.
- Add `<index>` for columns you filter by frequently.
- Use `<join-table>` for many-to-many relations.

### 4. UI — GraphQL schema

The schema is the contract with clients. Defining it explicitly (rather than
auto-generating from PHP) means breaking changes are visible in code review
before they reach production.

| File | Purpose |
|------|---------|
| `types/game.graphql` | Output type (`Game`, `Games`) |
| `inputs/game.graphql` | Input types (`CreateGameInput`, `UpdateGameInput`) |
| `queries.graphql` | Root `type Query` entries |
| `mutations.graphql` | Root `type Mutation` entries |

Naming rules:
- Output fields that can be null → nullable in GraphQL (no `!`).
- `Update*Input` fields are always optional (nullable).
- Map enums to `String` (the FieldResolver converts via `BackedEnum::value`).

### 5. UI — Resolvers

Resolvers are intentionally thin — parsing input, calling a command or query,
and returning the result. Keeping business logic out of resolvers means the
same use case can be called from a REST controller or a CLI command without
duplication.

| Resolver | Base class | Method |
|----------|------------|--------|
| Query | `BaseQuery` | `$this->query(new MyQuery(...))` |
| Mutation (create) | `BaseMutation` | `$this->command(...)` then `$this->query(...)` to return created entity |
| Mutation (delete) | `BaseMutation` | `$this->command(...)` then `return true` |

All resolvers are thin — parse input, call command/query, return result.

### 6. UI — ResolverMap

Register all resolvers in `GameResolverMap`:

```php
$this->addQuery('game', $this->game);
$this->addMutation('createGame', $this->createGame);
```

Field names in `addQuery()` / `addMutation()` must match the schema exactly.

### 7. UI — FieldResolver

Created once per service at `src/UI/GraphQL/FieldResolver/FieldResolver.php`.
Converts `BackedEnum` values to strings automatically. Check if it exists
before creating it.

## Verification checklist

### Infrastructure
- [ ] Repository interface has every method the use case needs
- [ ] Doctrine repository implements all interface methods
- [ ] Custom Doctrine Type exists for each Value Object
- [ ] Enum Type correctly converts `BackedEnum ↔ string`
- [ ] XML mapping exists and matches the entity fields
- [ ] Custom types registered in doctrine.yaml
- [ ] Many-to-many joins include `<join-table>` with cascades

### UI / GraphQL
- [ ] Output type defined in `types/game.graphql`
- [ ] Input types defined in `inputs/game.graphql`
- [ ] Field declared in `queries.graphql` or `mutations.graphql`
- [ ] Resolver class exists in the correct directory
- [ ] Resolver registered in `GameResolverMap` with exact field name
- [ ] `FieldResolver.php` exists (created once)

### Tests
- [ ] Unit test for CommandHandler / QueryHandler exists
- [ ] Integration test for new repository methods if applicable
- [ ] `composer lint` passes (PHPStan level 8 + cs-fixer)

## Reference

See `scripts/generate-crud.sh` for scaffolding resolvers, schemas, and
resolver map entries automatically.
