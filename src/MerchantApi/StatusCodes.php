<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;
use Danielz\SettleApi\SettleApiException;

/**
 * Class StatusCodes
 * @package Danielz\SettleApi\MerchantApi
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
