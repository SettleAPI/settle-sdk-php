<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class Shortlinks extends SettleApi
{
    public function list()
    {
        return $this->call('GET', 'shortlink/');
    }

    public function get($shortlink_id)
    {
        return $this->call('GET', "shortlink/{$shortlink_id}/");
    }

    public function create(array $data)
    {
        return $this->call('POST', 'shortlink/', $data);
    }

    public function update($shortlink_id, array $data)
    {
        return $this->call('PUT', "shortlink/{$shortlink_id}/", $data);
    }

    public function delete($shortlink_id)
    {
        return $this->call('DELETE', "shortlink/{$shortlink_id}/");
    }
}
