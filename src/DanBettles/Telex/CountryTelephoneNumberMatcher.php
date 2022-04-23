<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace DanBettles\Telex;

/**
 * Matches telephone numbers using a country numbering plan.
 *
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */
class CountryTelephoneNumberMatcher
{
    /**
     * @var CountryNumberingPlan
     */
    private $countryNumberingPlan;

    /**
     * @var string[]
     */
    private $intlCallPrefixes;

    /**
     * @var string
     */
    private $intlRegExp;

    /**
     * @var string
     */
    private $nationalRegExp;

    public function __construct(CountryNumberingPlan $countryNumberingPlan, array $intlCallPrefixes)
    {
        $this
            ->setCountryNumberingPlan($countryNumberingPlan)
            ->setIntlCallPrefixes($intlCallPrefixes)
            ->setIntlRegExp($this->createIntlRegExp())
            ->setNationalRegExp($this->createNationalRegExp())
        ;
    }

    /**
     * @param CountryNumberingPlan $plan
     * @return CountryTelephoneNumberMatcher $this
     */
    private function setCountryNumberingPlan(CountryNumberingPlan $plan)
    {
        $this->countryNumberingPlan = $plan;

        return $this;
    }

    /**
     * @return CountryNumberingPlan
     */
    public function getCountryNumberingPlan()
    {
        return $this->countryNumberingPlan;
    }

    private function setIntlCallPrefixes(array $prefixes)
    {
        $this->intlCallPrefixes = $prefixes;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getIntlCallPrefixes()
    {
        return $this->intlCallPrefixes;
    }

    private function createAltsSubpattern(array $alts, $capturing = false)
    {
        $altsList = implode('|', $alts);

        return '(' . ($capturing ? '' : '?:') . "{$altsList})";
    }

    private function createTrunkPrefixSubpattern()
    {
        $countryNumberingPlan = $this->getCountryNumberingPlan();

        if (!$countryNumberingPlan->hasTrunkPrefix()) {
            return '';
        }

        return $this->createAltsSubpattern($countryNumberingPlan->getTrunkPrefixes());
    }

    private function createNsnSubpattern()
    {
        $nsnLengths = $this->getCountryNumberingPlan()->getNsnLengths();

        //Sort the NSN lengths in reverse order so that longer numbers are matched before shorter ones - this *may* help
        //prevent us matching only parts of longer numbers.
        rsort($nsnLengths);

        return $this->createAltsSubpattern(array_map(function ($nsnLength) {
            return '\d{' . $nsnLength . '}';
        }, $nsnLengths));
    }

    private function createIntlRegExp()
    {
        $trunkPrefixSubpattern = $this->createTrunkPrefixSubpattern();

        return (
            '/^' .
            $this->createAltsSubpattern($this->getIntlCallPrefixes()) . '?' .
            $this->getCountryNumberingPlan()->getCountryCallingCode() .
            ($trunkPrefixSubpattern ? "{$trunkPrefixSubpattern}?" : '') .
            $this->createNsnSubpattern() .
            '/'
        );
    }

    private function setIntlRegExp($regExp)
    {
        $this->intlRegExp = $regExp;

        return $this;
    }

    /**
     * @return string
     */
    private function getIntlRegExp()
    {
        return $this->intlRegExp;
    }

    private function createNationalRegExp()
    {
        return '/^' . $this->createTrunkPrefixSubpattern() . $this->createNsnSubpattern() . '/';
    }

    private function setNationalRegExp($regExp)
    {
        $this->nationalRegExp = $regExp;

        return $this;
    }

    /**
     * @return string
     */
    private function getNationalRegExp()
    {
        return $this->nationalRegExp;
    }

    /**
     * Returns a `TelephoneNumberMatch` object if the specified candidate appears to contain an international telephone number, or
     * FALSE otherwise.
     *
     * @param Candidate $candidate
     * @return TelephoneNumberMatch|bool
     */
    public function matchIntl(Candidate $candidate)
    {
        if ($candidate->getNumberLength() < $this->getCountryNumberingPlan()->getMinIntlLength()) {
            return false;
        }

        $matchParts = [];
        $numMatches = preg_match($this->getIntlRegExp(), $candidate->getNumber(), $matchParts);

        return $numMatches
            ? new TelephoneNumberMatch($candidate, $matchParts[0])
            : false;
    }

    /**
     * Returns a `TelephoneNumberMatch` object if the specified candidate appears to contain a national telephone number, or FALSE
     * otherwise.
     *
     * @param Candidate $candidate
     * @return TelephoneNumberMatch|bool
     */
    public function matchNational(Candidate $candidate)
    {
        if ($candidate->getNumberLength() < $this->getCountryNumberingPlan()->getMinNationalLength()) {
            return false;
        }

        $matchParts = [];
        $numMatches = preg_match($this->getNationalRegExp(), $candidate->getNumber(), $matchParts);

        return $numMatches
            ? new TelephoneNumberMatch($candidate, $matchParts[0])
            : false;
    }

    /**
     * Returns a `TelephoneNumberMatch` object if the specified candidate appears to contain any kind of telephone number, or FALSE
     * otherwise.
     *
     * @param Candidate $candidate
     * @return TelephoneNumberMatch|bool
     */
    public function matchAny(Candidate $candidate)
    {
        return $this->matchIntl($candidate)
            ?: $this->matchNational($candidate);
    }
}
