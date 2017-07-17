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
     * The account verification url.
     *
     * @var string
     */
    public static $linkAccountVerification = '/account/verify/{token}';

    /**
     * Create default links.
     *
     * @returns void
     */
    public static function linkSetup()
    {
        static::$linkPasswordReset = env('APP_URL').static::$linkPasswordReset;
        static::$linkAccountVerification = env('APP_URL').static::$linkAccountVerification;
    }
}
