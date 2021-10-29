<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;
use Danielz\SettleApi\SettleApiException;

/**
 * Class Profile
 * @package Danielz\SettleApi\MerchantApi
 */
class Profile extends SettleApi
{
    /**
     * @param string $merchant_id
     * @return array
     * @throws SettleApiException
     */
    public function get($merchant_id)
    {
        return $this->call('GET', "merchant/{$merchant_id}/");
    }

    /**
     * @param string $merchant_id
     * @return array
     * @throws SettleApiException
     */
    public function lookup($merchant_id)
    {
        return $this->call('GET', "merchant_lookup/{$merchant_id}/");
    }
}
