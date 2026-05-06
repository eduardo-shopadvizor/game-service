---
name: wire-usecase
description: >
  Guía para completar la capa de Infrastructure y UI/GraphQL de un caso de uso
  ya implementado en la capa Application. Cubre: repositorio Doctrine, tipos
  custom, mapeos XML, resolvers Query/Mutation, ResolverMap y esquema GraphQL.
---

# Infrastructure + UI — Completar un caso de uso

Usa esta guía cada vez que un caso de uso en `Application/Game/<Feature>/` esté
listo y necesites conectarlo hacia abajo (persistencia) y hacia arriba (GraphQL).

---

## 0. Punto de partida — qué revisar antes de empezar

1. **Identifica el tipo de operación** del caso de uso:
   - Escribe datos → es un **Command** → necesitará **Mutation** en GraphQL.
   - Lee datos → es una **Query** → necesitará **Query** en GraphQL.

2. **Revisa `GameRepositoryInterface`** en
   `src/Domain/Game/Repository/GameRepositoryInterface.php`.
   ¿Necesita el caso de uso algún método que aún no existe?
   Si es así, añádelo a la interfaz **antes** de tocar la infraestructura.

3. **Revisa `Game::toPrimitives()`** en `src/Domain/Game/Model/Game.php`.
   El GraphQL resolver devuelve el objeto entidad directamente; el FieldResolver
   extrae los campos llamando a sus getters. Asegúrate de que los campos que
   quieres exponer tienen getter.

---

## 1. Infrastructure — Repositorio Doctrine

### 1.1 Implementación del repositorio

Archivo: `src/Infrastructure/Repository/DoctrineGameRepository.php`

```php
<?php

declare(strict_types=1);

namespace Saz\Game\Infrastructure\Repository;

use Saz\CatalogSharedContext\Infrastructure\Repository\DoctrineRepository;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Domain\Game\ValueObject\GameName;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineGameRepository extends DoctrineRepository implements GameRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function find(GameId $id): ?Game
    {
        return $this->getEntityManager()
            ->find(Game::class, $id);
    }

    public function findOneByName(GameName $name): ?Game
    {
        return $this->getEntityManager()
            ->getRepository(Game::class)
            ->findOneBy(['name' => $name]);
    }

    public function matching(Criteria $criteria): array
    {
        return $this->executeQuery($criteria);
    }

    public function count(Criteria $criteria): int
    {
        return $this->executeCount($criteria);
    }

    public function save(Game $game): void
    {
        $this->persist($game);
    }

    public function delete(Game $game): void
    {
        $this->remove($game);
    }
}
```

**Reglas:**
- Extiende `DoctrineRepository` del contexto compartido.
- El constructor siempre pasa `Game::class` al padre.
- Usa `$this->persist()` / `$this->remove()` del padre (no `flush()` directo).
- Si el caso de uso requiere filtros adicionales (por nombre, por género, etc.)
  sobrescribe `createQueryBuilderForCriteria(Criteria $criteria): QueryBuilder`
  añadiendo los `andWhere` correspondientes.

---

### 1.2 Doctrine Types para Value Objects

Directorio: `src/Infrastructure/Doctrine/Types/`

Crea un Type por cada Value Object que deba mapearse a la BD.

**Patrón para IDs** (extiende `IdType` de la base):

```php
<?php

declare(strict_types=1);

namespace Saz\Game\Infrastructure\Doctrine\Types;

use Saz\CatalogSharedContext\Infrastructure\Doctrine\Types\Base\IdType;
use Saz\Game\Domain\Game\ValueObject\GameId;

final class GameIdType extends IdType
{
    public function getName(): string
    {
        return 'game_id';
    }

    protected function getEntityClass(): string
    {
        return GameId::class;
    }
}
```

**Patrón para StringValueObject** (nombre, descripción, etc.):

```php
<?php

declare(strict_types=1);

namespace Saz\Game\Infrastructure\Doctrine\Types;

use Saz\CatalogSharedContext\Infrastructure\Doctrine\Types\StringValueObjectType;
use Saz\Game\Domain\Game\ValueObject\GameName;

final class GameNameType extends StringValueObjectType
{
    public function getName(): string
    {
        return 'game_name';
    }

    protected function getEntityClass(): string
    {
        return GameName::class;
    }
}
```

**Patrón para Enums** (BackedEnum):

```php
<?php

declare(strict_types=1);

namespace Saz\Game\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Saz\Game\Domain\Game\Enum\GameGenreEnum;

final class GameGenreType extends StringType
{
    public function getName(): string
    {
        return 'game_genre';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?GameGenreEnum
    {
        return $value !== null ? GameGenreEnum::from($value) : null;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return $value instanceof GameGenreEnum ? $value->value : $value;
    }
}
```

**Registro de Types** en `config/packages/doctrine.yaml` (o el fichero que
corresponda). Cada type custom necesita estar declarado:

```yaml
doctrine:
    dbal:
        types:
            game_id:          Saz\Game\Infrastructure\Doctrine\Types\GameIdType
            game_name:        Saz\Game\Infrastructure\Doctrine\Types\GameNameType
            game_description: Saz\Game\Infrastructure\Doctrine\Types\GameDescriptionType
            game_genre:       Saz\Game\Infrastructure\Doctrine\Types\GameGenreType
```

---

### 1.3 Mapeo XML de Doctrine

Archivo: `src/Infrastructure/Resources/config/doctrine/Game/Game.orm.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-mapping
    xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                        https://raw.github.com/doctrine/doctrine2/master/doctrine-mapping.xsd">

    <entity name="Saz\Game\Domain\Game\Model\Game" table="games">

        <id name="id" type="game_id"/>

        <field name="name"        type="game_name"/>
        <field name="description" type="game_description" nullable="true"/>
        <field name="genre"       type="game_genre"/>
        <field name="createdAt"   type="datetime_immutable"/>
        <field name="updatedAt"   type="datetime_immutable"/>

        <indexes>
            <index columns="genre"/>
            <index columns="created_at"/>
        </indexes>

    </entity>

</doctrine-mapping>
```

**Reglas:**
- El `name` en `<id>` y `<field>` corresponde al nombre de la propiedad PHP.
- El `type` corresponde al `getName()` del Doctrine Type que creaste en 1.2.
- Para relaciones many-to-many usa `<many-to-many>` con `<cascade-persist>` y
  `<cascade-remove>`.
- Añade `<index>` para todos los campos por los que se filtrará frecuentemente.

---

## 2. UI — GraphQL

### 2.1 Definición de tipos GraphQL

#### Tipo de salida (`src/UI/Resources/GraphQL/types/game.graphql`)

```graphql
type Game {
    id: ID!
    name: String!
    description: String
    genre: String!
    createdAt: DateTime!
    updatedAt: DateTime!
}

type Games {
    pagination: PaginationData!
    results: [Game!]!
}
```

- Crea un tipo `Games` (con paginación) si el caso de uso lista varios elementos.
- Mapea enums a `String` (el FieldResolver los convierte automáticamente via
  `BackedEnum::value`).
- Los campos nullable en el dominio son nullable en GraphQL (sin `!`).

#### Tipos de entrada (`src/UI/Resources/GraphQL/inputs/game.graphql`)

```graphql
input CreateGameInput {
    name: String!
    description: String
    genre: String!
}

input UpdateGameInput {
    name: String
    description: String
    genre: String
}
```

- En `Update*Input` todos los campos son opcionales (sin `!`) — se corresponde
  con el patrón `EmptyValue` del dominio.

#### Raíz de queries (`src/UI/Resources/GraphQL/queries.graphql`)

Añade el campo a `type Query`:

```graphql
type Query {
    game(id: ID!): Game
    games(pagination: PaginationInput, filter: GameFilterInput): Games!
}
```

#### Raíz de mutations (`src/UI/Resources/GraphQL/mutations.graphql`)

Añade la mutation a `type Mutation`:

```graphql
type Mutation {
    createGame(input: CreateGameInput!): Game!
    updateGame(id: ID!, input: UpdateGameInput!): Game!
    deleteGame(id: ID!): Boolean!
}
```

---

### 2.2 Resolver de Mutation

Directorio: `src/UI/GraphQL/Resolver/Mutation/`

**Patrón para Create:**

```php
<?php

declare(strict_types=1);

namespace Saz\Game\UI\GraphQL\Resolver\Mutation;

use Overblog\GraphQLBundle\Definition\Argument as ArgumentInterface;
use Ramsey\Uuid\Uuid;
use Saz\CatalogSharedContext\UI\GraphQL\Resolver\BaseMutation;
use Saz\Game\Application\Game\Create\CreateGameCommand;
use Saz\Game\Application\Game\Find\GetGameByIdQuery;

final class CreateGameMutation extends BaseMutation
{
    public function __invoke(ArgumentInterface $argument): mixed
    {
        $this->denyAccessUnlessGrantedForAdmin();

        $id = Uuid::uuid4()->toString();

        $this->command(new CreateGameCommand(...[
            'id'    => $id,
            ...$argument->offsetGet('input'),
        ]));

        return $this->query(new GetGameByIdQuery($id));
    }
}
```

**Patrón para Update:**

```php
final class UpdateGameMutation extends BaseMutation
{
    public function __invoke(ArgumentInterface $argument): mixed
    {
        $id = $argument->offsetGet('id');

        $this->command(new UpdateGameCommand(...[
            'id'  => $id,
            ...$argument->offsetGet('input'),
        ]));

        return $this->query(new GetGameByIdQuery($id));
    }
}
```

**Patrón para Delete:**

```php
final class DeleteGameMutation extends BaseMutation
{
    public function __invoke(ArgumentInterface $argument): bool
    {
        $this->denyAccessUnlessGrantedForAdmin();
        $this->command(new DeleteGameCommand($argument->offsetGet('id')));
        return true;
    }
}
```

**Reglas:**
- Extiende `BaseMutation` del contexto compartido.
- `$this->command()` despacha un Command (escribe).
- `$this->query()` despacha una Query (lee) — úsalo para recuperar el objeto
  recién creado/actualizado y devolverlo al cliente.
- Genera el UUID en el resolver, no en el handler.
- Llama a `denyAccessUnlessGrantedForAdmin()` cuando la operación requiera rol
  administrador.

---

### 2.3 Resolver de Query

Directorio: `src/UI/GraphQL/Resolver/Query/`

**Patrón para buscar por ID:**

```php
<?php

declare(strict_types=1);

namespace Saz\Game\UI\GraphQL\Resolver\Query;

use Overblog\GraphQLBundle\Definition\Argument as ArgumentInterface;
use Saz\CatalogSharedContext\UI\GraphQL\Resolver\BaseQuery;
use Saz\Game\Application\Game\Find\GetGameByIdQuery;

final class GameQuery extends BaseQuery
{
    public function __invoke(ArgumentInterface $argument): mixed
    {
        return $this->query(new GetGameByIdQuery($argument->offsetGet('id')));
    }
}
```

**Patrón para listar con paginación/filtros:**

```php
final class GamesQuery extends BaseQuery
{
    public function __invoke(ArgumentInterface $argument): mixed
    {
        return $this->query(new ListGamesQuery(
            pagination: $argument->offsetGet('pagination'),
            filter:     $argument->offsetGet('filter'),
        ));
    }
}
```

**Reglas:**
- Extiende `BaseQuery` del contexto compartido.
- Usa `$this->query()` para despachar la Query al bus de Symfony Messenger.
- El return type es `mixed` — Overblog lo serializa según el esquema GraphQL.

---

### 2.4 FieldResolver

Archivo: `src/UI/GraphQL/FieldResolver/FieldResolver.php`

```php
<?php

declare(strict_types=1);

namespace Saz\Game\UI\GraphQL\FieldResolver;

use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Resolver\FieldResolver as BaseFieldResolver;
use Closure;
use BackedEnum;

final class FieldResolver
{
    public function __invoke(
        mixed $parentValue,
        mixed $args,
        mixed $context,
        ResolveInfo $info,
    ): mixed {
        $fieldName = $info->fieldName;
        $value = BaseFieldResolver::valueFromObjectOrArray($parentValue, $fieldName);
        $value = $this->resolveValue($value);

        return $value instanceof Closure
            ? $value($parentValue, $args, $context, $info)
            : $value;
    }

    private function resolveValue(mixed $value): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }
        return $value;
    }
}
```

- Este fichero solo se crea **una vez** por servicio. Comprueba si ya existe
  antes de crearlo.
- Convierte automáticamente `BackedEnum` a su valor string/int para GraphQL.

---

### 2.5 ResolverMap

Archivo: `src/UI/GraphQL/ResolverMap/GameResolverMap.php`

```php
<?php

declare(strict_types=1);

namespace Saz\Game\UI\GraphQL\ResolverMap;

use Saz\CatalogSharedContext\UI\GraphQL\ResolverMap\BaseResolverMap;
use Saz\CatalogSharedContext\UI\GraphQL\TypeResolver\DateTimeTypeResolver;
use Saz\Game\UI\GraphQL\Resolver\Mutation\CreateGameMutation;
use Saz\Game\UI\GraphQL\Resolver\Mutation\DeleteGameMutation;
use Saz\Game\UI\GraphQL\Resolver\Mutation\UpdateGameMutation;
use Saz\Game\UI\GraphQL\Resolver\Query\GameQuery;
use Saz\Game\UI\GraphQL\Resolver\Query\GamesQuery;

final class GameResolverMap extends BaseResolverMap
{
    public function __construct(
        private readonly GameQuery $game,
        private readonly GamesQuery $games,
        private readonly CreateGameMutation $createGame,
        private readonly UpdateGameMutation $updateGame,
        private readonly DeleteGameMutation $deleteGame,
    ) {}

    protected function map(): array
    {
        $this->addType('DateTime', DateTimeTypeResolver::class);

        $this->addQuery('game', $this->game);
        $this->addQuery('games', $this->games);

        $this->addMutation('createGame', $this->createGame);
        $this->addMutation('updateGame', $this->updateGame);
        $this->addMutation('deleteGame', $this->deleteGame);

        return $this->getMap();
    }
}
```

**Reglas:**
- Extiende `BaseResolverMap` del contexto compartido.
- El nombre del campo en `addQuery()` / `addMutation()` debe coincidir
  **exactamente** con el campo definido en `queries.graphql` / `mutations.graphql`.
- Registra siempre el type resolver de `DateTime`.
- La inyección de dependencias es automática vía Symfony DI; el tag
  `overblog_graphql.resolver_map` ya está configurado en `config/services/ui.yaml`.

---

## 3. Checklist de verificación

Marca cada punto antes de considerar el caso de uso completamente integrado:

### Infrastructure
- [ ] `GameRepositoryInterface` tiene todos los métodos que necesita el caso de uso
- [ ] `DoctrineGameRepository` implementa todos los métodos de la interfaz
- [ ] Existe un Doctrine Type por cada Value Object (`GameIdType`, `GameNameType`, etc.)
- [ ] El Doctrine Type del Enum convierte correctamente `BackedEnum ↔ string`
- [ ] El mapeo XML existe y refleja correctamente la entidad `Game`
- [ ] Los types custom están registrados en la configuración de Doctrine
- [ ] Si hay relaciones (`many-to-many`), el XML incluye el `<join-table>` con las cascadas

### UI / GraphQL
- [ ] El tipo de salida `Game` está definido en `types/game.graphql`
- [ ] Los tipos de entrada (`CreateGameInput`, `UpdateGameInput`) están en `inputs/game.graphql`
- [ ] El campo está declarado en `queries.graphql` o `mutations.graphql`
- [ ] Existe el Resolver (Query o Mutation) correspondiente en su directorio
- [ ] El Resolver está registrado en `GameResolverMap` con el nombre exacto del campo
- [ ] `FieldResolver.php` existe en `UI/GraphQL/FieldResolver/`
- [ ] El FieldResolver está configurado como `default_field_resolver` en `config/services/ui.yaml`

### Tests
- [ ] Existe test unitario del CommandHandler/QueryHandler (ver skill `unit-tests-guidelines`)
- [ ] Existe test de integración del repositorio Doctrine si se añadieron métodos nuevos
- [ ] `composer lint` pasa sin errores (PHPStan nivel 8 + cs-fixer)

---

## 4. Referencia rápida de nombres

| Capa             | Archivo                                                  | Namespace                                      |
|------------------|----------------------------------------------------------|------------------------------------------------|
| Domain repo      | `src/Domain/Game/Repository/GameRepositoryInterface.php` | `Saz\Game\Domain\Game\Repository`              |
| Infra repo       | `src/Infrastructure/Repository/DoctrineGameRepository.php` | `Saz\Game\Infrastructure\Repository`         |
| Doctrine type    | `src/Infrastructure/Doctrine/Types/Game*Type.php`        | `Saz\Game\Infrastructure\Doctrine\Types`       |
| Mapeo XML        | `src/Infrastructure/Resources/config/doctrine/Game/*.xml`| —                                              |
| FieldResolver    | `src/UI/GraphQL/FieldResolver/FieldResolver.php`         | `Saz\Game\UI\GraphQL\FieldResolver`            |
| ResolverMap      | `src/UI/GraphQL/ResolverMap/GameResolverMap.php`         | `Saz\Game\UI\GraphQL\ResolverMap`              |
| Query Resolver   | `src/UI/GraphQL/Resolver/Query/<Name>Query.php`          | `Saz\Game\UI\GraphQL\Resolver\Query`           |
| Mutation Resolver| `src/UI/GraphQL/Resolver/Mutation/<Name>Mutation.php`    | `Saz\Game\UI\GraphQL\Resolver\Mutation`        |
| GraphQL types    | `src/UI/Resources/GraphQL/types/game.graphql`            | —                                              |
| GraphQL inputs   | `src/UI/Resources/GraphQL/inputs/game.graphql`           | —                                              |
| Queries root     | `src/UI/Resources/GraphQL/queries.graphql`               | —                                              |
| Mutations root   | `src/UI/Resources/GraphQL/mutations.graphql`             | —                                              |
