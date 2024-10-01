<?php

namespace Xchimx\UnsplashApi;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class UnsplashService
{
    protected Client $client;

    protected array $lastResponseHeaders = [];

    protected array $temporaryOptions = [];

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('unsplash.base_uri'),
            'headers' => [
                'Authorization' => 'Client-ID '.config('unsplash.unsplash_access_key'),
                'Accept-Version' => 'v1',
            ],
        ]);
    }

    protected function request($method, $uri, $options = [])
    {
        $options = array_merge_recursive($options, $this->temporaryOptions);

        $response = $this->client->{$method}($uri, $options);

        $this->storeLastResponseHeaders($response);

        $this->temporaryOptions = [];

        return $response;
    }

    protected function storeLastResponseHeaders(ResponseInterface $response): void
    {
        $this->lastResponseHeaders = $response->getHeaders();
    }

    public function getLastResponseHeaders(): array
    {
        return $this->lastResponseHeaders;
    }

    public function searchPhotos($query, $perPage = 10, $page = 1)
    {
        $response = $this->request('get', 'search/photos', [
            'query' => [
                'query' => $query,
                'per_page' => $perPage,
                'page' => $page,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function searchPhotosAdvanced(array $params)
    {
        $response = $this->request('get', 'search/photos', [
            'query' => $params,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getPhoto($id)
    {
        $response = $this->request('get', "photos/{$id}");

        return json_decode($response->getBody(), true);
    }

    public function getRandomPhoto(array $params = [])
    {
        $response = $this->request('get', 'photos/random', [
            'query' => $params,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getPhotoDownloadLink($id)
    {
        $response = $this->request('get', "photos/{$id}/download");

        $data = json_decode($response->getBody(), true);

        return $data['url'] ?? null;
    }

    public function listCollections($perPage = 10, $page = 1)
    {
        $response = $this->request('get', 'collections', [
            'query' => [
                'per_page' => $perPage,
                'page' => $page,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getCollection($id)
    {
        $response = $this->request('get', "collections/{$id}");

        return json_decode($response->getBody(), true);
    }

    public function searchCollections($query, $perPage = 10, $page = 1)
    {
        $response = $this->request('get', 'search/collections', [
            'query' => [
                'query' => $query,
                'per_page' => $perPage,
                'page' => $page,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getUser($username)
    {
        $response = $this->request('get', "users/{$username}");

        return json_decode($response->getBody(), true);
    }

    public function getUserPhotos($username, $perPage = 10, $page = 1)
    {
        $response = $this->request('get', "users/{$username}/photos", [
            'query' => [
                'per_page' => $perPage,
                'page' => $page,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function withOptions(array $options)
    {
        $this->temporaryOptions = $options;

        return $this;
    }
}
