<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex;

use DanBettles\Telex\NumberFinder;
use DanBettles\Telex\Candidate;

class NumberFinderTest extends \PHPUnit_Framework_TestCase
{
    public static function providesTelephoneNumbers()
    {
        return [[
            [new Candidate('+44 (0)1243 123456')],
            '+44 (0)1243 123456',
        ], [
            [new Candidate('123-45-67')],
            'Telephone number written in a Mexican format: 123-45-67.',
        ], [
            [new Candidate('+ (0')],
            '+ (0) -',
        ], [
            [],
            '+ () -',
        ], [
            [new Candidate('(01243) 123456')],
            'A UK landline number might look like (01243) 123456.',
        ], [
            [new Candidate('93.412.46.02'), new Candidate('917.741.056')],
            '93.412.46.02 is an old-style Spanish telephone number.  917.741.056 is a new-style Spanish telephone number.',
        ], [
            [new Candidate('+39.028321909')],
            '+39.028321909',
        ], [
            [],
            '£1,234.00',
        ], [
            [],
            '£ 1,234.00',
        ], [
            [],
            '1,234.00€',
        ], [
            [],
            '1,234.00 €',
        ], [
            [],
            '€1.234,00',
        ], [
            [],
            '€ 1.234,00',
        ], [
            [],
            '1.234,00€',
        ], [
            [],
            '1.234,00 €',
        ], [
            [],
            '€1 234,00',
        ], [
            [],
            '€ 1 234,00',
        ], [
            [],
            '1 234,00€',
        ], [
            [],
            '1 234,00 €',
        ], [
            [new Candidate('(01243) 123456')],
            'Tel no.: (01243) 123456.  Followed by something else.',
        ]];
    }

    /**
     * @dataProvider providesTelephoneNumbers
     */
    public function testFindReturnsDetailsOfStringsThatCouldBeTelephoneNumbers($expectedCandidates, $string)
    {
        $finder = new NumberFinder();
        $candidates = $finder->find($string);

        $this->assertEquals($expectedCandidates, $candidates);
    }
}
