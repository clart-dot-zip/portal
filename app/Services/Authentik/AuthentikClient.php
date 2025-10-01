<?php

namespace App\Services\Authentik;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Services\Authentik\Exceptions\AuthentikException;

class AuthentikClient
{
    protected string $baseUrl;
    protected ?string $token;
    protected array $defaultHeaders;

    public function __construct(?string $token = null)
    {
        $this->baseUrl = rtrim(Config::get('services.authentik.base_url'), '/');
        $this->token = $token ?? Config::get('services.authentik.api_token');
        
        if (!$this->baseUrl) {
            throw new AuthentikException('Authentik base URL is not configured');
        }
        
        if (!$this->token) {
            throw new AuthentikException('Authentik API token is not configured');
        }

        $this->defaultHeaders = [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Make an authenticated request to the Authentik API
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): Response
    {
        $url = $this->baseUrl . '/api/v3' . $endpoint;
        
        $response = Http::withHeaders($this->defaultHeaders)
            ->timeout(30)
            ->{strtolower($method)}($url, $data);

        if ($response->failed()) {
            $this->handleApiError($response);
        }

        return $response;
    }

    /**
     * Handle API errors
     */
    protected function handleApiError(Response $response): void
    {
        $statusCode = $response->status();
        $body = $response->json();
        
        $message = $body['detail'] ?? $body['message'] ?? 'Unknown API error';
        
        throw new AuthentikException(
            "Authentik API Error [{$statusCode}]: {$message}",
            $statusCode
        );
    }

    /**
     * GET request
     */
    public function get(string $endpoint, array $params = []): array
    {
        $url = $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $this->makeRequest('GET', $url)->json();
    }

    /**
     * POST request
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->makeRequest('POST', $endpoint, $data)->json();
    }

    /**
     * PUT request
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->makeRequest('PUT', $endpoint, $data)->json();
    }

    /**
     * PATCH request
     */
    public function patch(string $endpoint, array $data = []): array
    {
        return $this->makeRequest('PATCH', $endpoint, $data)->json();
    }

    /**
     * DELETE request
     */
    public function delete(string $endpoint): array
    {
        $response = $this->makeRequest('DELETE', $endpoint);
        $json = $response->json();
        
        // Handle null responses for successful deletions
        return $json ?? [];
    }

    /**
     * Get paginated results
     */
    public function paginate(string $endpoint, array $params = []): array
    {
        $defaultParams = [
            'page' => 1,
            'page_size' => 100,
        ];
        
        $params = array_merge($defaultParams, $params);
        
        return $this->get($endpoint, $params);
    }

    /**
     * Get all results by automatically paginating
     */
    public function getAll(string $endpoint, array $params = []): array
    {
        $allResults = [];
        $page = 1;
        
        do {
            $params['page'] = $page;
            $params['page_size'] = 100;
            
            $response = $this->get($endpoint, $params);
            
            if (isset($response['results'])) {
                $allResults = array_merge($allResults, $response['results']);
                $hasNext = !is_null($response['next'] ?? null);
            } else {
                // If no pagination, return the response as is
                return $response;
            }
            
            $page++;
        } while ($hasNext);
        
        return $allResults;
    }
}