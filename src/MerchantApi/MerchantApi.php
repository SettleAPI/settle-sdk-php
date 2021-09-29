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
 * @property SettlementAccounts settlement_accounts
 * @property SalesSummary sales_summary
 * @property Shortlinks shortlinks
 * @property Balance balance
 * @property Logo logo
 * @property Profile profile
 * @property StatusCodes status_codes
 */
class MerchantApi extends SettleApi
{
    /**
     * @return string[]
     */
    protected function getMagicProperties(): array
    {
        return [
            'api_keys' => ApiKeys::class,
            'payment_requests' => PaymentRequests::class,
            'payment_sends' => PaymentSends::class,
            'pos' => Pos::class,
            'settlements' => Settlements::class,
            'settlement_accounts' => SettlementAccounts::class,
            'sales_summary' => SalesSummary::class,
            'shortlinks' => Shortlinks::class,
            'balance' => Balance::class,
            'logo' => Logo::class,
            'profile' => Profile::class,
            'status_codes' => StatusCodes::class,
        ];
    }
}
