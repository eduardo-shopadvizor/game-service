# API Documentation

`game-service` exposes a **GraphQL API** and a **Web Catalog** (HTML).

---

## GraphQL API

### Endpoint

```
POST /
```

### Schema

#### Types

```graphql
type Game {
    id: ID!
    name: String!
    description: String
    genre: String!
    createdAt: DateTime!
    updatedAt: DateTime!
}
```

#### Genres

The `genre` field is one of: `action`, `rpg`, `strategy`, `sports`, `adventure`.

---

### Queries

#### `game` — Find a game by ID

```graphql
query {
    game(id: "uuid-here") {
        id
        name
        description
        genre
        createdAt
        updatedAt
    }
}
```

**Returns:** `Game` or `null` if not found.

**Example response:**
```json
{
    "data": {
        "game": {
            "id": "550e8400-e29b-41d4-a716-446655440000",
            "name": "Elden Ring",
            "description": "FromSoftware's open-world masterpiece...",
            "genre": "rpg",
            "createdAt": "2024-03-15T10:30:00+00:00",
            "updatedAt": "2024-11-20T08:00:00+00:00"
        }
    }
}
```

---

### Mutations

#### `createGame` — Create a new game

```graphql
mutation CreateGame($input: CreateGameInput!) {
    createGame(input: $input) {
        id
        name
        genre
    }
}
```

**Variables:**
```json
{
    "input": {
        "name": "Elden Ring",
        "description": "FromSoftware's open-world masterpiece.",
        "genre": "rpg"
    }
}
```

**Returns:** the created `Game`.

---

#### `updateGame` — Update an existing game

```graphql
mutation UpdateGame($id: ID!, $input: UpdateGameInput!) {
    updateGame(id: $id, input: $input) {
        id
        name
        description
        genre
        updatedAt
    }
}
```

**Variables:**
```json
{
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "input": {
        "name": "Elden Ring",
        "description": "Updated description.",
        "genre": "rpg"
    }
}
```

**Returns:** the updated `Game`. Throws `GameNotFoundException` if the id does not exist.

---

## Web Catalog

Server-rendered HTML pages using Twig + Tailwind CSS.

### Routes

| Method | Path | Description |
|---|---|---|
| `GET` | `/catalog` | Game grid — all games |
| `GET` | `/catalog?genre=action` | Game grid filtered by genre |
| `GET` | `/catalog/{id}` | Game detail page |

### Genre filter values

| Value | Label |
|---|---|
| `action` | Action |
| `rpg` | RPG |
| `strategy` | Strategy |
| `sports` | Sports |
| `adventure` | Adventure |

### Example requests

```bash
# All games
curl http://localhost:8000/catalog

# Filtered by genre
curl "http://localhost:8000/catalog?genre=rpg"

# Game detail
curl http://localhost:8000/catalog/550e8400-e29b-41d4-a716-446655440000
```

---

## Error Responses

### GraphQL

Invalid queries return standard GraphQL errors:

```json
{
    "errors": [
        {
            "message": "...",
            "locations": [{"line": 1, "column": 3}]
        }
    ]
}
```

### Web Catalog

- Game not found (`GET /catalog/{id}`) → HTTP 404
- Invalid genre query param → silently ignored, shows all games
