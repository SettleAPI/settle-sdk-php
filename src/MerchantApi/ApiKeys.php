<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;
use Danielz\SettleApi\SettleApiException;

/**
 * Class ApiKeys
 * @package Danielz\SettleApi\MerchantApi
 */
class ApiKeys extends SettleApi
{
    /**
     * @return array
     * @throws SettleApiException
     */
    public function list()
    {
        return $this->call('GET', 'api_key/');
    }

    /**
     * @param string $api_key_id
     * @return array
     * @throws SettleApiException
     */
    public function get($api_key_id)
    {
        return $this->call('GET', "api_key/{$api_key_id}/");
    }

    /**
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function create(array $data)
    {
        return $this->call('POST', 'api_key/', $data);
    }

    /**
     * @param string $api_key_id
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function update($api_key_id, array $data)
    {
        return $this->call('PUT', "api_key/{$api_key_id}/", $data);
    }

    /**
     * @param string $api_key_id
     * @return array
     * @throws SettleApiException
     */
    public function delete($api_key_id)
    {
        return $this->call('DELETE', "api_key/{$api_key_id}/");
    }
}
