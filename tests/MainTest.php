<?php

use Danielz\SettleApi\SettleApiClient;
use Danielz\SettleApi\SettleApi;
use Danielz\SettleApi\MerchantApi\ApiKeys;
use Danielz\SettleApi\MerchantApi\MerchantApi;

test('Magic property classes', function () {
    $settle_api = new SettleApi(new SettleApiClient('', '', '', '', true));

    expect(get_class($settle_api->merchants) == MerchantApi::class)->toBeTrue();
    expect(spl_object_id($settle_api->merchants) == spl_object_id($settle_api->merchants))->toBeTrue();
    expect(get_class($settle_api->merchants->api_keys) == ApiKeys::class)->toBeTrue();
    expect(method_exists($settle_api->merchants->api_keys, 'list'))->toBeTrue();
});
