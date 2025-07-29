<?php

namespace App\Request;

class CharacterFilterRequest
{
    public ?string $dimensionName = null;
    public ?string $locationName = null;
    public ?string $season = null;
    public ?string $episode = null;

    public static function fromRequest(array $query): self
    {
        $self = new self();
        $self->dimensionName = $query['dimensionName'] ?? null;
        $self->locationName = $query['locationName'] ?? null;
        $self->season = $query['season'] ?? null;
        $self->episode = $query['episode'] ?? null;
        return $self;
    }

    public function hasAtLeastOneFilter(): bool
    {
        return $this->dimensionName || $this->locationName || $this->season;
    }
}
