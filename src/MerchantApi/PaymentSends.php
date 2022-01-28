<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class PaymentSends
 * @package SettleApi\MerchantApi
 * @link https://settleapi.stoplight.io/docs/settleapis/b3A6MTUzOTU0Mzc-merchant-payment-send-create
 */
class PaymentSends extends SettleApi
{
    /**
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function create(array $data)
    {
        return $this->call('POST', 'payment/', $data);
    }

    /**
     * @param string $payment_send_id
     * @return array
     * @throws SettleApiException
     */
    public function outcome($payment_send_id)
    {
        return $this->call('GET', "payment/{$payment_send_id}/outcome/");
    }
}
