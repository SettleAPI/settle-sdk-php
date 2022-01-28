<?php

use SettleApi\SettleApiClient;
use SettleApi\MerchantApi\ApiKeys;
use SettleApi\MerchantApi\MerchantApi;
use SettleApi\SettleApiException;

test('Magic property classes', function () {
    $merchant_api = new MerchantApi(new SettleApiClient('', '', '', '', true));

    expect($merchant_api->unknown)->toBeNull();
    expect($merchant_api->api_keys->unknown)->toBeNull();

    expect(get_class($merchant_api->api_keys))->toBe(ApiKeys::class);

    // test the object cache
    expect(spl_object_id($merchant_api->api_keys))->toBe(spl_object_id($merchant_api->api_keys));

    expect(method_exists($merchant_api->api_keys, 'list'))->toBeTrue();
});

test('Callback data', function () {
    $api = new SettleApiClient('', '', '', '', true);
    try {
        $api->getCallbackData();
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getMessage())->toBe("Authorization header is missing.");
    }
});


test('Shape valiation', function () {
    $api = new SettleApiClient('', '', '', '', true);
    expect($api->getValidateShapes())->toBeTrue();
    $api->setValidateShapes(false);
    expect($api->getValidateShapes())->toBeFalse();
});
