<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Arr;
use GuzzleHttp\Psr7\Request;
use App\Services\BaseApiClient;
use Illuminate\Support\Facades\{Cache, Log};

class RustServerApiClient extends BaseApiClient
{
    protected $token;

    const DEFAULT_EXPIRES_IN = 3600;

    const HEALTH_CHECK_PATH = 'health';

    const TOKEN_CACHE_KEY = 'rust_server_api_token';

    public function __construct()
    {
        $this->setBaseUri(config('rust-server.base_uri'));
        $this->createClient();
        $this->setAccessToken();
    }

    protected function addRequestHeaders(Request $request): Request
    {
        return $request->withHeader('Authorization', 'Bearer ' . $this->token);
    }

    /** @throws Exception */
    public function setAccessToken()
    {
        $token = Cache::get(self::TOKEN_CACHE_KEY);
        if (!$token) {
            Log::info('-- No token in cache, requesting new token from API.');

            $token_response = $this->post('token', [
                'username' => config('rust-server.demo_admin_user'),
                'password' => config('rust-server.demo_admin_password'),
            ]);
            
            if (!$token_response['success']) {
                throw new Exception('Failed to retrieve API token: ' . ($token_response['message'] ?? 'Unknown error'));
            }
            
            $token_response_data = Arr::get($token_response, 'data.data', []);
            $token = $token_response_data['access_token'] ?? null;
            
            if (!$token) {
                throw new Exception('Failed to retrieve API token: No access_token in response');
            }

            $expires_in = $token_response_data['expires_in'] ?? self::DEFAULT_EXPIRES_IN;
            Cache::put(self::TOKEN_CACHE_KEY, $token, $expires_in);
            Log::info('-- Token retrieved from API and cached.');
        } else {
            Log::info('-- Token retrieved from cache.');
        }

        $this->token = $token;
    }


    public function getApiHealthCheck(): array
    {
        $result = $this->get(self::HEALTH_CHECK_PATH);
        
        if (!empty($result['success'])) {
            $result['message'] = $result['data'];
        }
        
        return $result;
    }
}