<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class Settlements
 * @package SettleApi\MerchantApi
 * @link https://settleapi.stoplight.io/docs/settleapis/b3A6MTUzOTU0MjM-merchant-settlement-list
 */
class Settlements extends SettleApi
{
    /**
     * @return array
     * @throws SettleApiException
     */
    public function list()
    {
        return $this->call('GET', 'merchant/v1/settlement/');
    }

    /**
     * @param string $settlement_id
     * @return array
     * @throws SettleApiException
     */
    public function get($settlement_id)
    {
        return $this->call('GET', "merchant/v1/settlement/{$settlement_id}/");
    }

    /**
     * @return array
     * @throws SettleApiException
     * @todo This endpoint isn't working properly
     */
    public function latest()
    {
        return $this->call('GET', 'merchant/v1/last_settlement/');
    }

    /**
     * @return array
     * @throws SettleApiException
     */
    public function report()
    {
        return $this->call('GET', 'merchant/v1/settlement_report/');
    }
}
