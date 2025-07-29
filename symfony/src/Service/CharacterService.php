<?php

namespace App\Service;

use App\Enum\PortalEntity;
use Symfony\Contracts\Cache\CacheInterface;

class CharacterService
{
    private const ENTITY_CHARACTER_KEYS = [
        PortalEntity::LOCATION->value => 'residents',
        PortalEntity::EPISODE->value => 'characters',
    ];

    public function __construct(
        private readonly EpisodeService $episodeService,
        private readonly LocationService $locationService,
        private readonly PortalService $portalService,
        private readonly CacheInterface $cache,
    ) {
    }

    public function getAllCharacters(): array
    {
        //return $this->portalService->getAllRecords(PortalEntity::CHARACTER->value);

        return $this->cache->get('characters.all', function () {
            return $this->portalService->getAllRecords(PortalEntity::CHARACTER->value); // your actual API call
        });
    }

    public function getCharactersByLocationCriteria(array $criteria): array
    {
        $locations = $this->locationService->getLocations($criteria);
        return $this->getCharacterDetails($locations, PortalEntity::LOCATION->value);
    }

    public function getCharactersInEpisode(array $criteria): array
    {
        $episodes = $this->episodeService->getEpisodes($criteria);
        return $this->getCharacterDetails($episodes, PortalEntity::EPISODE->value);
    }

    public function getCharactersByName(string $characterName): array
    {
        return $this->portalService->getAllRecords(PortalEntity::CHARACTER->value, ['name' => $characterName]);
    }

    private function getCharacterDetails(array $entities, string $entityType): array
    {
        $key = self::ENTITY_CHARACTER_KEYS[$entityType] ?? null;

        $characterIds = [];

        foreach ($entities as $entity) {
            foreach ($entity[$key] ?? [] as $url) {
                $id = $this->extractIdFromUrl($url);
                $characterIds[] = $id;
            }
        }

        $characterIds = array_unique($characterIds);

        if (empty($characterIds)) {
            return [];
        }

        $idsString = implode(',', $characterIds);
        return $this->portalService->getRecordsByIds(PortalEntity::CHARACTER->value, $idsString);
    }

    private function extractIdFromUrl(string $url): int
    {
        return (int) basename($url);
    }
}
