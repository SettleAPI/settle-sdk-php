<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiClient;
use SettleApi\SettleApiException;

/**
 * Class PaymentRequests
 * @package SettleApi\MerchantApi
 * @link https://settleapi.stoplight.io/docs/settleapis/b3A6MTUzOTU0MTE-merchant-payment-request-list
 */
class PaymentRequests extends SettleApi
{
    const REQUEST_SHAPE = [
        'customer' => 'string',
        'action' => 'required|string',
        'currency' => 'string',
        'amount' => 'numeric',
        'pos_id' => 'string',
        'pos_tid' => 'string',
        'additional_amount' => 'numeric',
        'additional_edit' => 'bool',
        'allow_credit' => 'bool',
        'required_scope' => 'string',
        'required_scope_text' => 'string',
        'uri' => 'string',
        'scope' => 'string',
        'callback_uri' => 'string',
        'success_return_uri' => 'string',
        'failure_return_uri' => 'string',
        'display_message_uri' => 'string',
        'expires_in' => 'numeric',
        'max_scan_age' => 'numeric',
        'text' => 'string',
        'refund_id' => 'string',
        'capture_id' => 'string',
        'line_items' => 'any',
        'links' => 'any',
    ];

    /**
     * @return array
     * @throws SettleApiException
     */
    public function list($query = [])
    {
        return $this->call('GET', 'payment_request/', $query);
    }

    /**
     * @param string $payment_request_id
     * @return array
     * @throws SettleApiException
     */
    public function get($payment_request_id)
    {
        return $this->call('GET', "payment_request/{$payment_request_id}/");
    }

    /**
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function create(array $data)
    {
        return $this->call('POST', 'payment_request/', $data, self::REQUEST_SHAPE);
    }

    /**
     * @param string $payment_request_id
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function update($payment_request_id, array $data)
    {
        $path = "payment_request/{$payment_request_id}/";

        return $this->call('PUT', $path, $data, self::REQUEST_SHAPE);
    }

    /**
     * @param string $payment_request_id
     * @return array
     * @throws SettleApiException
     */
    public function outcome($payment_request_id)
    {
        return $this->call('GET', "payment_request/{$payment_request_id}/outcome/");
    }

    /**
     * @param string $payment_request_id
     * @param string $currency
     * @param float|int $amount
     * @param float|int $additional_amount
     * @param string $capture_id
     * @return array
     * @throws SettleApiException
     */
    public function capture($payment_request_id, $currency, $amount, $additional_amount = 0, $capture_id = '')
    {
        if (empty($capture_id)) {
            $capture_id = 'cap_' . date('YmdHis');
        }

        $data = [
            'action' => 'capture',
            'currency' => $currency,
            'amount' => $amount,
            'additional_amount' => $additional_amount,
            'capture_id' => $capture_id,
        ];

        return $this->update($payment_request_id, $data);
    }

    /**
     * @param string $payment_request_id
     * @param string $currency
     * @param float $amount
     * @param float|int $additional_amount
     * @param string $message
     * @param string $refund_id
     * @return array
     * @throws SettleApiException
     */
    public function refund($payment_request_id, $currency, $amount, $additional_amount = 0, $message = '', $refund_id = '')
    {
        if (empty($refund_id)) {
            $refund_id = 'ref_' . date('YmdHis');
        }

        $data = [
            'action' => 'refund',
            'currency' => $currency,
            'amount' => $amount,
            'additional_amount' => $additional_amount,
            'refund_id' => $refund_id,
            'text' => $message,
        ];

        return $this->update($payment_request_id, $data);
    }

    /**
     * @param string $payment_request_id
     * @param array $extraData
     * @return string
     */
    public function getLink($payment_request_id, $extraData = [])
    {
        return $this->createLink(SettleApiClient::LINK_TEMPLATE_PAYMENT, compact('payment_request_id'), $extraData);
    }

    /**
     * @param string $payment_request_id
     * @param array $extraData
     * @return string
     */
    public function getDeepLink($payment_request_id, $extraData = [])
    {
        return $this->createDeepLink($this->getLink($payment_request_id, $extraData));
    }
}
