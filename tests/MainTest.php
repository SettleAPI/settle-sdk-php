<?php

use SettleApi\SettleApiClient;
use SettleApi\MerchantApi\ApiKeys;
use SettleApi\MerchantApi\MerchantApi;

test('Magic property classes', function () {
    $merchant_api = new MerchantApi(new SettleApiClient('', '', '', '', true));

    expect($merchant_api->unknown)->toBeNull();
    expect($merchant_api->api_keys->unknown)->toBeNull();

    expect(get_class($merchant_api->api_keys))->toBe(ApiKeys::class);

    // test the object cache
    expect(spl_object_id($merchant_api->api_keys))->toBe(spl_object_id($merchant_api->api_keys));

    expect(method_exists($merchant_api->api_keys, 'list'))->toBeTrue();
});
