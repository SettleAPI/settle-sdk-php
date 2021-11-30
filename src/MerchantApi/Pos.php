<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class Pos
 * @package SettleApi\MerchantApi
 * @link https://api.support.settle.eu/api/reference/rest/v1/merchant.pos/
 */
class Pos extends SettleApi
{
    /**
     * @return array
     * @throws SettleApiException
     */
    public function list()
    {
        return $this->call('GET', 'pos/');
    }

    /**
     * @param string $pos_id
     * @return array
     * @throws SettleApiException
     */
    public function get($pos_id)
    {
        return $this->call('GET', "pos/{$pos_id}/");
    }

    /**
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function create(array $data)
    {
        return $this->call('POST', 'pos/', $data);
    }

    /**
     * @param string $pos_id
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function update($pos_id, array $data)
    {
        return $this->call('PUT', "pos/{$pos_id}/", $data);
    }

    /**
     * @param string $pos_id
     * @return array
     * @throws SettleApiException
     */
    public function delete($pos_id)
    {
        return $this->call('DELETE', "pos/{$pos_id}/");
    }
}
