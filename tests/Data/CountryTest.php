<?php

namespace ZapsterStudios\Ally\Tests\Data;

use Ally;
use ZapsterStudios\Ally\Tests\TestCase;

class CountryTest extends TestCase
{
    /**
     * @test
     * @group Data
     */
    public function canRetrieveCountries()
    {
        $this->assertSame(Ally::getCountryList()['DK'], 'Denmark');
        $this->assertRegexp('/AF,AX,AL,DZ,/', Ally::getCountryKeyString());
    }
}
