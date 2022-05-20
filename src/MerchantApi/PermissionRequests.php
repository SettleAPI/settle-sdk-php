<?php

namespace SettleApi\MerchantApi;

use SettleApi\SettleApi;
use SettleApi\SettleApiException;

/**
 * Class PermissionRequests
 * @package SettleApi\MerchantApi
 * @link https://settleapi.stoplight.io/docs/settleapis/b3A6Mjk5NjUxNjA-merchant-permissions-request-get
 */
class PermissionRequests extends SettleApi
{
    /**
     * @param array $data
     * @return array
     * @throws SettleApiException
     */
    public function create(array $data)
    {
        return $this->call('POST', 'merchant/v1/permission_request/', $data, [
            'customer' => 'required|string',
            'scope' => 'required|string',
            'legal_terms_url' => 'required|string',
            'callback_uri' => 'string',
            'text' => 'string',
            'expires_in' => 'string|numeric',
            'success_return_uri' => 'string',
            'failure_return_uri' => 'string',
        ]);
    }

    /**
     * @param string $permissionRequestId
     * @return array
     * @throws SettleApiException
     */
    public function get($permissionRequestId)
    {
        return $this->call('GET', "merchant/v1/permission_request/{$permissionRequestId}/");
    }

    /**
     * @param string $permissionRequestId
     * @return array
     * @throws SettleApiException
     */
    public function outcome($permissionRequestId)
    {
        return $this->call('GET', "merchant/v1/permission_request/{$permissionRequestId}/outcome/");
    }
}