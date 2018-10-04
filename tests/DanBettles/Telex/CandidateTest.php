<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex;

use DanBettles\Telex\Candidate;
use PHPUnit\Framework\TestCase;

class CandidateTest extends TestCase
{
    public function testIsInstantiable()
    {
        $candidate = new Candidate('+44 (0)1243 123456');

        $this->assertSame('+44 (0)1243 123456', $candidate->getSource());
    }

    public function testGetsourceReturnsTheSourceSetUsingSetsource()
    {
        $candidate = new Candidate('');
        $something = $candidate->setSource('foo');

        $this->assertSame('foo', $candidate->getSource());
        $this->assertInstanceOf('DanBettles\Telex\Candidate', $something);
    }

    public function testGetnumberReturnsTheNumberInTheCandidate()
    {
        $candidate = new Candidate('+44 (0)1243 123456');

        $this->assertSame('4401243123456', $candidate->getNumber());
    }

    public static function providesCandidatesContainingNumbers()
    {
        return [[
            0,
            new Candidate('+'),
        ], [
            0,
            new Candidate(''),
        ], [
            0,
            new Candidate('foo'),
        ], [
            0,
            new Candidate(' '),
        ], [
            3,
            new Candidate('123'),
        ], [
            13,
            new Candidate('+44 (0)1243 123456'),
        ]];
    }

    /**
     * @dataProvider providesCandidatesContainingNumbers
     */
    public function testGetnumberlengthReturnsTheLengthOfTheNumberInTheCandidate($expected, $candidate)
    {
        $this->assertSame($expected, $candidate->getNumberLength());
    }
}
