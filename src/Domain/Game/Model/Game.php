<?php

declare(strict_types=1);

namespace Saz\Game\Domain\Game\Model;

use Saz\Game\Domain\Common\EmptyValue;
use Saz\Game\Domain\Game\Enum\GameGenreEnum;
use Saz\Game\Domain\Game\ValueObject\GameDescription;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Domain\Game\ValueObject\GameName;

/**
 * Entidad raíz del agregado Game.
 *
 * Patrones aplicados:
 * - Factory method createFromPrimitives(): punto de entrada único desde datos externos
 * - toPrimitives(): serialización a tipos PHP nativos (para eventos, APIs, etc.)
 * - update() con EmptyValue: permite actualizaciones parciales sin usar null ambiguo
 * - Getters explícitos: acceso controlado a cada propiedad
 */
class Game
{
    public function __construct(
        private readonly GameId $id,
        private GameName $name,
        private ?GameDescription $description,
        private GameGenreEnum $genre,
        private readonly \DateTimeInterface $createdAt,
        private \DateTimeInterface $updatedAt,
    ) {
    }

    /**
     * Construye una instancia de Game desde tipos primitivos (strings, arrays, etc.).
     * Usado al recibir datos de la API, eventos externos o fixtures de test.
     */
    public static function createFromPrimitives(
        string $id,
        string $name,
        ?string $description,
        string $genre,
        \DateTimeInterface $createdAt = new \DateTimeImmutable(),
        \DateTimeInterface $updatedAt = new \DateTimeImmutable(),
    ): self {
        return new self(
            id: new GameId($id),
            name: new GameName($name),
            description: null !== $description ? new GameDescription($description) : null,
            genre: GameGenreEnum::from($genre),
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    /**
     * Serializa la entidad a un array de tipos primitivos.
     * Usado para publicar eventos de dominio y respuestas de la API.
     *
     * @return array{
     *     id: string,
     *     name: string,
     *     description: ?string,
     *     genre: string,
     *     createdAt: \DateTimeInterface,
     *     updatedAt: \DateTimeInterface,
     * }
     */
    public function toPrimitives(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => (string) $this->name,
            'description' => $this->description?->value(),
            'genre' => $this->genre->value,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    /**
     * Actualiza la entidad de forma parcial.
     * EmptyValue como valor por defecto indica "no se envió este campo" (distinto de null).
     *
     * Ejemplo: update(name: new GameName('Half-Life')) solo cambia el nombre.
     */
    public function update(
        GameName|EmptyValue $name = new EmptyValue(),
        GameDescription|EmptyValue|null $description = new EmptyValue(),
        GameGenreEnum|EmptyValue $genre = new EmptyValue(),
    ): void {
        if (!$name instanceof EmptyValue) {
            $this->name = $name;
        }

        if (!$description instanceof EmptyValue) {
            $this->description = $description;
        }

        if (!$genre instanceof EmptyValue) {
            $this->genre = $genre;
        }

        $this->updatedAt = new \DateTimeImmutable();
    }

    public function id(): GameId
    {
        return $this->id;
    }

    public function name(): GameName
    {
        return $this->name;
    }

    public function description(): ?GameDescription
    {
        return $this->description;
    }

    public function genre(): GameGenreEnum
    {
        return $this->genre;
    }

    public function createdAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}
