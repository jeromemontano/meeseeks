<?php

namespace App\Service;

use App\Enum\PortalEntity;

class EpisodeService
{
    public function __construct(
        private readonly PortalService $portalService
    ) {
    }

    public function getEpisodes(array $query): array
    {
        return $this->portalService->getAllRecords(PortalEntity::EPISODE->value, $query);
    }

}
