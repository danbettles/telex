<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex;

use DanBettles\Telex\TelephoneNumberMatch;
use DanBettles\Telex\Candidate;
use PHPUnit\Framework\TestCase;

class TelephoneNumberMatchTest extends TestCase
{
    public function testIsInstantiable()
    {
        $candidate = new Candidate('(01234) 567890');
        $match = new TelephoneNumberMatch($candidate, '01234567890');

        $this->assertSame($candidate, $match->getCandidate());
        $this->assertInstanceOf(Candidate::class, $match->getCandidate());
        $this->assertSame('01234567890', $match->getMatch());
    }

    public function providesPartials()
    {
        return [[
            true,
            new TelephoneNumberMatch(new Candidate('(01234) 567890 (123)'), '01234567890'),
        ], [
            false,
            new TelephoneNumberMatch(new Candidate('(01234) 567890'), '01234567890'),
        ]];
    }

    /**
     * @dataProvider providesPartials
     */
    public function testIspartialReturnsTrueIfOnlyPartOfTheNumberWasMatched($expected, TelephoneNumberMatch $match)
    {
        $this->assertSame($expected, $match->isPartial());
    }
}
