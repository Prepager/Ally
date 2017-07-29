<?php

namespace ZapsterStudios\Ally\Tests;

use Braintree_Configuration;

abstract class BraintreeTestCase extends TestCase
{
    /**
     * Setup the test before running.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        $location = realpath(__DIR__.'/../..');

        if (file_exists($location.'/.env')) {
            $dotenv = new \Dotenv\Dotenv($location);
            $dotenv->load();
        }
    }

    /**
     * Setup the test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Braintree_Configuration::environment('sandbox');
        Braintree_Configuration::merchantId(env('BRAINTREE_MERCHANT_ID'));
        Braintree_Configuration::publicKey(env('BRAINTREE_PUBLIC_KEY'));
        Braintree_Configuration::privateKey(env('BRAINTREE_PRIVATE_KEY'));
    }
}
