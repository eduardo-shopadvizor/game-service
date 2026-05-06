<?php

declare(strict_types=1);

namespace Saz\Game\UI\Web\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class GameImageExtension extends AbstractExtension
{
    private const GENRE_KEYWORDS = [
        'action' => 'videogames,gaming,controller',
        'rpg' => 'fantasy,dragon,medieval',
        'strategy' => 'chess,strategy,board',
        'sports' => 'sports,stadium,competition',
        'adventure' => 'adventure,exploration,landscape',
    ];

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('game_cover', [$this, 'getGameCover']),
        ];
    }

    public function getGameCover(string $name, string $genre): string
    {
        $lock = abs(crc32($name)) % 200 + 1;
        $keyword = self::GENRE_KEYWORDS[$genre] ?? 'videogames,gaming';

        return "https://loremflickr.com/640/360/{$keyword}?lock={$lock}";
    }
}
