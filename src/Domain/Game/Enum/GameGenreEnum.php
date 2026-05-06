<?php

declare(strict_types=1);

namespace Saz\Game\Domain\Game\Enum;

/**
 * Enum de dominio para el género del juego.
 * Patrón equivalente a BrandTypeEnum en brand-service.
 *
 * Los enums de dominio son la forma idiomática en PHP 8.1+ de representar
 * un conjunto cerrado de valores válidos para un campo.
 */
enum GameGenreEnum: string
{
    case ACTION = 'action';
    case RPG = 'rpg';
    case STRATEGY = 'strategy';
    case SPORTS = 'sports';
    case ADVENTURE = 'adventure';
}
