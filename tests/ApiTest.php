<?php

use SettleApi\MerchantApi\MerchantApi;
use SettleApi\SettleApiClient;
use SettleApi\SettleApiException;

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

test('API: ShortLinks', function() {
    global $merchant_api;

    $short_links_api = $merchant_api->short_links;
    $existing_count = count($short_links_api->list()['uris']);

    $short_link = $short_links_api->create(['callback_uri' => 'https://example.com']);
    expect($short_links_api->get($short_link['id'])['id'])->toBe($short_link['id']);
    expect(count($short_links_api->list()['uris']))->toBe($existing_count + 1);

    expect($short_links_api->update($short_link['id'], ['callback_uri' => 'https://example.com']))->toBeTrue();

    expect($short_links_api->delete($short_link['id']))->toBeTrue();
    expect(count($short_links_api->list()['uris']))->toBe($existing_count);


    try {
        $short_links_api->get($short_link['id']);
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
//    expect('pcqghkrpztq1')->toBeIn(array_column($requests['items'],'tid'));

    $request = $payment_requests_api->get('pcqghkrpztq1');
    expect(isset($request['id']))->toBeTrue();
    expect($request['id'])->toBe('pcqghkrpztq1');

    $request_data = [
        'action' => 'sale',
        'additional_edit' => false,
        'allow_credit' => true,
        'amount' => 100,
        'currency' => 'BGN',
        'max_scan_age' => 600,
        'pos_id' => '7DROG',
        'pos_tid' => date('YmdHis'),
    ];
    $request = $merchant_api->payment_requests->create($request_data);

    $outcome = $merchant_api->payment_requests->outcome($request['id']);
    expect($outcome['status'])->toBe('pending');
    expect($outcome['amount'])->toBe($request_data['amount']);

    try {
        $merchant_api->payment_requests->capture($request['id'], $request_data['currency'], $request_data['amount']);
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(409);
        expect($e->getMessage())->toContain("Tried to capture payment before customer authorized payment or after authorization has expired");
    }

    try {
        $merchant_api->payment_requests->refund($request['id'], $request_data['currency'], $request_data['amount']);
        expect(true)->toBeFalse(); // make sure we don't hit this line
    } catch (SettleApiException $e) {
        expect($e->getCode())->toBe(400);
        expect($e->getMessage())->toContain("The requested amount for refund is larger than the captured amount");
    }

    $merchant_api->payment_requests->update($request['id'], ['action' => 'abort']);
    $outcome = $merchant_api->payment_requests->outcome($request['id']);
    expect($outcome['status'])->toBe('fail');
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
    $api_client->setIsSandbox(true);

    // Global
    expect($api_client->createLink('missing'))->toBe(SettleApiClient::SETTLE_LINK);
    expect($api_client->createLink(SettleApiClient::LINK_TEMPLATE_PAYMENT))->toBe(SettleApiClient::PAYMENT_LINK);
    expect($api_client->createLink(SettleApiClient::LINK_TEMPLATE_SHORT_LINK))->toBe(SettleApiClient::SHORT_LINK_LINK);

    // Payment Requests
    $api = $merchant_api->payment_requests;
    expect($api->getLink('pcqghkrpztq1'))->toBe('http://settle.eu/p/pcqghkrpztq1/');
    expect($api->getLink('pcqghkrpztq1', ['a' => 'b', 'c' => 1]))->toBe('http://settle.eu/p/pcqghkrpztq1/a=b&c=1');

    // Deep links
    $deepLink = 'https://settledemo.page.link?apn=eu.settle.app.sandbox&ibi=eu.settle.app.sandbox&isi=1453180781&ius=eu.settle.app.firebaselink&link=https%3A%2F%2Fsettle-demo%3A%2F%2Fqr%2Fhttp%3A%2F%2Fsettle.eu%2Fp%2Fpypz44mcswz3%2F';
    expect($api->getDeepLink('pypz44mcswz3'))->toBe($deepLink);
    expect($api_client->getDeepLink('http://settle.eu/p/pypz44mcswz3/'))->toBe($deepLink);

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

test('Callback validation', function () {
   global $api_client;

    $body = '{"meta": {"seqno": 0, "labels": ["timeline"], "uri": "https://api-dot-settle-core-demo.appspot.com/merchant/v1/payment_request/phkan3yvn4ex/outcome/", "id": "daMUL1Q1R9yP9K_EQrVnDQ", "context": "ctx:daMUL1Q1R9yP9K_EQrVnDQ", "timestamp": "2021-11-07 07:20:59", "event": "payment_aborted_by_customer"}, "object": {"status": "fail", "customer": "token:5703313655857152", "refunds": [], "auth_amount": 0, "auth_additional_amount": 0, "credit": false, "captures": [], "pos_id": "pos123", "date_modified": "2021-11-07 07:20:42", "date_expires": "2021-11-07 13:20:42", "currency": "NOK", "amount": 2900, "interchange_fee": 0, "status_code": 5006, "tid": "phkan3yvn4ex", "attachment_uri": "https://settle-core-demo.appspot.com/_ah/upload/AMmfu6a49Ta2AXX8NEgjcDCCmlfTwjiPOZ6OBK4uQ0LRwFbU-hR1JU6clKjAhFvaWjL6u6JvqLxRISX1CtXW4yp7yNqNY8-ZNVNFFhkajid8QeqnHNBIXVmtg4p7KJAVm3ElYqXPuGG9_qsxF7mf3rpmrbuYjzp2fT1iQaOwzVe--LV30upHVcxkDVy0lpJMxyKh86RKswuqlcVRM4JHpwlCW-aONlM4roY2mF14Al3fEebjTPg8n8oSlvdcwfFcbIvDTBTlpLCOOgZGGipOl5zkd4eIH8zHP6kpSwy_V5pKQqhjN1Odb6hdUuKNpJGspXrgXw3JVCz3kiRokRgy7rshbIAKLvuK2Xnn9gNR3JfTCI8AwnAcXws/ALBNUaYAAAAAYYeAs9Shob9n0EBUTaZnj-jRHCTZp1vr/", "pos_tid": "6i6tEp46pERGGi6ZbxkyV3", "permissions": null, "transaction_fee": 0, "additional_amount": 0}}';
    $headers = [
        'HTTP_AUTHORIZATION' => 'RSA-SHA256 p8bHuIN3I41lof3zEQYlEyGfj0N+uyZW2wY2xR+x7oAbfwZtRjaX8wI3QVaF23wS+d2fgJiJ0ZJqumz0rqwBcGlKy1sqhNGiA1QXfJs0o79vptex/+CGfVm7cdtCPhv2fougwHFGx6uAlYozYUpQGcIPHD4DmRFZpPpOEn7Vc9I=',
        'HTTP_X_SETTLE_TIMESTAMP' => '2021-11-07 07:21:00',
        'CONTENT_TYPE' => 'application/vnd.mcash.api.merchant.v1+json',
        'HTTP_X_SETTLE_CONTENT_DIGEST' => 'SHA256=qnm7VZVajBcZ+p506yfEhm7tC4hTA0q7F5YXyxd1WUA=',
    ];
    $callbackUrl = 'https://daniel-zahariev.info/requestbin/settle.php';

    expect($api_client->isValidCallback($callbackUrl, $body, $headers))->toBeTrue();
});
