<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class Pos extends SettleApi
{
    public function list()
    {
        return $this->call('GET', 'pos/');
    }

    public function get($pos_id)
    {
        return $this->call('GET', "pos/{$pos_id}/");
    }

    public function create(array $data)
    {
        return $this->call('POST', 'pos/', $data);
    }

    public function update($pos_id, array $data)
    {
        return $this->call('PUT', "pos/{$pos_id}/", $data);
    }

    public function delete($pos_id)
    {
        return $this->call('DELETE', "pos/{$pos_id}/");
    }
}
