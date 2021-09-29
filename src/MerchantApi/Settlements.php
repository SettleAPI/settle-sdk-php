<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class Settlements extends SettleApi
{
    public function list()
    {
        return $this->call('GET', 'settlement/');
    }

    public function get($settlement_id)
    {
        return $this->call('GET', "settlement/{$settlement_id}/");
    }

    public function latest()
    {
        return $this->call('GET', 'last_settlement/');
    }

    public function report()
    {
        return $this->call('GET', 'settlement_report/');
    }
}
