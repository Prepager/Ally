<?php

namespace ZapsterStudios\Ally\Configuration;

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
     * The account verification url.
     *
     * @var string
     */
    public static $linkInvitations = '/account/teams';

    /**
     * Create default links.
     *
     * @returns void
     */
    public static function linkSetup()
    {
        static::$linkPasswordReset = env('APP_URL').static::$linkPasswordReset;
        static::$linkAccountVerification = env('APP_URL').static::$linkAccountVerification;
        static::$linkInvitations = env('APP_URL').static::$linkInvitations;
    }
}
