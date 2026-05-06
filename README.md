# game-service

Esqueleto educativo que replica la arquitectura de `brand-service`.

## Propósito

Este proyecto sirve como ejemplo de referencia para aprender a estructurar microservicios con:
- **DDD** (Domain-Driven Design): separación en capas Domain / Application / Infrastructure
- **CQRS**: separación entre comandos (escritura) y queries (lectura)
- **Value Objects**: encapsulación de primitivos con validación
- **Repository Pattern**: abstracción del acceso a datos en el dominio

## Arquitectura

```
src/
├── Domain/           ← Lógica de negocio pura (sin dependencias externas)
│   ├── Common/       ← Utilidades compartidas entre entidades
│   └── Game/
│       ├── Enum/         ← Enumeraciones del dominio
│       ├── Exception/    ← Excepciones de dominio
│       ├── Model/        ← Entidad Game
│       ├── Repository/   ← Interfaz del repositorio (contrato)
│       └── ValueObject/  ← Objetos de valor tipados
└── Application/      ← Casos de uso (orquestación)
    └── Game/
        └── Find/         ← Query para buscar un Game por ID
```

## Stack tecnológico

- **PHP** >= 8.3
- **Symfony** 7.3
- **Messenger** (CQRS bus)
- **catalog-shared-context** (base classes compartidas)

## Patrones clave

| Patrón | Dónde verlo |
|--------|-------------|
| Value Object | `Domain/Game/ValueObject/` |
| Domain Exception | `Domain/Game/Exception/` |
| Repository Interface | `Domain/Game/Repository/GameRepositoryInterface.php` |
| Query + Handler | `Application/Game/Find/` |
| EmptyValue (updates parciales) | `Domain/Common/EmptyValue.php` |
