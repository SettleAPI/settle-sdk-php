<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class SettlementAccounts extends SettleApi
{
    public function get($account_id)
    {
        return $this->call('GET', "settlement_account/{$account_id}/");
    }

    public function update($account_id, array $data)
    {
        return $this->call('PUT', "settlement_account/{$account_id}/", $data);
    }

}
