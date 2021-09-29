<?php

namespace Danielz\SettleApi;


use Danielz\SettleApi\MerchantApi\MerchantApi;

/**
 * Class SettleApi
 * @package Danielz
 *
 * @property MerchantApi merchants
 */
class SettleApi
{
    private SettleApiClient $api_client;
    private $materializedProperties = [];

    /**
     * SettleMerchantApi constructor.
     * @param SettleApiClient $api_client
     */
    final public function __construct(SettleApiClient $api_client)
    {
        echo "new " . static::class . " class  \n";
        $this->api_client = $api_client;
    }

    final protected function call($method, $path, $data = [])
    {
        return $this->api_client->call($method, $path, $data);
    }

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

    protected function getMagicProperties()
    {
        return [
            'merchants' => MerchantApi::class,
        ];
    }


}
