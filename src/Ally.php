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
