<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex;

use DanBettles\Telex\CountryTelephoneNumberMatcher;
use DanBettles\Telex\CountryTelephoneNumberMatcherFactory;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class CountryTelephoneNumberMatcherFactoryTest extends TestCase
{
    public function testIsInstantiable()
    {
        $factory = new CountryTelephoneNumberMatcherFactory();

        $this->assertInstanceOf(CountryTelephoneNumberMatcherFactory::class, $factory);
    }

    public function testCreateforcountrybycodeCreatesAMatcherForTheCountryWithTheSpecifiedCountryCode()
    {
        $factory = new CountryTelephoneNumberMatcherFactory();
        $matcher = $factory->createForCountryByCode('gb');

        $this->assertInstanceOf(CountryTelephoneNumberMatcher::class, $matcher);

        $countryNumberingPlan = $matcher->getCountryNumberingPlan();

        $this->assertEquals([7, 9, 10], $countryNumberingPlan->getNsnLengths());
        $this->assertSame('44', $countryNumberingPlan->getCountryCallingCode());
        $this->assertEquals(['0'], $countryNumberingPlan->getTrunkPrefixes());

        $this->assertEquals(['00', '011', '0011'], $matcher->getIntlCallPrefixes());
    }

    public function testCreateforcountrybycodeThrowsAnExceptionIfTheCountryNumberingPlanForTheCountryWithTheSpecifiedCountryCodeDoesNotExist()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('The country numbering plan for the country with the specified country code does not exist.');

        $factory = new CountryTelephoneNumberMatcherFactory();
        $factory->createForCountryByCode('xx');
    }

    public function testCreateforallReturnsAnArrayContainingAMatcherForEachOfTheCountriesWeHaveACountryNumberingPlanFor()
    {
        $factory = new CountryTelephoneNumberMatcherFactory();
        $matchers = $factory->createForAllCountries();

        $this->assertIsArray($matchers);
        $this->assertNotEmpty($matchers);

        $matcher = end($matchers);

        $this->assertInstanceOf(CountryTelephoneNumberMatcher::class, $matcher);
    }
}
