<?php

namespace App\Service;

use App\Config\AutocompleteConfig;
use App\Enum\PortalEntity;
use App\Utils\IdExtractor;

class CharacterService
{
    private const ENTITY_CHARACTER_KEYS = [
        PortalEntity::LOCATION->value  => 'residents',
        PortalEntity::DIMENSION->value => 'residents',
        PortalEntity::EPISODE->value   => 'characters',
    ];

    private const RESULTS_PER_PAGE = 9;

    public function __construct(
        private readonly EpisodeService $episodeService,
        private readonly LocationService $locationService,
        private readonly PortalService $portalService
    ) {
    }

    public function getCharacterSuggestions(string $name): array
    {
        $seenNames = [];
        $suggestions = [];
        $page = 1;

        do {
            $response = $this->portalService->getPaginatedRecords(
                PortalEntity::CHARACTER->value,
                ['page' => $page, 'name' => $name]
            );

            foreach ($response['results'] ?? [] as $character) {
                $charName = $character['name'];

                if (isset($seenNames[$charName])) {
                    continue;
                }

                $seenNames[$charName] = true;
                $suggestions[] = $charName;

                if (count($suggestions) === AutocompleteConfig::SUGGESTION_LIMIT) {
                    break 2;
                }
            }
            $page++;
        } while (!empty($response['info']['next']));

        return $suggestions;
    }

    public function getCharactersByCriteria(array $criteria, string $entity, int $page = 1): array
    {
        $records = [];

        if ($entity == PortalEntity::EPISODE->value) {
            $records = $this->episodeService->getEpisodes($criteria);
        }

        if ($entity == PortalEntity::LOCATION->value || $entity == PortalEntity::DIMENSION->value) {
            $records = $this->locationService->getLocations($criteria);
        }

        if ($entity == PortalEntity::CHARACTER->value) {
            return $this->getCharactersByName($criteria, $page);
        }

        return $this->getCharacterDetails($records, $entity, $page);
    }

    private function getCharactersByName(array $query, int $page = 1): array
    {
        $characters = [];
        $apiPage = 1;
        do {
            $response = $this->portalService->getPaginatedRecords(
                PortalEntity::CHARACTER->value,
                array_merge($query, ['page' => $apiPage])
            );

            $characters = array_merge($characters, $response['results'] ?? []);
            $apiPage++;
        } while (!empty($response['info']['next']));

        $page = max(1, $page);
        $offset = ($page - 1) * self::RESULTS_PER_PAGE;
        $partialCharacters = array_slice($characters, $offset, self::RESULTS_PER_PAGE);

        return [
            'characters' => $this->episodeService->updateCharacterEpisodes($partialCharacters),
            'pagination' => $this->generateCharactersPagination($page, count($characters))
        ];
    }

    private function getCharacterDetails(array $entities, string $entityType, int $page): array
    {
        $key = self::ENTITY_CHARACTER_KEYS[$entityType] ?? null;

        $characterIds = [];
        foreach ($entities as $entity) {
            foreach ($entity[$key] ?? [] as $url) {
                $id = IdExtractor::extractIdFromUrl($url);
                $characterIds[] = $id;
            }
        }

        $characterIds = array_unique($characterIds);
        if (empty($characterIds)) {
            return [];
        }

        $page = max(1, $page);
        $offset = ($page - 1) * self::RESULTS_PER_PAGE;
        $batchCharacterIds = array_slice($characterIds, $offset, self::RESULTS_PER_PAGE);
        $idsString = implode(',', $batchCharacterIds);

        $partialCharacters = $this->portalService->getRecordsByIds(PortalEntity::CHARACTER->value, $idsString);

        if (!empty($partialCharacters) && array_keys($partialCharacters) !== range(0, count($partialCharacters) - 1)) {
            $partialCharacters = [$partialCharacters];
        }

        return [
            'characters' => $this->episodeService->updateCharacterEpisodes($partialCharacters),
            'pagination' => $this->generateCharactersPagination($page, count($characterIds))
        ];
    }

    private function generateCharactersPagination(int $page, int $characterCount): array
    {
        $perPage = self::RESULTS_PER_PAGE;
        $page = max(1, $page);
        $lastPage = ceil($characterCount / $perPage);

        $startingRecord = ($page - 1) * $perPage + 1;
        $endingRecord = min($page * $perPage, $characterCount);

        return [
            'previous' => $page > 1 ? $page - 1 : null,
            'next'     => $page < $lastPage ? $page + 1 : null,
            'info'     => sprintf(
                'Displaying %d to %d of %d characters',
                $startingRecord,
                $endingRecord,
                $characterCount
            ),
        ];
    }
}
