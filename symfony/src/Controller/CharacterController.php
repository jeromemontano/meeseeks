<?php

namespace App\Controller;

use App\Enum\PortalEntity;
use App\Formatter\CharacterResponseFormatter;
use App\Request\CharacterFilterRequest;
use App\Service\CharacterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CharacterController extends AbstractController
{
    public function __construct(
        private readonly CharacterService $characterService
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/character', name: 'get_characters', methods: ['GET'])]
    public function getCharacters(Request $request): JsonResponse
    {
        $query = $request->query->all();
        if (!$query) {
            return $this->json([]);
        }

        $filter = CharacterFilterRequest::fromRequest($query);
        $page = (int) $request->query->get('page', 1);

        $criteria = [];
        $entity = null;
        $filterQuery = '';

        if ($filter->locationName) {
            $criteria = ['name' => $filter->locationName];
            $entity = PortalEntity::LOCATION->value;
            $filterQuery = $filter->locationName;
        } elseif ($filter->dimensionName) {
            $criteria = ['dimension' => $filter->dimensionName];
            $entity = PortalEntity::DIMENSION->value;
            $filterQuery = $filter->dimensionName;
        } elseif ($filter->characterName) {
            $criteria = ['name' => $filter->characterName];
            $entity = PortalEntity::CHARACTER->value;
            $filterQuery = $filter->characterName;
        } elseif ($filter->season) {
            $episodeCode = $filter->season;
            if ($filter->episode) {
                $episodeCode .= $filter->episode;
            }

            $criteria = ['episode' => $episodeCode];
            $entity = PortalEntity::EPISODE->value;
            $filterQuery = $episodeCode;
        }

        if (!$entity) {
            return $this->json([]);
        }

        $response = $this->characterService->getCharactersByCriteria(
            $criteria,
            $entity,
            $page
        );

        $result = CharacterResponseFormatter::format($response, $filterQuery);

        return $this->json($result);
    }

    #[Route('/character/autocomplete', name: 'get_character_suggestions', methods: ['GET'])]
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('query');
        $result = [];
        if ($query) {
            $result = $this->characterService->getCharacterSuggestions($query);
        }

        return $this->json($result);
    }
}
