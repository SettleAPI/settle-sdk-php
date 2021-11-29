[![License](https://img.shields.io/badge/license-Apache%202-brightgreen.svg)](https://github.com/daniel-zahariev/music-codes/blob/master/COPYING)
[![Build Status](https://api.travis-ci.com/daniel-zahariev/settle-sdk-php.svg?branch=main)](https://app.travis-ci.com/github/daniel-zahariev/settle-sdk-php)
[![Coverage Status](https://coveralls.io/repos/github/daniel-zahariev/settle-sdk-php/badge.svg?branch=main)](https://coveralls.io/github/daniel-zahariev/settle-sdk-php?branch=main)

# PHP SDK for connecting to the Settle Payment Platform

**Start accepting payments via Settle in seconds** ✨

An easy to use **SDK** for **PHP** with all the best practices to kickstart your integration with the **Settle Payment Platform**.

## Installation

`composer require danielz/settle-sdk-php`

## Usage

The library provides a basic Client class that handles the communication with the Settel REST API:  

```php
$settle_client = new SettleApiClient(
    SETTLE_MERCHANT_ID,
    SETTLE_USER_ID,
    SETTLE_PUBLIC_KEY,
    SETTLE_PRIVATE_KEY,
    SETTLE_IN_SANDBOX
);
```

The library then provides an entry point class for each section in the [REST API](https://api.support.settle.eu/api/reference/rest/v1/).
Currently, only MerchantsApi is available as Permissions and OAuth2 are pending breaking changes and will be added soon.


### Merchants API
The following class serves as an entry point for all the resources in the section:  
```php
$merchant_api = new MerchantApi($settle_client);
```
Each resource (class) can be accessed via magic property on the Merchants API object:

```php
$merchant_api->api_keys->...;
$merchant_api->balance->...;
$merchant_api->dynamic_links->...;
$merchant_api->payment_requests->...;
$merchant_api->payment_sends->...;
$merchant_api->pos->...;
$merchant_api->profile->...;
$merchant_api->settlements->...;
$merchant_api->short_links->...;
$merchant_api->status_codes->...;
```

The methods that each class implements very closely matches the REST API specification:

```php
$merchant_api->api_keys->list();
$merchant_api->api_keys->get($api_key_id);
$merchant_api->api_keys->create($api_key_data);
$merchant_api->api_keys->update($api_key_id, $api_key_data);
$merchant_api->api_keys->delete($api_key_id);
```

Only `PaymentRequests` class has a few extra helper methods:

```php
$merchant_api->payment_requests->capture($payment_request_id,$currency,$amount)
$merchant_api->payment_requests->refund($payment_request_id, $currency, $amount);
$merchant_api->payment_requests->getPaymentLink($payment_request_id);
$merchant_api->payment_requests->getMobilePaymentLink($payment_request_id);
```







### Webhooks / Callbacks
In order to validate callback requests from Settle, both Apache and nginx servers require manual setup to pass the `Authorization` header to PHP. 

Here's an example for Apache:

```
RewriteEngine On
RewriteRule .* - [e=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```

Here's an example for nginx:
```
proxy_set_header Authorization $http_authorization;
proxy_pass_header  Authorization;
```
