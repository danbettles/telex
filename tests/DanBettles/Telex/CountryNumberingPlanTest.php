<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex;

use DanBettles\Telex\CountryNumberingPlan;
use PHPUnit\Framework\TestCase;

class CountryNumberingPlanTest extends TestCase
{
    public function testIsInstantiable()
    {
        $plan = new CountryNumberingPlan(
            'gb',
            [7, 9, 10],
            '44',
            ['0']
        );

        $this->assertSame('gb', $plan->getCountryCode());
        $this->assertEquals([7, 9, 10], $plan->getNsnLengths());
        $this->assertSame('44', $plan->getCountryCallingCode());
        $this->assertEquals(['0'], $plan->getTrunkPrefixes());
    }

    public static function providesMinIntlLengths()
    {
        return [[
            12,
            [
                'country_code' => 'gb',
                'nsn_lengths' => [10],
                'country_calling_code' => '44',
                'trunk_prefixes' => ['0'],
            ],
        ], [
            9,
            [
                'country_code' => 'gb',
                'nsn_lengths' => [7, 9, 10],
                'country_calling_code' => '44',
                'trunk_prefixes' => ['0'],
            ],
        ]];
    }

    /**
     * @dataProvider providesMinIntlLengths
     */
    public function testGetminintllengthReturnsTheMinimumLengthOfAnInternationalTelephoneNumber($expectedLength, $record)
    {
        $plan = new CountryNumberingPlan(
            $record['country_code'],
            $record['nsn_lengths'],
            $record['country_calling_code'],
            $record['trunk_prefixes']
        );

        $this->assertSame($expectedLength, $plan->getMinIntlLength());
    }

    public static function providesMinNationalLengths()
    {
        return [[
            11,
            [
                'country_code' => 'gb',
                'nsn_lengths' => [10],
                'country_calling_code' => '44',
                'trunk_prefixes' => ['0'],
            ],
        ], [
            8,
            [
                'country_code' => 'gb',
                'nsn_lengths' => [7, 9, 10],
                'country_calling_code' => '44',
                'trunk_prefixes' => ['0'],
            ],
        ], [
            8,
            [
                'country_code' => 'xx',
                'nsn_lengths' => [7, 9, 10],
                'country_calling_code' => '100',
                'trunk_prefixes' => ['0', '12'],
            ],
        ]];
    }

    /**
     * @dataProvider providesMinNationalLengths
     */
    public function testGetminnationallengthReturnsTheMinimumLengthOfANationalTelephoneNumber($expectedLength, $record)
    {
        $plan = new CountryNumberingPlan(
            $record['country_code'],
            $record['nsn_lengths'],
            $record['country_calling_code'],
            $record['trunk_prefixes']
        );

        $this->assertSame($expectedLength, $plan->getMinNationalLength());
    }

    public function testHastrunkprefixReturnsTrueIfTheNumberingPlanIncludesATrunkPrefix()
    {
        $planWithTrunkPrefix = new CountryNumberingPlan(
            'gb',
            [7, 9, 10],
            '44',
            ['0']
        );

        $this->assertTrue($planWithTrunkPrefix->hasTrunkPrefix());

        $planWithoutTrunkPrefix = new CountryNumberingPlan(
            'es',
            [9],
            '34',
            []
        );

        $this->assertFalse($planWithoutTrunkPrefix->hasTrunkPrefix());
    }
}
