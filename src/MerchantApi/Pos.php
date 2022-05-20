<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class Pos
 * @package SettleApi\MerchantApi
 * @link https://settleapi.stoplight.io/docs/settleapis/b3A6MTUzOTU0MTY-merchant-pos-list
 */
class Pos extends SettleApi
{
    /**
     * @return array
     * @throws SettleApiException
     */
    public function list()
    {
        return $this->call('GET', 'merchant/v1/pos/');
    }

    /**
     * @param string $pos_id
     * @return array
     * @throws SettleApiException
     */
    public function get($pos_id)
    {
        return $this->call('GET', "merchant/v1/pos/{$pos_id}/");
    }

    /**
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function create(array $data)
    {
        return $this->call('POST', 'merchant/v1/pos/', $data);
    }

    /**
     * @param string $pos_id
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function update($pos_id, array $data)
    {
        return $this->call('PUT', "merchant/v1/pos/{$pos_id}/", $data);
    }

    /**
     * @param string $pos_id
     * @return array
     * @throws SettleApiException
     */
    public function delete($pos_id)
    {
        return $this->call('DELETE', "merchant/v1/pos/{$pos_id}/");
    }
}
