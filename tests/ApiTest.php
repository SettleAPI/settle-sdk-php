<?php

use Danielz\SettleApi\MerchantApi\MerchantApi;
use Danielz\SettleApi\SettleApiClient;
use Danielz\SettleApi\SettleApiException;

// Questions:
// Can we pass `serial_number` when creating shortlinks
//

$api_client = new SettleApiClient(
    SETTLE_MERCHANT_ID,
    SETTLE_USER_ID,
    SETTLE_PUBLIC_KEY,
    SETTLE_PRIVATE_KEY,
    SETTLE_IN_SANDBOX
);
$merchant_api = new MerchantApi($api_client);

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

test('API: Payment Requests', function() {
    global $merchant_api;
    $payment_requests_api = $merchant_api->payment_requests;

    $requests = $payment_requests_api->list();
    expect(isset($requests['items']))->toBeTrue();
    expect(count($requests['items']))->toBeGreaterThan(1);
    expect('pcqghkrpztq1')->toBeIn(array_column($requests['items'],'tid'));

    $request = $payment_requests_api->get('pcqghkrpztq1');
    expect(isset($request['id']))->toBeTrue();
    expect($request['id'])->toBe('pcqghkrpztq1');
});

test('API [not working]: Settlements', function() {
    global $merchant_api;

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


test('API: Links', function() {
    global $api_client, $merchant_api;

    expect($api_client->createLink('missing'))->toBe(SettleApiClient::SETTLE_LINK);
    expect($api_client->createLink('payment_link'))->toBe(SettleApiClient::PAYMENT_LINK);

    $isSandbox = $api_client->getIsSandbox();
    $api_client->setIsSandbox(true);
    expect($api_client->createLink('payment_link_mobile'))->toBe(SettleApiClient::PAYMENT_LINK_MOBILE_SANDBOX);
    $api_client->setIsSandbox(false);
    expect($api_client->createLink('payment_link_mobile'))->toBe(SettleApiClient::PAYMENT_LINK_MOBILE);
    $api_client->setIsSandbox($isSandbox);

    $api = $merchant_api->payment_requests;
    expect($api->getPaymentLink('pcqghkrpztq1'))->toBe('https://settle.eu/p/pcqghkrpztq1/');
    expect($api->getMobilePaymentLink('pcqghkrpztq1'))->toBe('https://settledemo.page.link/?apn=eu.settle.app.sandbox&ibi=eu.settle.app.sandbox&isi=1453180781&ius=eu.settle.app.firebaselink&link=https://settle-demo://qr/http://settle.eu/p/pcqghkrpztq1/');
});

test('API: Utility', function() {
    global $api_client;

    try {
        // Test unknown API path to confirm exceptions are working
        $api_client->call('GET', 'unknown');
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(404);
    }
});
