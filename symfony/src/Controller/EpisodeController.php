<?php

namespace App\Controller;

use App\Service\EpisodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class EpisodeController extends AbstractController
{
    public function __construct(
        private readonly EpisodeService $episodeService
    ) {
    }
    #[Route('/episodes', name: 'get_episodes', methods: ['GET'])]
    public function getEpisodes(): JsonResponse
    {
        return $this->json($this->episodeService->getEpisodeList());
    }
}
