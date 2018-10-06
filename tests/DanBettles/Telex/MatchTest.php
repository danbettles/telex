<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex;

use DanBettles\Telex\Match;
use DanBettles\Telex\Candidate;
use PHPUnit\Framework\TestCase;

class MatchTest extends TestCase
{
    public function testIsInstantiable()
    {
        $candidate = new Candidate('(01234) 567890');
        $match = new Match($candidate, '01234567890');

        $this->assertSame($candidate, $match->getCandidate());
        $this->assertInstanceOf('DanBettles\Telex\Candidate', $match->getCandidate());
        $this->assertSame('01234567890', $match->getMatch());
    }

    public static function providesPartials()
    {
        return [[
            true,
            new Match(new Candidate('(01234) 567890 (123)'), '01234567890'),
        ], [
            false,
            new Match(new Candidate('(01234) 567890'), '01234567890'),
        ]];
    }

    /**
     * @dataProvider providesPartials
     */
    public function testIspartialReturnsTrueIfOnlyPartOfTheNumberWasMatched($expected, Match $match)
    {
        $this->assertSame($expected, $match->isPartial());
    }
}
