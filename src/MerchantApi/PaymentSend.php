<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class PaymentSend
 * @package SettleApi\MerchantApi
 * @link https://settleapi.stoplight.io/docs/settleapis/b3A6MTUzOTU0Mzc-merchant-payment-send-create
 */
class PaymentSend extends SettleApi
{
    /**
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function create(array $data)
    {
        return $this->call('POST', 'payment/v2/payment/', $data);
    }

    /**
     * @param string $payment_send_id
     * @return array
     * @throws SettleApiException
     */
    public function get($payment_send_id)
    {
        return $this->call('GET', "payment/v2/payment/{$payment_send_id}/");
    }
}
