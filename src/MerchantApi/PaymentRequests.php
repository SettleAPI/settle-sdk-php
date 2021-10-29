<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;
use Danielz\SettleApi\SettleApiException;

/**
 * Class PaymentRequests
 * @package Danielz\SettleApi\MerchantApi
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
     * @return array
     */
    public function capture($payment_request_id, $currency, $amount, $additional_amount = 0)
    {
        $data = [
            'action' => 'capture',
            'currency' => $currency,
            'amount' => $amount,
            'additional_amount' => $additional_amount,
            'capture_id' => 'cap_' . date('YmdHis'),
        ];

        return $this->update($payment_request_id, $data);
    }

    /**
     * @param string $payment_request_id
     * @param string $currency
     * @param float $amount
     * @param float|int $additional_amount
     * @param string $message
     * @return array
     */
    public function refund($payment_request_id, $currency, $amount, $additional_amount = 0, $message = '')
    {
        $data = [
            'action' => 'refund',
            'currency' => $currency,
            'amount' => $amount,
            'additional_amount' => $additional_amount,
            'refund_id' => 'ref_' . date('YmdHis'),
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
        return $this->createLink('payment_link', compact('payment_request_id'));
    }

    /**
     * @param string $payment_request_id
     * @return string
     */
    public function getMobilePaymentLink($payment_request_id)
    {
        return $this->createLink('payment_link_mobile', compact('payment_request_id'));
    }
}
