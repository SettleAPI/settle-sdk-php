<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;
use Danielz\SettleApi\SettleApiException;

/**
 * Class Shortlinks
 * @package Danielz\SettleApi\MerchantApi
 */
class Shortlinks extends SettleApi
{
    /**
     * @return array
     * @throws SettleApiException
     */
    public function list()
    {
        return $this->call('GET', 'shortlink/');
    }

    /**
     * @param string $shortlink_id
     * @return array
     * @throws SettleApiException
     */
    public function get($shortlink_id)
    {
        return $this->call('GET', "shortlink/{$shortlink_id}/");
    }

    /**
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function create(array $data)
    {
        return $this->call('POST', 'shortlink/', $data);
    }

    /**
     * @param string $shortlink_id
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function update($shortlink_id, array $data)
    {
        return $this->call('PUT', "shortlink/{$shortlink_id}/", $data);
    }

    /**
     * @param string $shortlink_id
     * @return array
     * @throws SettleApiException
     */
    public function delete($shortlink_id)
    {
        return $this->call('DELETE', "shortlink/{$shortlink_id}/");
    }
}
