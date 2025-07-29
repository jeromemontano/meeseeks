<?php

namespace App\Controller;

use App\Enum\PortalEntity;
use App\Formatter\CharacterResponseFormatter;
use App\Request\CharacterFilterRequest;
use App\Request\CharacterSearchRequest;
use App\Service\CharacterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    #[Route('/characters', name: 'get_characters', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $filter = CharacterFilterRequest::fromRequest($request->query->all());

        if (!$filter->hasAtLeastOneFilter()) {
            return $this->json(['error' => 'At least one filter must be provided.'], 400);
        }

        $result = [];

        if ($filter->dimensionName) {
            $characters = $this->characterService->getCharactersByLocationCriteria(['dimension' => $filter->dimensionName]);
            $result[PortalEntity::DIMENSION->value] = CharacterResponseFormatter::format($characters, $filter->dimensionName);
        }

        if ($filter->locationName) {
            $characters = $this->characterService->getCharactersByLocationCriteria(['name' => $filter->locationName]);
            $result[PortalEntity::LOCATION->value] = CharacterResponseFormatter::format($characters, $filter->locationName);
        }

        if ($filter->season) {
            $episodeCode = $filter->season;
            if ($filter->episode) {
                $episodeCode = $filter->season.$filter->episode;
            }

            $characters = $this->characterService->getCharactersInEpisode(['episode' => $episodeCode]);
            $result[PortalEntity::EPISODE->value] = CharacterResponseFormatter::format($characters, $episodeCode);
        }

        return $this->json($result);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/characters/search', name: 'search_characters_by_name', methods: ['GET'])]
    public function search(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = new CharacterSearchRequest();
        $data->name = $request->query->get('name');

        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }

        $characters = $this->characterService->getCharactersByName($data->name);
        $result[PortalEntity::CHARACTER->value] = CharacterResponseFormatter::format($characters, $data->name);
        return $this->json($result);
    }
}
