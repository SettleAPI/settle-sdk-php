<?php

namespace Danielz\SettleApi;

use Exception;

class SettleApiClient
{
    protected string $merchantId;
    protected string $userId;
    protected string $publicKey;
    protected string $privateKey;
    protected bool $isSandbox;

    const BASE_URL_PRODUCTION = 'https://api.settle.eu/merchant/v1/';
    const BASE_URL_SANDBOX = 'https://api.sandbox.settle.eu/merchant/v1/';

    /**
     * SettleApi constructor.
     * @param string $merchantId
     * @param string $userId
     * @param string $publicKey
     * @param string $privateKey
     * @param bool $isSandbox
     */
    public function __construct(string $merchantId, string $userId, string $publicKey, string $privateKey, bool $isSandbox)
    {
        $this->merchantId = $merchantId;
        $this->userId = $userId;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->isSandbox = $isSandbox;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $postFields
     * @return array|bool
     * @throws SettleApiException
     */
    public function call(string $method, string $path, array $postFields = [])
    {
        $method = strtoupper($method);
        $path = ltrim($path, '/');
        $url = $this->getBaseUrl() . $path;

        $headers = $this->getHeaders($method, $url, $postFields);

        $curl_options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers
        ];

        if (!empty($postFields)) {
            $curl_options[CURLOPT_POST] = true;
            $curl_options[CURLOPT_POSTFIELDS] = json_encode($postFields);
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response_body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = @json_decode($response_body, true);
        curl_close($ch);

        switch (true) {
            case $httpCode == 204:
                return true;

            case $httpCode >= 200 && $httpCode < 300:
                return $response;

            default:
                if ($response !== null) {
                    $message = $response['error_description'] ?? $response['error_detail'] ?? $response['error_type'] ?? 'Something went wrong.';
                    throw new SettleApiException($message, $httpCode);
                }
                throw new SettleApiException('Something went wrong.', $httpCode);
        }
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->isSandbox ? self::BASE_URL_SANDBOX : self::BASE_URL_PRODUCTION;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $postFields
     * @return string[]
     */
    protected function getHeaders(string $method, string $url, array $postFields): array
    {
        $content = empty($postFields) ? '' : json_encode($postFields);
        $content_digest = base64_encode(hash('sha256', $content, true));

        $headers = [
            'Accept: application/vnd.mcash.api.merchant.v1+json',
            'Content-Type: application/json',
        ];

        $settle_headers = [
            'x-settle-merchant' => $this->merchantId,
            'x-settle-user' => $this->userId,
            'x-settle-timestamp' => date('Y-m-d H:i:s'),
            'x-settle-content-digest' => 'SHA256=' . $content_digest,
        ];

        $headers_fingerprint = [];
        foreach($settle_headers as $name => $value) {
            $headers[] = "{$name}: {$value}";
            $headers_fingerprint[] = strtoupper($name) . '=' . $value;
        }
        sort($headers_fingerprint);

        $request_fingerprint = join('|', [$method, $url, join('&', $headers_fingerprint)]);
        openssl_sign($request_fingerprint, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);
        $headers[] = 'Authorization: RSA-SHA256 ' . base64_encode($signature);

        return $headers;
    }
}
