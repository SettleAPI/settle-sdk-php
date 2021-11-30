<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiClient;
use SettleApi\SettleApiException;

/**
 * Class PaymentRequests
 * @package SettleApi\MerchantApi
 * @link https://api.support.settle.eu/api/reference/rest/v1/merchant.payment.request/
 */
class PaymentRequests extends SettleApi
{
    /**
     * @return array
     * @throws SettleApiException
     */
    public function list()
    {
        return $this->call('GET', 'payment_request/');
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
        return $this->call('POST', 'payment_request/', $data);
    }

    /**
     * @param string $payment_request_id
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function update($payment_request_id, array $data)
    {
        return $this->call('PUT', "payment_request/{$payment_request_id}/", $data);
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
     * @return string
     */
    public function getPaymentLink($payment_request_id)
    {
        return $this->createLink(SettleApiClient::LINK_TEMPLATE_PAYMENT, compact('payment_request_id'));
    }

    /**
     * @param string $payment_request_id
     * @param array $socialData
     * @return string
     */
    public function getDynamicLink($payment_request_id, $socialData = [])
    {
        return $this->createLink(SettleApiClient::LINK_TEMPLATE_DYNAMIC, compact('payment_request_id'), $socialData);
    }
}
