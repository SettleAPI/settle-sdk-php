<?php

namespace SettleApi;

/**
 * Class SettleApi
 * @package SettleApi
 */
abstract class SettleApi
{
    private SettleApiClient $api_client;
    private $materializedProperties = [];

    /**
     * SettleMerchantApi constructor.
     * @param SettleApiClient $api_client
     */
    final public function __construct(SettleApiClient $api_client)
    {
        $this->api_client = $api_client;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    final protected function call(string $method, string $path, array $data = [])
    {
        return $this->api_client->call($method, $path, $data);
    }

    /**
     * @return bool
     */
    final protected function isSandbox()
    {
        return $this->api_client->getIsSandbox();
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (!isset($this->materializedProperties[$name])) {
            $magic_properties = $this->getMagicProperties();

            if (isset($magic_properties[$name])) {
                $this->materializedProperties[$name] = new $magic_properties[$name]($this->api_client);
            } else {
                $this->materializedProperties[$name] = null;
            }
        }

        return $this->materializedProperties[$name];
    }

    /**
     * @return string[]
     */
    protected function getMagicProperties()
    {
        return [];
    }

    /**
     * @return string
     */
    protected function createLink($template, array $data = [], array $extraData = [])
    {
        return $this->api_client->createLink($template, $data, $extraData);
    }
}
