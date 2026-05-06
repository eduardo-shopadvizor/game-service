# Architecture

## Overview

`game-service` follows **Hexagonal Architecture** (Ports & Adapters) with **CQRS** (Command Query Responsibility Segregation). The namespace root is `Saz\Game\`.

The core rule: **the Domain never depends on Infrastructure or UI**. Dependencies always point inward.

```
UI (GraphQL / Web)
      ↓
Application (Use Cases)
      ↓
Domain (Entities, Value Objects, Repository interfaces)
      ↑
Infrastructure (Doctrine implementations)
```

---

## Layer Breakdown

### Domain (`src/Domain/`)

Pure PHP — no framework, no Doctrine, no external dependencies.

| Component | Location | Description |
|---|---|---|
| `Game` | `Domain/Game/Model/Game.php` | Aggregate root |
| `GameId` | `Domain/Game/ValueObject/GameId.php` | UUID value object |
| `GameName` | `Domain/Game/ValueObject/GameName.php` | Name with validation |
| `GameDescription` | `Domain/Game/ValueObject/GameDescription.php` | Optional description |
| `GameGenreEnum` | `Domain/Game/Enum/GameGenreEnum.php` | `action \| rpg \| strategy \| sports \| adventure` |
| `GameRepositoryInterface` | `Domain/Game/Repository/GameRepositoryInterface.php` | Repository contract |
| `GameNotFoundException` | `Domain/Game/Exception/GameNotFoundException.php` | Domain exception |
| `EmptyValue` | `Domain/Common/EmptyValue.php` | Sentinel for partial updates |

#### Game entity

```php
Game::createFromPrimitives(string $id, string $name, ?string $description, string $genre, DateTimeInterface $createdAt, DateTimeInterface $updatedAt): Game
Game::update(GameName|EmptyValue, GameDescription|EmptyValue|null, GameGenreEnum|EmptyValue): void
Game::toPrimitives(): array
```

`EmptyValue` is used in `update()` to distinguish "field not sent" from `null`, enabling true partial updates without ambiguity.

---

### Application (`src/Application/`)

Use cases organised by feature. Each feature directory contains a **Query/Command DTO**, a **UseCase class**, and a **Handler** (Symfony Messenger `#[AsMessageHandler]`).

#### Implemented use cases

| Feature | Query/Command | UseCase | Handler |
|---|---|---|---|
| Find game by ID | `GetGameByIdQuery` | `FindGame` | `GetGameByIdQueryHandler` |
| List games (with optional genre filter) | `ListGamesQuery` | `ListGames` | `ListGamesQueryHandler` |

#### Patterns

- **Handler** receives the DTO, converts primitive values to Value Objects, then delegates to the UseCase.
- **UseCase** contains domain logic and calls the repository interface.
- All classes are `final`.

---

### Infrastructure (`src/Infrastructure/`)

Framework-specific implementations hidden from the domain.

#### Repository

`DoctrineGameRepository` implements `GameRepositoryInterface` extending `DoctrineRepository<Game>` from the shared context.

| Method | Description |
|---|---|
| `find(GameId)` | Find by primary key |
| `findOneByName(GameName)` | Find by exact name (case-sensitive LIKE) |
| `matching(Criteria)` | Generic filtered list |
| `count(Criteria)` | Count matching records |
| `save(Game)` | Persist + flush |
| `delete(Game)` | Remove + flush |

#### Doctrine Types

Custom types map Value Objects and Enums to database columns:

| Type name | Class | Maps |
|---|---|---|
| `game_id` | `GameIdType` | UUID string |
| `game_name` | `GameNameType` | String |
| `game_description` | `GameDescriptionType` | String (nullable) |
| `game_genre` | `GameGenreType` | Enum backing string |

#### XML Mapping

`src/Infrastructure/Resources/config/doctrine/Game/Game.orm.xml` maps the `Game` entity to the `games` table. Indexes on `genre` and `created_at`.

#### Fixtures

`GameFixtures` loads 25 real games (5 per genre) with random dates via Faker. Only available in `dev` and `test` environments.

---

### UI (`src/UI/`)

Two independent adapters — GraphQL and Web — sharing the same Application layer.

#### GraphQL (`src/UI/GraphQL/`)

Built with OverblogGraphQL + SazHelixGraphQL bundles.

| Component | Location | Role |
|---|---|---|
| `GameQuery` | `Resolver/Query/GameQuery.php` | Resolves `query { game(id) }` |
| `GameResolverMap` | `ResolverMap/GameResolverMap.php` | Wires resolvers to the schema |
| `FieldResolver` | `FieldResolver/FieldResolver.php` | Converts `BackedEnum` to string for GraphQL |

Schema files in `src/UI/Resources/GraphQL/`.

#### Web Catalog (`src/UI/Web/`)

Symfony controller + Twig templates with Tailwind CSS (CDN).

| Component | Location | Role |
|---|---|---|
| `CatalogController` | `Web/Controller/CatalogController.php` | `GET /catalog` and `GET /catalog/{id}` |
| `base.html.twig` | `Web/Resources/views/base.html.twig` | Layout |
| `catalog/index.html.twig` | `Web/Resources/views/catalog/index.html.twig` | Game grid with genre filters |
| `catalog/show.html.twig` | `Web/Resources/views/catalog/show.html.twig` | Game detail page |
| `components/game_card.html.twig` | `Web/Resources/views/components/` | Reusable card component |

---

## Dependency Rule Summary

```
Domain        ← no external dependencies
Application   ← Domain only
Infrastructure← Domain + Doctrine/Symfony
UI            ← Application + Symfony/Twig/GraphQL
```

---

## Key Design Decisions

| Decision | Reason |
|---|---|
| `EmptyValue` sentinel in `update()` | Distinguishes "not sent" from `null` without making all fields nullable |
| XML Doctrine mappings (not annotations) | Keeps the Domain entity free of infrastructure annotations |
| `final` on all use case classes | Prevents unintended inheritance that could break invariants |
| Separate `ListGames` + `FindGame` use cases | Each use case has a single, clear responsibility |
| Tailwind via CDN | No Node.js build step needed for the demo web catalog |
