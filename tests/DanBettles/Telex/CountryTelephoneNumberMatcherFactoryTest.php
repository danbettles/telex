<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex\CountryTelephoneNumberMatcherFactory;

use DanBettles\Telex\CountryTelephoneNumberMatcherFactory;

class Test extends \PHPUnit_Framework_TestCase
{
    public function testIsInstantiable()
    {
        new CountryTelephoneNumberMatcherFactory();
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

        $this->assertTrue(is_array($matchers));
        $this->assertNotEmpty($matchers);

        $matcher = end($matchers);

        $this->assertInstanceOf('DanBettles\Telex\CountryTelephoneNumberMatcher', $matcher);
    }
}
