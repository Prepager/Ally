<?php

namespace ZapsterStudios\Ally;

class Ally
{
    use Configuration\TeamConfiguration;
    use Configuration\UserConfiguration;
    use Configuration\PlanConfiguration;
    use Configuration\LinkConfiguration;
    use Data\CountryList;
    use Data\ResponseList;

    /**
     * The current version number.
     *
     * @var int
     */
    public static $version = '1.0.0';

    /**
     * Setup configuration.
     *
     * @return void
     */
    public static function setup()
    {
        static::teamSetup();
        static::linkSetup();
    }
}
