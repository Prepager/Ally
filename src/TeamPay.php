<?php

namespace ZapsterStudios\TeamPay;

class TeamPay
{
    /**
     * Use configuration files.
     */
    use Configuration\TeamConfiguration;
    use Configuration\PlanConfiguration;
    use Configuration\UserConfiguration;

    /**
     * Use data repositories.
     */
    use Data\ResponseList;

    /**
     * The sql query log.
     *
     * @var array
     */
    public static $queryLog = [];
}
