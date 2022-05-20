<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class StatusCodes
 * @package SettleApi\MerchantApi
 * @link https://settleapi.stoplight.io/docs/settleapis/b3A6MTUzOTU0MzU-merchant-status-codes-list
 */
class StatusCodes extends SettleApi
{
    /**
     * @return array
     * @throws SettleApiException
     */
    public function list()
    {
        return $this->call('GET', "merchant/v1/status_code/");
    }

    /**
     * @param string $status_code_id
     * @return array
     * @throws SettleApiException
     */
    public function get($status_code_id)
    {
        return $this->call('GET', "merchant/v1/status_code/{$status_code_id}/");
    }
}
