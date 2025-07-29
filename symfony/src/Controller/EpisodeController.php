<?php

namespace App\Controller;

use App\Service\PortalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class EpisodeController extends AbstractController
{
    #[Route('/episodes', name: 'get_episodes', methods: ['GET'])]
    public function index(PortalService $portalService): JsonResponse
    {
        return $this->json($portalService->getAllEpisodes());
    }
}
