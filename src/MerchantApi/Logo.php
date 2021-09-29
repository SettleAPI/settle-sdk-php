<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class Logo extends SettleApi
{
    public function get($merchant_id)
    {
        return $this->call('GET', "merchant/{$merchant_id}/logo/");
    }
}
