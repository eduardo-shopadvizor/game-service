<?php

declare(strict_types=1);

namespace Saz\Game\UI\Web\Controller;

use Saz\CatalogSharedContext\Application\Service\BusInterface;
use Saz\Game\Application\Game\Find\GetGameByIdQuery;
use Saz\Game\Application\Game\List\ListGamesQuery;
use Saz\Game\Domain\Game\Enum\GameGenreEnum;
use Saz\Game\Domain\Game\Model\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/catalog')]
final class CatalogController extends AbstractController
{
    public function __construct(private readonly BusInterface $bus)
    {
    }

    #[Route('', name: 'catalog_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $genre = $request->query->get('genre');

        if (null !== $genre && !\in_array($genre, array_column(GameGenreEnum::cases(), 'value'), true)) {
            $genre = null;
        }

        /** @var Game[] $games */
        $games = $this->bus->query(new ListGamesQuery($genre));

        return $this->render('catalog/index.html.twig', [
            'games' => $games,
            'genres' => GameGenreEnum::cases(),
            'currentGenre' => $genre,
        ]);
    }

    #[Route('/{id}', name: 'catalog_show', methods: ['GET'])]
    public function show(string $id): Response
    {
        /** @var ?Game $game */
        $game = $this->bus->query(new GetGameByIdQuery($id));

        if (null === $game) {
            throw $this->createNotFoundException('Game not found');
        }

        return $this->render('catalog/show.html.twig', [
            'game' => $game,
        ]);
    }
}
