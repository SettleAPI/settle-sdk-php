<?php

namespace Danielz\SettleApi;

class SettleApiClient
{
    protected string $merchantId;
    protected string $userId;
    protected string $publicKey;
    protected string $privateKey;
    protected bool $isSandbox;

    const BASE_URL_PRODUCTION = 'https://api.settle.eu/merchant/v1/';
    const BASE_URL_SANDBOX = 'https://api.sandbox.settle.eu/merchant/v1/';

    const PUBLIC_KEY_PRODUCTION = "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC9iglTBPG1poCw3qFPlxT0MSHO\nt6kgRmpVrLBY9Fx8Zn+zAoY89ZeFhwwnRR8IDQcj4yEAjsoXCxtH3bbh/OdvlFG6\nxdSsAeph6/MSk9YAVKWRWU5ber9cgoQ89KJ14goLUnhhegynUjnz+hdgAET5k9Uc\nsxnmfU7XeT78FP02JQIDAQAB\n-----END PUBLIC KEY-----";
    const PUBLIC_KEY_SANDBOX = "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDS92fCQmAPDpmcgraqPRXgz4Nd\nd/biPxIH5aG1dAQ8dMMcEjGCn7Sm5VcX1iV8L5oW+MlcnHFaZdVyy1Lcqed/8+r0\nQM9cFqQWif35C+eOr/s7/CCY/WXMApqO6YihtHvP+jgjrXltw0LHrUwMWO718udN\nhlg22QkpjhG90kvf3QIDAQAB\n-----END PUBLIC KEY-----";

    const SETTLE_LINK = 'https://settle.eu';
    const PAYMENT_LINK = 'http://settle.eu/p/:payment_request_id/';
    const PAYMENT_LINK_MOBILE = 'http://settle.eu/p/:payment_request_id/';
    const PAYMENT_LINK_MOBILE_SANDBOX = 'https://settledemo.page.link/?apn=eu.settle.app.sandbox&ibi=eu.settle.app.sandbox&isi=1453180781&ius=eu.settle.app.firebaselink&link=https://settle-demo://qr/http://settle.eu/p/:payment_request_id/';

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
     * @return bool
     */
    public function getIsSandbox()
    {
        return $this->isSandbox;
    }

    /**
     * @return string
     */
    public function getSettlePublicKey()
    {
        return $this->isSandbox ? self::PUBLIC_KEY_SANDBOX : self::PUBLIC_KEY_PRODUCTION;
    }

    /**
     * @param $isSandbox
     */
    public function setIsSandbox($isSandbox)
    {
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


    /**
     * @param string $callbackUrl
     * @param string $method
     * @return false|mixed
     * @throws SettleApiException
     */
    public function getCallbackData($callbackUrl = '', $method = 'POST')
    {
        $body = file_get_contents('php://input');
        if (empty($callbackUrl)) {
            $callbackUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        $is_valid_request = $this->isValidCallback($callbackUrl, $body, $_SERVER, $method);

        return $is_valid_request ? json_decode($body, true) : false;
    }

    /**
     * @param string $callbackUrl
     * @param string|array $body
     * @param array $headers
     * @param string $method
     * @return bool
     * @throws SettleApiException
     */
    public function isValidCallback($callbackUrl, $body, $headers, $method = 'POST')
    {
        $data = is_string($body) ? $body : json_encode($body);
        $content_digest = base64_encode(hash('sha256', $data, true));

        $expected_signature = false;
        $expected_content_digest = false;
        $settle_headers = [];
        foreach($headers as $header => $value) {
            $normalized_header = str_replace(['http_', '_'], ['', '-'], strtolower($header));
            switch($normalized_header) {
                case 'authorization':
                    list($algo, $authorization) = explode(' ', $value, 2);
                    if ($algo == 'RSA-SHA256') {
                        $expected_signature = base64_decode($authorization);
                    }
                    break;
                case 'x-settle-timestamp':
                    $settle_headers[] = 'X-SETTLE-TIMESTAMP=' . $value;
                    break;
                case 'x-settle-content-digest':
                    list($algo, $digest) = explode('=', $value, 2);
                    if ($algo == 'SHA256') {
                        $settle_headers[] = 'X-SETTLE-CONTENT-DIGEST=' . $value;
                        $expected_content_digest = $digest;
                    }
                    break;
            }
        }
        sort($settle_headers);

        if (!$expected_signature) {
            throw new SettleApiException("Authorization header is missing.");
        }

        $fingerprint = join('|', [strtoupper($method), $callbackUrl, join('&', $settle_headers)]);
        $valid_signature = openssl_verify($fingerprint, $expected_signature, $this->getSettlePublicKey(), OPENSSL_ALGO_SHA256);

        return ($expected_content_digest == $content_digest) && $valid_signature;
    }

    public function createLink($template, array $data = [])
    {
        switch($template) {
            default:
                $link = self::SETTLE_LINK;
                break;
            case 'payment_link':
                $link = self::PAYMENT_LINK;
                break;
            case 'payment_link_mobile':
                $link = $this->isSandbox ? self::PAYMENT_LINK_MOBILE_SANDBOX : self::PAYMENT_LINK_MOBILE;
                break;
        }

        foreach($data as $param => $value) {
            $link = str_replace(":{$param}", $value, $link);
        }

        return $link;
    }
}
