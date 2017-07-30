<?php

namespace ZapsterStudios\Ally\Tests;

use Ally;
use Braintree_Configuration;

trait CashierTrait
{
    /**
     * Load the cashier settings.
     *
     * @return void
     */
    public function loadCashierSettings()
    {
        Braintree_Configuration::environment('sandbox');
        Braintree_Configuration::merchantId(env('BRAINTREE_MERCHANT_ID'));
        Braintree_Configuration::publicKey(env('BRAINTREE_PUBLIC_KEY'));
        Braintree_Configuration::privateKey(env('BRAINTREE_PRIVATE_KEY'));
    }

    /**
     * Load the cashier plans.
     *
     * @return void
     */
    public function loadCashierPlans()
    {
        Ally::$plans = [];
        Ally::addPlan('free-plan', 'Free Plan', 0);
        Ally::addPlan('valid-first-plan', 'Valid First Plan', 5);
        Ally::addPlan('valid-second-plan', 'Valid Second Plan', 10);
    }
}
