<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class ApiService
{
    protected $client;

    protected $token;

    protected $connectionFailed;

    const CACHE_KEY = 'api_token';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('app.api_base_url', 'http://host.docker.internal:8080/'),
        ]);

        $this->token = $this->getAccessToken();
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

    public function getApiHealthCheck()
    {
        if (!$this->token) {
            throw new Exception('Token Unavailable.');
        }
    
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ];
    
        $result = $this->get('health', [], $options);
        
        if (!empty($result['success'])) {
            $result['message'] = $result['data'];
        }
        
        return $result;
    }

    public function get($endpoint, $params = [], $options = [])
    {
        if (!empty($params)) {
            $options['query'] = $params;
        }
        
        return $this->makeRequest('GET', $endpoint, $options);
    }
    
    public function post($endpoint, $data = [], $options = [])
    {
        if (!empty($data)) {
            $options['json'] = $data; // or 'form_params' if your API expects form data
        }
        
        return $this->makeRequest('POST', $endpoint, $options);
    }

    private function makeRequest($method, $endpoint, $options = [])
    {
        try {
            $defaultOptions = [
                'timeout' => 10,
                'connect_timeout' => 5
            ];
            
            $options = array_merge($defaultOptions, $options);
            
            $response = $this->client->request($method, $endpoint, $options);
            
            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true)
            ];
            
        } catch (ConnectException $e) {
            return [
                'success' => false,
                'message' => 'Cannot connect to API server. Please ensure the API server is running.',
                'error' => $e->getMessage()
            ];
            
        } catch (RequestException $e) {
            return [
                'success' => false,
                'message' => 'API request failed',
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : null,
                'error' => $e->getMessage()
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'API error occurred',
                'error' => $e->getMessage()
            ];
        }
    }
}
