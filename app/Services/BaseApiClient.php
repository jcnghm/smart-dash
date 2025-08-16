<?php

namespace App\Services;

use Throwable;
use Illuminate\Support\Facades\Log;
use Closure as RetryMiddlewareClosure;
use GuzzleHttp\Psr7\Request as ApiRequest;
use GuzzleHttp\Psr7\Response as ApiResponse;
use GuzzleHttp\{Client, Middleware, HandlerStack};
use GuzzleRetry\GuzzleRetryMiddleware as RetryMiddleware;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use GuzzleHttp\Exception\{ConnectException, RequestException, ClientException, ServerException};

class BaseApiClient
{
    protected $client;

    protected $base_uri;

    protected $add_retry_middleware = true;

    const MAX_RETRIES = 2;

    const DEFAULT_TIMEOUT = 10;

    const DEFAULT_RETRY_STATUS = false;

    const RETRY_ON_STATUS = [500, 501, 502];

    public function setBaseUri(string $base_uri)
    {
        $this->base_uri = $base_uri;
    }

    protected function createClient()
    {
        $options = [
            'base_uri' => $this->base_uri,
            'timeout'  => self::DEFAULT_TIMEOUT,
            'connect_timeout' => self::DEFAULT_TIMEOUT,
            'retry_on_timeout' => self::DEFAULT_RETRY_STATUS,
            'handler' => $this->buildHandler()
        ];

        $this->client = new Client($options);
    }

    protected function buildHandler(): HandlerStack
    {
        $handler_stack = HandlerStack::create();

        $handler_stack->push(Middleware::mapRequest(function (ApiRequest $request) {
            return $this->addRequestHeaders($request);
        }));

        if ($this->add_retry_middleware) {
            $handler_stack->push($this->getRetryMiddleware());
        }

        return $handler_stack;
    }

    protected function getRetryMiddleware(): RetryMiddlewareClosure
    {
        return RetryMiddleware::factory(
            [
                'max_retry_attempts' => self::MAX_RETRIES,
                'retry_on_status' => self::RETRY_ON_STATUS
            ]
        );
    }

    /** @throws Throwable */
    public function get(string $endpoint, array $options = []): array
    {
        try {
            $response = $this->client->get($endpoint, $options);
            return $this->handleHttpResponse($response);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    /** @throws Throwable */
    public function post(string $endpoint, array $data = [], array $options = []): array
    {
        try {
            if (!empty($data) && !isset($options['json'])) {
                $options['json'] = $data;
            }
            
            $response = $this->client->post($endpoint, $options);
            return $this->handleHttpResponse($response);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    protected function addRequestHeaders(ApiRequest $request): ApiRequest
    {
        return $request;
    }

    protected function handleHttpResponse(ApiResponse $response): array
    {
        if (in_array($response->getStatusCode(), [HttpResponse::HTTP_OK, HttpResponse::HTTP_CREATED])) {
            $body = $this->getResponseBody($response);
            return $this->handleSuccess(!empty($body) ? $body : []);
        } else {
            return [
                'success' => false,
                'message' => 'HTTP error: ' . $response->getStatusCode(),
                'status' => $response->getStatusCode(),
                'data' => $this->getResponseBody($response) ?? []
            ];
        }
    }

    protected function getResponseBody(ApiResponse $response): array
    {
        $body = $response->getBody()->getContents();
        $decoded = json_decode($body, true);
        return $decoded ?? [];
    }

    protected function handleSuccess(array $response): array
    {
        return [
            'success' => true,
            'data'   => $response
        ];
    }

    protected function handleException($e): array
    {
        Log::error('API Exception occurred', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        switch (get_class($e)) {
            case ConnectException::class:
                return [
                    'success' => false,
                    'message' => 'Cannot connect to API server. Please ensure the API server is running.',
                    'error' => $e->getMessage()
                ];
                
            case RequestException::class:
                return [
                    'success' => false,
                    'message' => 'API request failed',
                    'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : null,
                    'error' => $e->getMessage()
                ];
                
            case ClientException::class:
                return [
                    'success' => false,
                    'message' => 'Client error occurred',
                    'status' => $e->getResponse()->getStatusCode(),
                    'error' => $e->getMessage()
                ];
                
            case ServerException::class:
                return [
                    'success' => false,
                    'message' => 'Server error occurred',
                    'status' => $e->getResponse()->getStatusCode(),
                    'error' => $e->getMessage()
                ];
                
            default:
                return [
                    'success' => false,
                    'message' => 'API error occurred',
                    'error' => $e->getMessage()
                ];
        }
    }
}