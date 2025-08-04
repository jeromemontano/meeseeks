<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PortalService
{

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $baseUrl
    ) {
    }

    public function getRecordsByIds(string $entityType, string $ids): array
    {
        $url = "{$this->baseUrl}/{$entityType}/{$ids}";
        return $this->fetchRecordsInApi($url);
    }

    public function getPaginatedRecords(string $entityType, array $query = []): array
    {
        $url = "{$this->baseUrl}/{$entityType}";
        return $this->fetchRecordsInApi($url, $query);
    }

    public function fetchRecordsInApi(string $url, array $query = []): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => $query,
            ]);
            return $response->toArray();
        } catch (ClientExceptionInterface) {
            return [];
        } catch (TransportExceptionInterface) {
            return [];
        }
    }
}
