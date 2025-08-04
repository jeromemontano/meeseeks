<?php

namespace App\Service;

use App\Config\AutocompleteConfig;
use App\Enum\PortalEntity;

class LocationService
{
    public function __construct(
        private readonly PortalService $portalService
    ) {
    }

    public function getLocationSuggestions(string $location): array
    {
        $locations = [];
        $page = 1;

        do {
            $response = $this->portalService->getPaginatedRecords(
                PortalEntity::LOCATION->value,
                ['page' => $page, 'name' => $location]
            );

            if (isset($response['results'])) {
                foreach ($response['results'] as $location) {
                    $locations[] = $location['name'];

                    if (count($locations) >= AutocompleteConfig::SUGGESTION_LIMIT) {
                        break 2;
                    }
                }
            }

            $page++;
        } while (!empty($response['info']['next']));

        return $locations;
    }

    public function getDimensionSuggestions(string $dimension): array
    {
        $locations = [];
        $page = 1;

        do {
            $response = $this->portalService->getPaginatedRecords(
                PortalEntity::LOCATION->value,
                ['page' => $page, 'dimension' => $dimension]
            );

            if (isset($response['results'])) {
                foreach ($response['results'] as $location) {

                    if (!in_array($location['dimension'], $locations)) {
                        $locations[] = $location['dimension'];
                    }

                    if (count($locations) > AutocompleteConfig::SUGGESTION_LIMIT) {
                        break 2;
                    }
                }
            }
            $page++;
        } while (!empty($response['info']['next']));

        return array_slice($locations, 0, AutocompleteConfig::SUGGESTION_LIMIT);
    }

    public function getLocations(array $query = []): array
    {
        $locations = [];
        $page = 1;

        do {
            $response = $this->portalService->getPaginatedRecords(
                PortalEntity::LOCATION->value,
                array_merge($query, ['page' => $page])
            );

            $locations = array_merge($locations, $response['results'] ?? []);
            $page++;
        } while (!empty($response['info']['next']));

        return $locations;
    }

}
