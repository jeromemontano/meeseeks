<?php

namespace App\Service;

use App\Enum\PortalEntity;

class LocationService
{
    public function __construct(
        private readonly PortalService $portalService
    ) {
    }

    public function getLocations(array $query): array
    {
        return $this->portalService->getAllRecords(PortalEntity::LOCATION->value, $query);
    }

}
