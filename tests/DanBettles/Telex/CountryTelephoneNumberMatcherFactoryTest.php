<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex;

use DanBettles\Telex\CountryTelephoneNumberMatcherFactory;
use PHPUnit\Framework\TestCase;

class CountryTelephoneNumberMatcherFactoryTest extends TestCase
{
    public function testIsInstantiable()
    {
        $factory = new CountryTelephoneNumberMatcherFactory();

        $this->assertInstanceOF('DanBettles\Telex\CountryTelephoneNumberMatcherFactory' , $factory);
    }

    public function testCreateforcountrybycodeCreatesAMatcherForTheCountryWithTheSpecifiedCountryCode()
    {
        $factory = new CountryTelephoneNumberMatcherFactory();
        $matcher = $factory->createForCountryByCode('gb');

        $this->assertInstanceOf('DanBettles\Telex\CountryTelephoneNumberMatcher', $matcher);

        $countryNumberingPlan = $matcher->getCountryNumberingPlan();

        $this->assertEquals([7, 9, 10], $countryNumberingPlan->getNsnLengths());
        $this->assertSame('44', $countryNumberingPlan->getCountryCallingCode());
        $this->assertEquals(['0'], $countryNumberingPlan->getTrunkPrefixes());

        $this->assertEquals(['00', '011', '0011'], $matcher->getIntlCallPrefixes());
    }

    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage The country numbering plan for the country with the specified country code does not exist.
     */
    public function testCreateforcountrybycodeThrowsAnExceptionIfTheCountryNumberingPlanForTheCountryWithTheSpecifiedCountryCodeDoesNotExist()
    {
        $factory = new CountryTelephoneNumberMatcherFactory();
        $factory->createForCountryByCode('xx');
    }

    public function testCreateforallReturnsAnArrayContainingAMatcherForEachOfTheCountriesWeHaveACountryNumberingPlanFor()
    {
        $factory = new CountryTelephoneNumberMatcherFactory();
        $matchers = $factory->createForAllCountries();

        $this->assertInternalType('array', $matchers);
        $this->assertNotEmpty($matchers);

        $matcher = end($matchers);

        $this->assertInstanceOf('DanBettles\Telex\CountryTelephoneNumberMatcher', $matcher);
    }
}
