<?php

namespace ZapsterStudios\TeamPay\Configuration;

trait LinkConfiguration
{
    /**
     * The password reset url.
     *
     * @var string
     */
    public static $linkPasswordReset = '/login/reset/{token}';

    /**
     * Create default links.
     *
     * @returns void
     */
    public static function linkSetup()
    {
        static::$linkPasswordReset = env('APP_URL').static::$linkPasswordReset;
    }
}
