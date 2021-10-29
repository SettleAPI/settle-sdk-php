<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;
use Danielz\SettleApi\SettleApiException;

/**
 * Class PaymentSends
 * @package Danielz\SettleApi\MerchantApi
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
     * @param $payment_send_id
     * @return array
     * @throws SettleApiException
     */
    public function outcome($payment_send_id)
    {
        return $this->call('GET', "payment/{$payment_send_id}/outcome/");
    }
}
