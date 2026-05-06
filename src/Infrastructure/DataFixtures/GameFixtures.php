<?php

declare(strict_types=1);

namespace Saz\Game\Infrastructure\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use Saz\Game\Domain\Game\Model\Game;

final class GameFixtures extends Fixture
{
    private const GAMES = [
        // ACTION
        [
            'name' => 'Counter-Strike 2',
            'genre' => 'action',
            'description' => 'The iconic tactical shooter returns. Join the battle in fast-paced 5v5 matches'
                . ' on legendary maps where strategy and precision decide the outcome.',
        ],
        [
            'name' => 'Doom Eternal',
            'genre' => 'action',
            'description' => 'Rip and tear through demonic hordes at breakneck speed.'
                . ' The ultimate test of skill and aggression in a relentless fight for survival.',
        ],
        [
            'name' => 'Devil May Cry 5',
            'genre' => 'action',
            'description' => 'Stylish action at its finest. Battle demons with Dante, Nero, and V'
                . ' in spectacular combo-driven combat that rewards mastery.',
        ],
        [
            'name' => 'Call of Duty: Modern Warfare III',
            'genre' => 'action',
            'description' => 'Experience the ultimate modern warfare. Fight through intense campaigns'
                . ' and multiplayer battles across iconic global locations.',
        ],
        [
            'name' => 'Hades',
            'genre' => 'action',
            'description' => 'Defy the god of the dead in this rogue-like dungeon crawler.'
                . ' Each escape attempt reveals more of the story and sharpens your skills.',
        ],

        // RPG
        [
            'name' => 'Elden Ring',
            'genre' => 'rpg',
            'description' => 'FromSoftware\'s open-world masterpiece. Explore the Lands Between'
                . ' and uncover the mystery of the Elden Ring in a vast, challenging world.',
        ],
        [
            'name' => 'The Witcher 3: Wild Hunt',
            'genre' => 'rpg',
            'description' => 'An open-world RPG epic. Play as Geralt of Rivia in a richly detailed'
                . ' fantasy world filled with meaningful choices and consequences.',
        ],
        [
            'name' => 'Baldur\'s Gate 3',
            'genre' => 'rpg',
            'description' => 'The definitive RPG experience. A massive adventure with true freedom'
                . ' of choice, deep tactical combat, and unforgettable characters.',
        ],
        [
            'name' => 'Dark Souls III',
            'genre' => 'rpg',
            'description' => 'The punishing masterpiece of challenging combat. Explore a dying world'
                . ' full of secrets, tragedy, and extraordinary boss encounters.',
        ],
        [
            'name' => 'Final Fantasy XVI',
            'genre' => 'rpg',
            'description' => 'Epic fantasy drama meets intense real-time combat.'
                . ' Follow Clive Rosfield in a world of warring nations and powerful Eikons.',
        ],

        // STRATEGY
        [
            'name' => 'Civilization VI',
            'genre' => 'strategy',
            'description' => 'Build an empire to stand the test of time. Lead your civilization'
                . ' from the Stone Age to the Space Age through diplomacy, culture, and war.',
        ],
        [
            'name' => 'StarCraft II',
            'genre' => 'strategy',
            'description' => 'The gold standard of real-time strategy. Command one of three unique'
                . ' factions — Terran, Zerg, or Protoss — in intense competitive battles.',
        ],
        [
            'name' => 'XCOM 2',
            'genre' => 'strategy',
            'description' => 'Earth has fallen to alien occupation. Lead the resistance'
                . ' in tense turn-based tactical combat where every decision carries weight.',
        ],
        [
            'name' => 'Age of Empires IV',
            'genre' => 'strategy',
            'description' => 'Command great civilizations through history. Build, advance, and conquer'
                . ' in this legendary RTS series with stunning historical authenticity.',
        ],
        [
            'name' => 'Into the Breach',
            'genre' => 'strategy',
            'description' => 'Perfect information, near-perfect strategy.'
                . ' Defend humanity against Vek creatures in elegant, chess-like tactical battles.',
        ],

        // SPORTS
        [
            'name' => 'EA Sports FC 24',
            'genre' => 'sports',
            'description' => 'Experience the beautiful game with the most authentic football simulation.'
                . ' Play with your favorite clubs and players from top leagues worldwide.',
        ],
        [
            'name' => 'NBA 2K24',
            'genre' => 'sports',
            'description' => 'The most authentic basketball simulation. Build your legend on the court'
                . ' with stunning gameplay, rich career modes, and online competition.',
        ],
        [
            'name' => 'Rocket League',
            'genre' => 'sports',
            'description' => 'High-octane soccer with rocket-powered cars.'
                . ' Master aerial maneuvers and team coordination in this unique competitive sport.',
        ],
        [
            'name' => 'F1 23',
            'genre' => 'sports',
            'description' => 'Live every moment of the Formula 1 season. Take control of an F1 car'
                . ' on the most iconic circuits in the world with realistic physics.',
        ],
        [
            'name' => 'Tony Hawk\'s Pro Skater 1+2',
            'genre' => 'sports',
            'description' => 'The legendary skateboarding game remastered to perfection.'
                . ' Shred classic parks, land combo tricks, and relive the iconic soundtrack.',
        ],

        // ADVENTURE
        [
            'name' => 'The Legend of Zelda: Breath of the Wild',
            'genre' => 'adventure',
            'description' => 'A revolutionary open-world adventure. Explore the vast kingdom of Hyrule'
                . ' with complete freedom and discover its secrets at your own pace.',
        ],
        [
            'name' => 'Red Dead Redemption 2',
            'genre' => 'adventure',
            'description' => 'An epic tale of life in America\'s unforgiving heartland.'
                . ' Experience the last days of the outlaw era in breathtaking detail.',
        ],
        [
            'name' => 'God of War Ragnarök',
            'genre' => 'adventure',
            'description' => 'Kratos and Atreus journey to the nine realms in search of answers.'
                . ' Face gods and monsters in this epic saga of fatherhood and destiny.',
        ],
        [
            'name' => 'Cyberpunk 2077',
            'genre' => 'adventure',
            'description' => 'An open-world action-adventure set in Night City,'
                . ' a megalopolis obsessed with power and body modification. Be whoever you want to be.',
        ],
        [
            'name' => 'Horizon Forbidden West',
            'genre' => 'adventure',
            'description' => 'Aloy ventures into dangerous new lands to uncover the secrets'
                . ' of a far-future Earth overrun by mysterious and deadly machines.',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        foreach (self::GAMES as $data) {
            $game = Game::createFromPrimitives(
                id: Uuid::uuid4()->toString(),
                name: $data['name'],
                description: $data['description'],
                genre: $data['genre'],
                createdAt: \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 years', '-1 month')),
                updatedAt: \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', 'now')),
            );

            $manager->persist($game);
        }

        $manager->flush();
    }
}
