<?php

namespace ZapsterStudios\TeamPay;

class TeamPay
{
    /*
     * Use configuration files.
     */
    use Configuration\TeamConfiguration;
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
}
