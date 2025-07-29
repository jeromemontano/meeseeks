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

    public function getAllRecords(string $entityType, array $query = []): array
    {
        $results = [];
        $page = 1;

        $url = "{$this->baseUrl}/{$entityType}";

        do {
            $queryWithPage = array_merge($query, ['page' => $page]);
            $response = $this->fetchRecordsInApi($url, $queryWithPage);
            $results = array_merge($results, $response['results'] ?? []);
            $page++;
        } while (!empty($response['info']['next']));

        return $results;
    }

    private function fetchRecordsInApi(string $url, array $query = []): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => $query,
            ]);
            return $response->toArray();
        } catch (ClientExceptionInterface) {
            // 4xx errors (including 404)
            return [];
        } catch (TransportExceptionInterface) {
            // Network or transport-level issues
            return [];
        }
    }
}
