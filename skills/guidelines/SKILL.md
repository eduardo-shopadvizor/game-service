---
name: guidelines
description: >
  Project conventions for PHP/Symfony GraphQL services following hexagonal
  architecture. Use this whenever you are adding a new feature, fixing a bug,
  or refactoring in any repository under the Saz namespace. Also applies when
  evaluating whether a proposed pattern, library, or structure fits the
  project's conventions — if unsure, consult this skill first.
---

# Project Guidelines

## Why these conventions exist

Consistency across the codebase makes every developer faster. When all
modules follow the same patterns — same folder layout, same dependency
rules, same naming — you can jump into any part of the system and know
where to find things and how to extend them.

This project uses **hexagonal architecture** (ports & adapters) to keep
business logic decoupled from frameworks, databases, and external services.
That means:
- The **domain** stays pure PHP with zero framework dependencies.
- The **application** layer orchestrates use cases.
- **Infrastructure** plugs in concrete implementations.
- **UI** adapts to the delivery mechanism (GraphQL, CLI, etc.).

## Stack

| Component | Version / Value |
|-----------|----------------|
| PHP       | >= 8.3 |
| Framework | Symfony 7.4 |
| API       | GraphQL (Overblog) |
| Namespace base | `Saz\Game` |
| Architecture | Hexagonal / Clean Architecture |
| Testing | PHPUnit (unit + integration) |
| Static analysis | PHPStan level 8 |
| Code style | PSR-12 + shopadvizor/symfony-standards |

## Coding conventions

### Required in every file

- `declare(strict_types=1);`
- Maximum 120 characters per line

### Naming

| Element | Convention | Example |
|---------|-----------|---------|
| Classes | PascalCase | `CreateGameCommand` |
| Interfaces | PascalCase + `Interface` suffix | `GameRepositoryInterface` |
| Exceptions | PascalCase + `Exception` suffix | `GameNotFoundException` |
| Methods / variables | camelCase | `getById()` |
| Constants | UPPER_SNAKE_CASE | `MAX_PLAYERS` |

## Layer rules

### Domain layer (`src/Domain/`)

- Pure business logic. No framework, Doctrine, or Symfony imports.
- Value Objects encapsulate primitive validation.
- Repository interfaces define the contract (no implementations).
- Entities are rich domain objects, not anemic data bags.

**Why:** Keeping the domain pure means you can test business rules without
bootstrapping a database or Symfony kernel. It also means you can swap
infrastructure later (e.g., move from Doctrine to event sourcing) without
touching a single line of business logic.

### Application layer (`src/Application/`)

- Orchestrates use cases. Depends on Domain interfaces only.
- Splits into **Commands** (write) and **Queries** (read) — CQRS.
- Each use case = Command/Query + Handler + UseCase class.
- No web-related concepts (HTTP, sessions, routing) appear here.

**Why:** Separating reads from writes lets you optimize each path
independently. Commands can go through a bus (messenger, event sourcing)
while queries can hit read-optimized projections.

### Infrastructure layer (`src/Infrastructure/`)

- Implements Domain interfaces (Doctrine repositories, custom types).
- Depends on Application + Domain.
- No business logic here — only technical concerns.

**Why:** Infrastructure is a detail. By depending on Domain interfaces,
you can change databases, message queues, or caching layers without
affecting the business logic.

### UI layer (`src/UI/`)

- GraphQL resolvers (Query/Mutation), custom types, field resolvers.
- Depends on Application + Domain.
- Resolvers are thin — they parse input, call a command/query, and return
  the result. No business logic.

**Why:** Keeping resolvers thin means you can add a new delivery mechanism
(REST API, CLI commands, async workers) without duplicating use case logic.

## Testing conventions

- File naming: `{ClassName}Test`
- Method naming: `testShould{DoSomething}`
- Use Object Mothers (`*Mother`) for test data
- Strict assertions: `assertSame`, `assertInstanceOf`, not loose comparisons
- Mirror `src/` structure under `tests/`

## Before writing new code

1. Read at least 2-3 existing files in the same layer to match style.
2. Check if a repository interface already has the method you need.
3. Verify the Value Object you need doesn't already exist.
4. Run `composer lint` before considering code complete.
