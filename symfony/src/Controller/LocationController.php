<?php

namespace App\Controller;

use App\Service\LocationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class LocationController extends AbstractController
{
    public function __construct(
        private readonly LocationService $locationService
    ) {
    }

    #[Route('/location/autocomplete', name: 'get_location_suggestions', methods: ['GET'])]
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('query');
        $result = [];
        if ($query) {
            $result = $this->locationService->getLocationSuggestions($query);
        }

        return $this->json($result);
    }

    #[Route('/dimension/autocomplete', name: 'get_dimension_suggestions', methods: ['GET'])]
    public function dimensionAutocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('query');
        $result = [];
        if ($query) {
            $result = $this->locationService->getDimensionSuggestions($query);
        }

        return $this->json($result);
    }
}
