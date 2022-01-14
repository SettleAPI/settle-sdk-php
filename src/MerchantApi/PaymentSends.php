<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class PaymentSends
 * @package SettleApi\MerchantApi
 * @link https://api.support.settle.eu/api/reference/rest/v1/merchant.payment.send/
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
