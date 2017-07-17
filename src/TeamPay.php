<?php

namespace ZapsterStudios\TeamPay;

class TeamPay
{
    /*
     * Use configuration files.
     */
    use Configuration\TeamConfiguration;
    use Configuration\UserConfiguration;
    use Configuration\PlanConfiguration;
    use Configuration\LinkConfiguration;
    /*
     * Use data repositories.
     */
    use Data\ResponseList;

    /**
     * The sql query log.
     *
     * @var array
     */
    public static $queryLog = [];

    /**
     * Setup configuration.
     *
     * @returns void
     */
    public static function setup()
    {
        static::teamSetup();
        static::linkSetup();
    }
}
