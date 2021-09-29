<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class SettlementAccounts extends SettleApi
{
    public function get($settlement_id)
    {
        return $this->call('GET', "settlement_account/{$settlement_id}/");
    }

    public function update($settlement_id, array $data)
    {
        return $this->call('PUT', "settlement_account/{$settlement_id}/", $data);
    }

}
