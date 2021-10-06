<?php

use Danielz\SettleApi\MerchantApi\MerchantApi;
use Danielz\SettleApi\SettleApiClient;
use Danielz\SettleApi\SettleApiException;

// Questions:
// Can we pass `serial_number` when creating shortlinks
//

$merchant_api = new MerchantApi(new SettleApiClient(SETTLE_MERCHANT_ID, SETTLE_USER_ID, SETTLE_PUBLIC_KEY, SETTLE_PRIVATE_KEY, SETTLE_IN_SANDBOX));

test('API: Api Keys', function() {
    global $merchant_api;

    $api_keys_api = $merchant_api->api_keys;
    $existing_count = count($api_keys_api->list()['items']);

    $api_key = $api_keys_api->create([
        'label' => 'PEST test',
        'key_type' => 'secret',
        'roles' => 'user',
        'secret' => 'some secret',
    ]);

    $api_key_fresh = $api_keys_api->get($api_key['id']);
    expect($api_key_fresh['id'])->toBe($api_key['id']);
    expect($api_key_fresh['has_secret'])->toBeTrue();
    expect(count($api_keys_api->list()['items']))->toBe($existing_count + 1);

    expect($api_keys_api->update($api_key['id'], ['secret' => 'new secret']))->toBeTrue();

    expect($api_keys_api->delete($api_key['id']))->toBeTrue();
    expect(count($api_keys_api->list()['items']))->toBe($existing_count);
});

test('API: POS', function() {
    global $merchant_api;

    $pos_api = $merchant_api->pos;
    $existing_count = count($pos_api->list()['uris']);

    $pos_data = [
        'name' => 'PEST shop',
        'type' => 'webshop',
        'id' => 'pos1',
    ];

    $pos = $pos_api->create($pos_data);
    expect($pos_api->get($pos['id'])['id'])->toBe($pos['id']);
    expect(count($pos_api->list()['uris']))->toBe($existing_count + 1);

    try {
        // we shouldn't be able to create a POS with the same id
        $pos_api->create($pos_data);
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(409);
    }

    expect($pos_api->update($pos['id'], ['name' => 'PEST shop 2', 'type' => 'webshop']))->toBeTrue();

    expect($pos_api->delete($pos['id']))->toBeTrue();
    expect(count($pos_api->list()['uris']))->toBe($existing_count);


    try {
        $pos_api->get($pos['id']);
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(404);
    }
});

test('API: Shortlinks', function() {
    global $merchant_api;

    $shortlinks_api = $merchant_api->shortlinks;
    $existing_count = count($shortlinks_api->list()['uris']);

    $shortlink = $shortlinks_api->create(['callback_uri' => 'https://example.com']);
    expect($shortlinks_api->get($shortlink['id'])['id'])->toBe($shortlink['id']);
    expect(count($shortlinks_api->list()['uris']))->toBe($existing_count + 1);

    expect($shortlinks_api->update($shortlink['id'], ['callback_uri' => 'https://example.com']))->toBeTrue();

    expect($shortlinks_api->delete($shortlink['id']))->toBeTrue();
    expect(count($shortlinks_api->list()['uris']))->toBe($existing_count);


    try {
        $shortlinks_api->get($shortlink['id']);
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(404);
    }
});

test('API: Settlements', function() {
    global $merchant_api;

    $settlements_api = $merchant_api->settlements;
    $existing_count = count($settlements_api->list()['uris']);
    expect($existing_count)->toBeGreaterThan(0);

    expect((int)$settlements_api->get(1)['id'])->toBe(1);
});

test('API [not working]: Logo, SalesSummary, Settlements, SettlementAccounts', function() {
    global $merchant_api;

    try {
        // Currently this endpoint isn't working
        $merchant_api->logo->get(SETTLE_MERCHANT_ID);
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(404);
    }

    try {
        // Missing 'X-Appengine-Inbound-Appid' header
        $merchant_api->sales_summary->get();
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(400);
    }

    try {
        // This returns an odd response code
        $merchant_api->settlements->latest();
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(302);
    }

    try {
        // Missing 'X-Appengine-Inbound-Appid' header
        $merchant_api->settlements->report();
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(400);
    }

    try {
        // Missing 'X-Appengine-Inbound-Appid' header
        $merchant_api->settlement_accounts->get(1);
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(400);
    }

    try {
        // Missing 'X-Appengine-Inbound-Appid' header
        $merchant_api->settlement_accounts->update(1, []);
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBeIn([400, 411]);
    }
});

test('API: Profile, Balance, StatusCodes', function() {
    global $merchant_api;

    $merchant_profile = $merchant_api->profile->get(SETTLE_MERCHANT_ID);
    expect($merchant_profile['id'])->toBe(SETTLE_MERCHANT_ID);

    try {
        // lookup is only available for integrators
        $merchant_api->profile->lookup(SETTLE_MERCHANT_ID);
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(401);
    }

    $balance = $merchant_api->balance->get(SETTLE_MERCHANT_ID);
    expect($balance['currency'])->toBe('BGN');

    $status_codes = $merchant_api->status_codes->list();
    expect(count($status_codes))->toBeGreaterThan(50);

    $status_code_5901 = $merchant_api->status_codes->get(5901);
    expect($status_code_5901['name'])->toBe('INSUFFICIENT_MCASH_BALANCE');
});
