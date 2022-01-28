<?php

namespace SettleApi;

use DanielZ\ShapeValidator\ShapeException;
use DanielZ\ShapeValidator\ShapeValidator;
use DateTime;
use DateTimeZone;

/**
 * Class SettleApiClient
 * @package SettleApi
 * @link https://api.support.settle.eu/api/reference/rest/v1/
 */
class SettleApiClient
{
    protected string $merchantId;
    protected string $userId;
    protected string $publicKey;
    protected string $privateKey;
    protected bool $isSandbox;
    protected bool $validateShapes = true;

    const BASE_URL_PRODUCTION = 'https://api.settle.eu/merchant/v1/';
    const BASE_URL_SANDBOX = 'https://api.sandbox.settle.eu/merchant/v1/';
    const BASE_URL_DYNAMICLINKS = 'https://dynamiclinks.settle.dev/api/create';

    const PUBLIC_KEY_PRODUCTION = "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC9iglTBPG1poCw3qFPlxT0MSHO\nt6kgRmpVrLBY9Fx8Zn+zAoY89ZeFhwwnRR8IDQcj4yEAjsoXCxtH3bbh/OdvlFG6\nxdSsAeph6/MSk9YAVKWRWU5ber9cgoQ89KJ14goLUnhhegynUjnz+hdgAET5k9Uc\nsxnmfU7XeT78FP02JQIDAQAB\n-----END PUBLIC KEY-----";
    const PUBLIC_KEY_SANDBOX = "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDS92fCQmAPDpmcgraqPRXgz4Nd\nd/biPxIH5aG1dAQ8dMMcEjGCn7Sm5VcX1iV8L5oW+MlcnHFaZdVyy1Lcqed/8+r0\nQM9cFqQWif35C+eOr/s7/CCY/WXMApqO6YihtHvP+jgjrXltw0LHrUwMWO718udN\nhlg22QkpjhG90kvf3QIDAQAB\n-----END PUBLIC KEY-----";

    const LINK_TEMPLATE_PAYMENT = 'payment_link';
    const LINK_TEMPLATE_SHORT_LINK = 'short_link_link';

    const SETTLE_LINK = 'https://settle.eu';
    const PAYMENT_LINK = 'http://settle.eu/p/:payment_request_id/';
    const SHORT_LINK_LINK = 'http://settle.eu/s/:short_link_id/';

    const DEEP_LINK_CONFIG_PRODUCTION = [
        'env' => 'settle',
        'baseUrl' => 'get.settle.eu',
        'apn' => 'eu.settle.app',
        'ibi' => 'eu.settle.app',
        'isi' => '1440051902',
        'ius' => 'eu.settle.app.firebaselink',
    ];

    const DEEP_LINK_CONFIG_SANDBOX = [
        'env' => 'settle-demo',
        'baseUrl' => 'settledemo.page.link',
        'apn' => 'eu.settle.app.sandbox',
        'ibi' => 'eu.settle.app.sandbox',
        'isi' => '1453180781',
        'ius' => 'eu.settle.app.firebaselink',
    ];

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
     * @return bool
     */
    public function getValidateShapes()
    {
        return $this->validateShapes;
    }

    /**
     * @param $validateShapes
     */
    public function setValidateShapes($validateShapes)
    {
        $this->validateShapes = $validateShapes;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     * @param array $shape
     * @return array|bool
     * @throws SettleApiException
     */
    public function call(string $method, string $path, array $data = [], $shape = [])
    {
        if ($this->validateShapes && !empty($shape)) {
            try {
                $validator = new ShapeValidator($shape);
                $validator->validate($data);
            } catch (ShapeException $e) {
                throw new SettleApiException($e->getMessage(), $e->getCode(), $e, $e->getValidationErrors());
            }
        }

        $curl_options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
        ];

        $method = strtoupper($method);
        $url = $this->getApiBaseUrl() . ltrim($path, '/');

        if (!empty($data)) {
            if ($method == 'GET') {
                $url  .= '?' . http_build_query($data);
            } else {
                $curl_options[CURLOPT_POST] = true;
                $curl_options[CURLOPT_POSTFIELDS] = json_encode($data);
            }
        }

        $curl_options[CURLOPT_URL] = $url;
        $curl_options[CURLOPT_HTTPHEADER] = $this->getHeaders($method, $url, $data);

        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);

        $response_body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = @json_decode($response_body, true);
        curl_close($ch);

        switch (true) {
            case $httpCode == 204:
                return true;

            case $httpCode >= 200 && $httpCode < 300:
                return $response;

            case $httpCode == 404:
                throw new SettleApiException("The resource could not be found.", $httpCode);

            default:
                if ($response !== null) {
                    $message = $response['error_description'] ?? $response['error_detail'] ?? $response['error_type'] ?? 'Something went wrong.';
                    throw new SettleApiException($message, $httpCode);
                }

                $errorMessage = !empty($response_body) ? $response_body : 'Something went wrong.';
                throw new SettleApiException($errorMessage, $httpCode);
        }
    }

    /**
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return $this->isSandbox ? self::BASE_URL_SANDBOX : self::BASE_URL_PRODUCTION;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $data
     * @return string[]
     */
    protected function getHeaders(string $method, string $url, array $data): array
    {
        $content = $method == 'GET' || empty($data) ? '' : json_encode($data);
        $content_digest = base64_encode(hash('sha256', $content, true));

        $headers = [
            'User-Agent: SettleApi PHP Client',
            'Accept: application/vnd.mcash.api.merchant.v1+json',
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content),
        ];

        $date = (new DateTime())->setTimezone(new DateTimeZone('UTC'));
        $settle_headers = [
            'x-settle-merchant' => $this->merchantId,
            'x-settle-user' => $this->userId,
            'x-settle-timestamp' => $date->format('Y-m-d H:i:s'),
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
     * Verify that the current request to PHP is sent from Settle
     * and if yes, get the data sent by Settle
     *
     * @param string $callbackUrl
     * @param string $method
     * @return false|mixed
     * @throws SettleApiException
     */
    public function getCallbackData($callbackUrl = '', $method = 'POST')
    {
        $body = file_get_contents('php://input');
        if (empty($callbackUrl)) {
            $scheme = strtolower($_SERVER['HTTPS'] ?? '') == 'on' ? 'https' : ($_SERVER['REQUEST_SCHEME'] ?? 'http');
            $callbackUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');
        }

        $is_valid_request = $this->isValidCallback($callbackUrl, $body, $_SERVER, $method);

        return $is_valid_request ? json_decode($body, true) : false;
    }

    /**
     * Verify that the callback is indeed sent from Settle
     * The verification depends on the 'Authorization' and custom Settle headers
     * See README for more details
     *
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
                    @list($algo, $authorization) = explode(' ', $value, 2);
                    if ($algo == 'RSA-SHA256') {
                        $expected_signature = base64_decode((string)$authorization);
                    }
                    break;
                case 'x-settle-timestamp':
                    $settle_headers[] = 'X-SETTLE-TIMESTAMP=' . $value;
                    break;
                case 'x-settle-content-digest':
                    @list($algo, $digest) = explode('=', $value, 2);
                    if ($algo == 'SHA256') {
                        $settle_headers[] = 'X-SETTLE-CONTENT-DIGEST=' . $value;
                        $expected_content_digest = (string)$digest;
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

    /**
     * @param string $template
     * @param array $data
     * @param array $extraData
     * @return string
     */
    public function getLink($template, array $data = [], array $extraData = [])
    {
        switch($template) {
            default:
                $link = self::SETTLE_LINK;
                break;
            case self::LINK_TEMPLATE_PAYMENT:
                $link = self::PAYMENT_LINK;
                break;
            case self::LINK_TEMPLATE_SHORT_LINK:
                $link = self::SHORT_LINK_LINK;
                break;
        }

        foreach($data as $param => $value) {
            $link = str_replace(":{$param}", $value, $link);
        }

        if (!empty($extraData)) {
            $link .= http_build_query($extraData);
        }

        return $link;
    }

    /**
     * Get mobile friendly deep link
     *
     * @param string $short_link
     * @return string
     */
    public function getDeepLink($short_link)
    {
        $config = $this->getIsSandbox() ? self::DEEP_LINK_CONFIG_SANDBOX : self::DEEP_LINK_CONFIG_PRODUCTION;

        $deep_link = 'https://' . $config['baseUrl'] . '?';
        unset($config['baseUrl']);

        $config['link'] = 'https://' . $config['env'] . '://qr/' . $short_link;
        unset($config['env']);

        return $deep_link . http_build_query($config);
    }
}
