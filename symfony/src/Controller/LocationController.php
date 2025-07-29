<?php

namespace App\Controller;

use App\Service\PortalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class LocationController extends AbstractController
{
    #[Route('/location', name: 'app_location')]
    public function index(PortalService $portalService): JsonResponse
    {
        return $this->json($portalService->getAllLocations());
    }
}
