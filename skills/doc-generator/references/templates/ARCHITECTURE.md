# Architecture

## Overview

<!--
Describe the system at a high level: what it does, what patterns it uses,
how it fits into the broader ecosystem.
-->

## Layers

### Domain (`src/Domain/`)

Pure business logic. No framework dependencies.

- Entities
- Value Objects
- Repository interfaces
- Domain events

### Application (`src/Application/`)

Use case orchestration (CQRS).

- Commands (writes)
- Queries (reads)
- Handlers
- Use Cases

### Infrastructure (`src/Infrastructure/`)

Concrete implementations of domain interfaces.

- Doctrine repositories
- Custom Doctrine types
- XML mappings
- Symfony Messenger bus

### UI (`src/UI/`)

Delivery mechanism.

- GraphQL resolvers (Query / Mutation)
- ResolverMap
- Field resolvers
- GraphQL schema (`.graphql` files)

## Dependency flow

```
UI → Application → Domain
     Infrastructure → Application → Domain
```

Domain has zero external dependencies. Application depends on Domain.
Infrastructure implements Domain interfaces. UI wires Application to
the outside world.

## Data flow

```
Client → GraphQL → Resolver → Command/Query → Handler → UseCase → Repository → Database
```

## Key decisions

- Why hexagonal architecture: [reason]
- Why CQRS: [reason]
- Why GraphQL: [reason]
- Why XML mappings over annotations/attributes: [reason]
