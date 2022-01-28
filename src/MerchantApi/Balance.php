<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class Balance
 * @package SettleApi\MerchantApi
 * @link https://settleapi.stoplight.io/docs/settleapis/b3A6MTUzOTU0NDE-merchant-balance-get
 */
class Balance extends SettleApi
{
    /**
     * @param string $merchant_id
     * @return array
     * @throws SettleApiException
     */
    public function get($merchant_id)
    {
        return $this->call('GET', "merchant/{$merchant_id}/balance/");
    }
}
