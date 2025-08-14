<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;

class ApiService
{
    protected $client;

    protected $token;

    const CACHE_KEY = 'api_token';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('app.api_base_url', 'http://host.docker.internal:8080/'),
        ]);

        $this->token = $this->getAccessToken();
    }

    public function getApiHealthCheck()
    {
        if (!$this->token) {
            throw new \Exception('API token not available');
        }

        $response = $this->client->get('health', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getAccessToken()
    {
        $token = Cache::get(self::CACHE_KEY);
        if (!$token) {
            $token_response_data = Arr::get($this->post('token', [
                'username' => config('app.api_username') ?? 'admin',
                'password' => config('app.api_password') ?? 'password123',
            ]), 'data', []);
            $token = $token_response_data['access_token'] ?? null;
            if (!$token) {
                throw new \Exception('Failed to retrieve API token');
            }

            $token_response_data['expires_in'] = 3600;
            Cache::put(self::CACHE_KEY, $token, $token_response_data['expires_in'] ?? 3600);
        }
        return $token;
    }

    public function get($endpoint, $params = [])
    {
        try {
            $response = $this->client->get($endpoint, ['query' => $params]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            // Handle error
            throw $e;
        }
    }

    public function post($endpoint, $data = [])
    {
        try {
            $response = $this->client->post($endpoint, ['json' => $data]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw $e;
        }
    }
}
