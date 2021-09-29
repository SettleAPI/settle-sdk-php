<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class SalesSummary extends SettleApi
{
    public function get()
    {
        return $this->call('GET', "sales_summary/");
    }
}
