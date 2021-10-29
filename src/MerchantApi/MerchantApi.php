<?php

namespace Danielz\SettleApi\MerchantApi;

use Danielz\SettleApi\SettleApi;

/**
 * Class MerchantApi
 * @package Danielz\SettleApi\MerchantApi
 *
 * @property ApiKeys api_keys
 * @property PaymentRequests payment_requests
 * @property PaymentSends payment_sends
 * @property Pos pos
 * @property Settlements settlements
 * @property Shortlinks shortlinks
 * @property Balance balance
 * @property Profile profile
 * @property StatusCodes status_codes
 */
class MerchantApi extends SettleApi
{
    /**
     * @return string[]
     */
    protected function getMagicProperties()
    {
        return [
            'api_keys' => ApiKeys::class,
            'balance' => Balance::class,
            'payment_requests' => PaymentRequests::class,
            'payment_sends' => PaymentSends::class,
            'pos' => Pos::class,
            'profile' => Profile::class,
            'settlements' => Settlements::class,
            'shortlinks' => Shortlinks::class,
            'status_codes' => StatusCodes::class,
        ];
    }
}
