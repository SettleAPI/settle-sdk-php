<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class StatusCodes
 * @package SettleApi\MerchantApi
 * @link https://api.support.settle.eu/api/reference/rest/v1/merchant.statusCodes/
 */
class StatusCodes extends SettleApi
{
    /**
     * @return array
     * @throws SettleApiException
     */
    public function list()
    {
        return $this->call('GET', "status_code/");
    }

    /**
     * @param string $status_code_id
     * @return array
     * @throws SettleApiException
     */
    public function get($status_code_id)
    {
        return $this->call('GET', "status_code/{$status_code_id}/");
    }
}
