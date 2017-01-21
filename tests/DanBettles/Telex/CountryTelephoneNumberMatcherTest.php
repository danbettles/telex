<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\DanBettles\Telex\CountryTelephoneNumberMatcher;

use DanBettles\Telex\CountryTelephoneNumberMatcher;
use DanBettles\Telex\Candidate;
use DanBettles\Telex\CountryNumberingPlan;
use DanBettles\Telex\Match;

class Test extends \PHPUnit_Framework_TestCase
{
    private function createGbCountryNumberingPlan()
    {
        return new CountryNumberingPlan(
            'gb',
            [7, 9, 10],
            '44',
            ['0']
        );
    }

    private function createGbMatcher()
    {
        return new CountryTelephoneNumberMatcher($this->createGbCountryNumberingPlan(), ['00']);
    }

    public function testIsInstantiable()
    {
        $countryNumberingPlan = $this->createGbCountryNumberingPlan();
        $intlCallPrefixes = ['00'];
        $matcher = new CountryTelephoneNumberMatcher($countryNumberingPlan, $intlCallPrefixes);

        $this->assertEquals($countryNumberingPlan, $matcher->getCountryNumberingPlan());
        $this->assertEquals($intlCallPrefixes, $matcher->getIntlCallPrefixes());
    }

    public static function providesIntlCandidates()
    {
        $argsRecords = [];

        $candidate1 = new Candidate('+44 795 7230 995');

        $argsRecords[] = [
            new Match($candidate1, '447957230995'),
            $candidate1,
        ];

        $candidate2 = new Candidate('+44 (0)795 7230 995');

        $argsRecords[] = [
            new Match($candidate2, '4407957230995'),
            $candidate2,
        ];

        $candidate3 = new Candidate('0044 795 7230 995');

        $argsRecords[] = [
            new Match($candidate3, '00447957230995'),
            $candidate3,
        ];

        $candidate4 = new Candidate('0044 (0)795 7230 995');

        $argsRecords[] = [
            new Match($candidate4, '004407957230995'),
            $candidate4,
        ];

        $candidate5 = new Candidate('+44 795 7230 995.  0795 7230 996');

        $argsRecords[] = [
            new Match($candidate5, '447957230995'),
            $candidate5,
        ];

        return $argsRecords;
    }

    /**
     * @dataProvider providesIntlCandidates
     */
    public function testMatchintlReturnsAMatchObjectIfTheCandidateAppearsToContainAnInternationalTelephoneNumber(
        $expected,
        $candidate
    ) {
        $this->assertEquals($expected, $this->createGbMatcher()->matchIntl($candidate));
    }

    public static function providesFalseIntlCandidates()
    {
        return [[
            //The string is empty.
            new Candidate(''),
        ], [
            //The number is too short to be an international GB number.
            new Candidate('+44 795 723'),
        ], [
            //The number is long enough - and looks about right in places - but the numbers aren't right.
            new Candidate('1144 123 4567'),
        ], [
            //The number is French.
            new Candidate('+33 (0)4 50 74 98 58'),
        ]];
    }

    /**
     * @dataProvider providesFalseIntlCandidates
     */
    public function testMatchintlReturnsFalseIfTheCandidateDoesNotAppearToContainAnInternationalTelephoneNumber(
        $candidate
    ) {
        $this->assertFalse($this->createGbMatcher()->matchIntl($candidate));
    }

    public static function providesNationalCandidates()
    {
        $argsRecords = [];

        $candidate1 = new Candidate('01243 123456');

        $argsRecords[] = [
            new Match($candidate1, '01243123456'),
            $candidate1,
        ];

        $candidate2 = new Candidate('07952 123456');

        $argsRecords[] = [
            new Match($candidate2, '07952123456'),
            $candidate2,
        ];

        $candidate3 = new Candidate('(01243) 123456');

        $argsRecords[] = [
            new Match($candidate3, '01243123456'),
            $candidate3,
        ];

        return $argsRecords;
    }

    /**
     * @dataProvider providesNationalCandidates
     */
    public function testMatchnationalReturnsAMatchObjectIfTheCandidateAppearsToContainANationalTelephoneNumber(
        $expected,
        $candidate
    ) {
        $this->assertEquals($expected, $this->createGbMatcher()->matchNational($candidate));
    }

    public static function providesFalseNationalCandidates()
    {
        return [[
            //The string is empty.
            new Candidate(''),
        ], [
            //The number is too short.
            new Candidate('(01243) 12'),
        ]];
    }

    /**
     * @dataProvider providesFalseNationalCandidates
     */
    public function testMatchnationalReturnsFalseIfTheCandidateDoesNotAppearToContainANationalTelephoneNumber(
        $candidate
    ) {
        $this->assertFalse($this->createGbMatcher()->matchNational($candidate));
    }

    public function testBuildsRegularExpressionsCorrectly()
    {
        $planWithoutTrunkPrefix = new CountryNumberingPlan('dk', [8], '45', []);
        $matcher = new CountryTelephoneNumberMatcher($planWithoutTrunkPrefix, ['00']);

        $nonmatchingCandidate = new Candidate('+44 (0)0000 000 000');

        $this->assertFalse($matcher->matchIntl($nonmatchingCandidate));
    }

    public static function providesIntlAndNationalCandidates()
    {
        return array_merge(self::providesIntlCandidates(), self::providesNationalCandidates());
    }

    /**
     * @dataProvider providesIntlAndNationalCandidates
     */
    public function testMatchanyReturnsAMatchObjectIfTheCandidateAppearsToContainAnyKindOfTelephoneNumber(
        $expected,
        $candidate
    ) {
        $this->assertEquals($expected, $this->createGbMatcher()->matchAny($candidate));
    }

    public static function providesFalseIntlAndNationalCandidates()
    {
        return array_merge(self::providesFalseIntlCandidates(), self::providesFalseNationalCandidates());
    }

    /**
     * @dataProvider providesFalseIntlAndNationalCandidates
     */
    public function testMatchanyReturnsFalseIfTheCandidateDoesNotAppearToContainAnyKindOfTelephoneNumber(
        $candidate
    ) {
        $this->assertFalse($this->createGbMatcher()->matchAny($candidate));
    }
}
