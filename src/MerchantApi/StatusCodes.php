<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class StatusCodes extends SettleApi
{
    public function list()
    {
        return $this->call('GET', "status_code/");
    }

    public function get($status_code_id)
    {
        return $this->call('GET', "status_code/{$status_code_id}/");
    }
}
