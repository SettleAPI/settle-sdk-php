<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiClient;
use SettleApi\SettleApiException;

/**
 * Class ShortLinks
 * @package SettleApi\MerchantApi
 * @link https://settleapi.stoplight.io/docs/settleapis/b3A6MTUzOTU0Mjg-merchant-shortlink-list
 */
class ShortLinks extends SettleApi
{
    /**
     * @return array
     * @throws SettleApiException
     */
    public function list()
    {
        return $this->call('GET', 'merchant/v1/shortlink/');
    }

    /**
     * @param string $short_link_id
     * @return array
     * @throws SettleApiException
     */
    public function get($short_link_id)
    {
        return $this->call('GET', "merchant/v1/shortlink/{$short_link_id}/");
    }

    /**
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function create(array $data)
    {
        return $this->call('POST', 'merchant/v1/shortlink/', $data);
    }

    /**
     * @param string $short_link_id
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function update($short_link_id, array $data)
    {
        return $this->call('PUT', "merchant/v1/shortlink/{$short_link_id}/", $data);
    }

    /**
     * @param string $short_link_id
     * @return array
     * @throws SettleApiException
     */
    public function delete($short_link_id)
    {
        return $this->call('DELETE', "merchant/v1/shortlink/{$short_link_id}/");
    }

    /**
     * @param string $short_link_id
     * @param array $extraData
     * @return string
     */
    public function getLink($short_link_id, $extraData = [])
    {
        return $this->createLink(SettleApiClient::LINK_TEMPLATE_SHORT_LINK, compact('short_link_id'), $extraData);
    }

    /**
     * @param string $short_link_id
     * @param array $extraData
     * @return string
     */
    public function getDeepLink($short_link_id, $extraData = [])
    {
        return $this->createDeepLink($this->getLink($short_link_id, $extraData));
    }
}
