<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class Profile extends SettleApi
{
    public function get($merchant_id)
    {
        return $this->call('GET', "merchant/{$merchant_id}/");
    }

    public function lookup($merchant_id)
    {
        return $this->call('GET', "merchant_lookup/{$merchant_id}/");
    }
}
