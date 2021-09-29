<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class ApiKeys extends SettleApi
{
    public function list()
    {
        return $this->call('GET', 'api_key/');
    }

    public function get($api_key_id)
    {
        return $this->call('GET', "api_key/{$api_key_id}/");
    }

    public function create(array $data)
    {
        return $this->call('POST', 'api_key/', $data);
    }

    public function update($api_key_id, array $data)
    {
        return $this->call('PUT', "api_key/{$api_key_id}/", $data);
    }

    public function delete($api_key_id)
    {
        return $this->call('DELETE', "api_key/{$api_key_id}/");
    }
}
