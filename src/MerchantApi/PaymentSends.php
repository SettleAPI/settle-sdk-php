<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class PaymentSends extends SettleApi
{
    public function create(array $data)
    {
        return $this->call('POST', 'payment/', $data);
    }

    public function outcome($payment_send_id)
    {
        return $this->call('GET', "payment/{$payment_send_id}/outcome/");
    }
}
