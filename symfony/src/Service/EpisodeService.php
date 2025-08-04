<?php

namespace App\Service;

use App\Enum\PortalEntity;
use App\Utils\IdExtractor;

class EpisodeService
{
    public function __construct(
        private readonly PortalService $portalService
    ) {
    }

    public function getEpisodes(array $query = []): array
    {
        $episodes = [];
        $page = 1;

        do {
            $response = $this->portalService->getPaginatedRecords(
                PortalEntity::EPISODE->value,
                array_merge($query, ['page' => $page])
            );

            $episodes = array_merge($episodes, $response['results'] ?? []);
            $page++;
        } while (!empty($response['info']['next']));

        return $episodes;
    }

    public function getEpisodeList(): array
    {
        $episodes = $this->getEpisodes();

        $seasonAndEpisodeList = [];
        $seasonList = [];
        $episodeList = [];

        foreach ($episodes as $episode) {
            if (preg_match('/(S\d{2})(E\d{2})/', $episode['episode'], $matches)) {
                $seasonCode = $matches[1];
                $episodeCode = $matches[2];

                $episodeList[$seasonCode][] = [
                    'name' => $episode['name'],
                    'episode' => $episodeCode,
                ];

                $seasonNumber = (int) ltrim(substr($seasonCode, 1), '0');
                $seasonList[$seasonCode] = "Season " . $seasonNumber;
            }
        }
        $seasonAndEpisodeList['seasons'] = $seasonList;
        $seasonAndEpisodeList['episodes'] = $episodeList;

        return $seasonAndEpisodeList;
    }


    public function updateCharacterEpisodes(array $characters): array
    {
        foreach ($characters as &$character) {
            if (isset($character['episode'])) {
                $character['episode'] = array_map(function ($url) {

                    return IdExtractor::extractIdFromUrl($url);
                }, $character['episode']);
            }
        }

        return $characters;
    }
}
