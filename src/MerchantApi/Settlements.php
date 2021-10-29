<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;
use Danielz\SettleApi\SettleApiException;

/**
 * Class Settlements
 * @package Danielz\SettleApi\MerchantApi
 */
class Settlements extends SettleApi
{
    /**
     * @return array
     * @throws SettleApiException
     */
    public function list()
    {
        return $this->call('GET', 'settlement/');
    }

    /**
     * @param string $settlement_id
     * @return array
     * @throws SettleApiException
     */
    public function get($settlement_id)
    {
        return $this->call('GET', "settlement/{$settlement_id}/");
    }

    /**
     * @return array
     * @throws SettleApiException
     */
    public function latest()
    {
        return $this->call('GET', 'last_settlement/');
    }

    /**
     * @return array
     * @throws SettleApiException
     */
    public function report()
    {
        return $this->call('GET', 'settlement_report/');
    }
}
