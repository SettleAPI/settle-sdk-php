<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class Profile
 * @package SettleApi\MerchantApi
 * @link https://api.support.settle.eu/api/reference/rest/v1/merchant.profile/
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
        return $this->call('GET', "merchant/v1/merchant/{$merchant_id}/");
    }

    /**
     * @param string $merchant_id
     * @return array
     * @throws SettleApiException
     */
    public function lookup($merchant_id)
    {
        return $this->call('GET', "merchant/v1/merchant_lookup/{$merchant_id}/");
    }
}
