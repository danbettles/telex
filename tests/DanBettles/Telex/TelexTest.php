<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex;

use DanBettles\Telex\Telex;
use DanBettles\Telex\NumberFinder;
use DanBettles\Telex\CountryTelephoneNumberMatcherFactory;
use DanBettles\Telex\TelephoneNumberMatch;
use Tests\TestCase;

class TelexTest extends TestCase
{
    private function createTelex()
    {
        return new Telex(new NumberFinder(), new CountryTelephoneNumberMatcherFactory());
    }

    public function testIsInstantiable()
    {
        $numberFinder = new NumberFinder();
        $countryTelephoneNumberMatcherFactory = new CountryTelephoneNumberMatcherFactory();
        $telex = new Telex($numberFinder, $countryTelephoneNumberMatcherFactory);

        $this->assertInstanceOf(NumberFinder::class, $telex->getNumberFinder());
        $this->assertInstanceOf(CountryTelephoneNumberMatcherFactory::class, $telex->getCountryTelephoneNumberMatcherFactory());
    }

    public function testReadmeUsageSnippet()
    {
        $telex = new Telex(new NumberFinder(), new CountryTelephoneNumberMatcherFactory());
        $matches = $telex->findAll('A UK landline number: (01234) 567 890.  A UK mobile number: +44 (0)7123 456 789.');

        $this->assertIsArray($matches);
        $this->assertCount(2, $matches);

        $landineMatch = $matches[0];

        $this->assertInstanceOf(TelephoneNumberMatch::class, $landineMatch);
        $this->assertSame('(01234) 567 890', $landineMatch->getCandidate()->getSource());
        $this->assertSame('01234567890', $landineMatch->getCandidate()->getNumber());

        $mobileMatch = $matches[1];

        $this->assertInstanceOf(TelephoneNumberMatch::class, $mobileMatch);
        $this->assertSame('+44 (0)7123 456 789', $mobileMatch->getCandidate()->getSource());
        $this->assertSame('4407123456789', $mobileMatch->getCandidate()->getNumber());
    }

    public function providesTextContainingTelNums()
    {
        $file = fopen($this->getFixtureFilename('telephone_numbers.csv'), 'r');

        $argLists = [];

        while ($record = fgetcsv($file)) {
            $record[0] = (int) $record[0];
            $argLists[] = $record;
        }

        fclose($file);

        return $argLists;
    }

    /**
     * @dataProvider providesTextContainingTelNums
     * @group functional
     */
    public function testFindallFindsTheExpectedNumberOfTelephoneNumbersInText($expected, $input)
    {
        $matches = $this->createTelex()->findAll($input);

        $this->assertTrue(is_array($matches));
        $this->assertCount($expected, $matches);
    }

    public function providesTelNumsThatHighlightProblems()
    {
        return [[
            1,  //expected.  Actual = 2.
            '0039 02 876774',
        ], [
            1,  //expected.  Actual = 2.
            '0039 02 76006132',
        ]];
    }
}
