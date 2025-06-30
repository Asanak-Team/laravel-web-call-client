<?php

namespace Asanak\WebCall;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\{GuzzleException, RequestException, ConnectException};

class WebCallClient
{
    private string $username;
    private string $password;
    private string $baseUrl;
    private Client $client;
    private bool $logger;

    const BAD_REQUEST_ERROR = "bad request error.";
    const INVALID_RESPONSE_ERROR = "invalid response error.";


    public function __construct(string $username, string $password, string $baseUrl, bool $logger = false)
    {
        $this->username = $username;
        $this->password = $password;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->client = new Client();
        $this->logger = $logger;
    }

    /**
     * @param string $endpoint
     * @param array $payload
     * @throws \RuntimeException
     * @return array
     */
    private function sendRequest(string $endpoint, array $payload = []): array
    {
        $url = "{$this->baseUrl}{$endpoint}";

        $payload = array_merge([
            'username' => $this->username,
            'password' => $this->password,
        ], $payload);

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $body = $response->getBody()->getContents();

            if ($this->logger) {
                Log::info("Web Call API response", [
                    'endpoint' => $endpoint,
                    'body' => $body
                ]);
            }

            return $this->processResponse(json_decode($body, true));
        } catch (RequestException $e) {
            $responseBody = $e->hasResponse()
                ? json_decode($e->getResponse()->getBody()->getContents(), true)
                : null;

            if ($this->logger) {
                Log::error("HTTP RequestException", [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage(),
                    'http-code' => $e->getCode(),
                    'response' => $responseBody
                ]);
            }

            if ($responseBody) {
                return $this->processResponse($responseBody);
            }
            throw new \RuntimeException("HTTP error: " . $e->getMessage(), $e->getCode(), $e);
        } catch (ConnectException $e) {
            if ($this->logger) {
                Log::critical("Connection error", [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage()
                ]);
            }

            throw new \RuntimeException("Connection failed: " . $e->getMessage(), $e->getCode(), $e);
        } catch (GuzzleException $e) {
            if ($this->logger) {
                Log::error("Unhandled GuzzleException", [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage()
                ]);
            }

            throw new \RuntimeException("Guzzle error: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $filePath
     * @throws \RuntimeException
     */
    public function uploadNewVoice(string $filePath)
    {
        $url = "{$this->baseUrl}/v1/upload/voice";

        try {
            $response = $this->client->post($url, [
                'multipart' => [
                    ['name' => 'username', 'contents' => $this->username],
                    ['name' => 'password', 'contents' => $this->password],
                    ['name' => 'file', 'contents' => fopen($filePath, 'r')]
                ]
            ]);

            $body = $response->getBody()->getContents();

            if ($this->logger) {
                Log::info("Upload voice response", ['body' => $body]);
            }

            return $this->_processResponse(json_decode($body, true));
        } catch (\Throwable $e) {
            Log::error("Upload voice failed", [
                'filePath' => $filePath,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw new \RuntimeException("Upload failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $file_id
     * @param mixed $destination
     * @return array
     */
    public function callByVoice(string $file_id, mixed $destination)
    {
        $payload = [
            'file_id' => $file_id,
            'destination' => $destination,
        ];

        return $this->sendRequest('/v1/send/file', $payload);
    }

    /**
     * @param mixed $code
     * @param mixed $destination
     * @return array
     */
    public function callByOtp(mixed $code, mixed $destination)
    {
        $payload = [
            'code' => $code,
            'destination' => $destination,
        ];

        return $this->sendRequest('/v1/send/otp', $payload);
    }


    /**
     * @param string|array $callIds
     * @return array
     */
    public function callStatus(string|array $callIds): array
    {
        $ids = is_array($callIds) ? implode(',', $callIds) : $callIds;

        return $this->sendRequest('/v1/report/callstatus', [
            'call_ids' => $ids,
        ]);
    }

    public function getCredit(): array
    {
        return $this->sendRequest('/v1/report/credit');
    }

    private function _processResponse(array $response)
    {
        if (!isset($response['success'])) {
            throw new \RuntimeException(self::INVALID_RESPONSE_ERROR);
        }

        if ((bool)$response['success']) {
            unset($response['success'], $response['error']);
            return $response;
        }

        $errorMessage = $response['error'] ?? 'Unknown error';
        throw new \RuntimeException($errorMessage);
    }
}
