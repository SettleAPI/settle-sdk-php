<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

class PaymentRequests extends SettleApi
{
    public function list()
    {
        return $this->call('GET', 'payment_request/');
    }

    public function get($payment_request_id)
    {
        return $this->call('GET', "payment_request/{$payment_request_id}/");
    }

    public function create(array $data)
    {
        return $this->call('POST', 'payment_request/', $data);
    }

    public function update($payment_request_id, array $data)
    {
        return $this->call('PUT', "payment_request/{$payment_request_id}/", $data);
    }

    public function outcome($payment_request_id)
    {
        return $this->call('GET', "payment_request/{$payment_request_id}/outcome/");
    }
}
