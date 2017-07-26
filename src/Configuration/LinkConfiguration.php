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
     * @return void
     */
    public static function linkSetup()
    {
        static::$linkPasswordReset = config('app.url').static::$linkPasswordReset;
        static::$linkAccountVerification = config('app.url').static::$linkAccountVerification;
        static::$linkInvitations = config('app.url').static::$linkInvitations;
    }
}
