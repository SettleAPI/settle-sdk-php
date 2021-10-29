<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;
use Danielz\SettleApi\SettleApiException;

/**
 * Class Balance
 * @package Danielz\SettleApi\MerchantApi
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
