<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex\Telex;

use DanBettles\Telex\Telex;
use DanBettles\Telex\NumberFinder;
use DanBettles\Telex\CountryTelephoneNumberMatcherFactory;

class Test extends \PHPUnit_Framework_TestCase
{
//    private function createTelex()
//    {
//        return new Telex(new NumberFinder(), new CountryTelephoneNumberMatcherFactory());
//    }
//
    public function testIsInstantiable()
    {
        $numberFinder = new NumberFinder();
        $countryTelephoneNumberMatcherFactory = new CountryTelephoneNumberMatcherFactory();
        $telex = new Telex($numberFinder, $countryTelephoneNumberMatcherFactory);

        $this->assertSame($numberFinder, $telex->getNumberFinder());
        $this->assertSame($countryTelephoneNumberMatcherFactory, $telex->getCountryTelephoneNumberMatcherFactory());
    }

    public function testReadmeUsageSnippet()
    {
        $telex = new Telex(new NumberFinder(), new CountryTelephoneNumberMatcherFactory());
        $matches = $telex->findAll('A UK landline number: (01234) 567 890.  A UK mobile number: +44 (0)7123 456 789.');

        $this->assertTrue(is_array($matches));
        $this->assertCount(2, $matches);

        $landineMatch = $matches[0];

        $this->assertInstanceOf('DanBettles\Telex\Match', $landineMatch);
        $this->assertSame('(01234) 567 890.  ', $landineMatch->getCandidate()->getSource());
        $this->assertSame('01234567890', $landineMatch->getCandidate()->getNumber());

        $mobileMatch = $matches[1];

        $this->assertInstanceOf('DanBettles\Telex\Match', $mobileMatch);
        $this->assertSame('+44 (0)7123 456 789.', $mobileMatch->getCandidate()->getSource());
        $this->assertSame('4407123456789', $mobileMatch->getCandidate()->getNumber());
    }
}
