<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace DanBettles\Telex;

/**
 * `CountryNumberingPlan` is simply a container for a country numbering plan: it has no significant behaviour.
 *
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */
class CountryNumberingPlan
{
    /**
     * @var string
     */
    private $countryCode;

    /**
     * An array containing the lengths of *N*ational *S*ignificant *N*umbers in the plan.
     *
     * In general, a NSN comprises an area code and a local number; it does not include a trunk prefix.
     *
     * @var int[]
     */
    private $nsnLengths;

    /**
     * @var string
     */
    private $countryCallingCode;

    /**
     * @var string[]
     */
    private $trunkPrefixes;

    /**
     * @var int
     */
    private $minIntlLength;

    /**
     * @var int
     */
    private $minNationalLength;

    /**
     * @param string $countryCode
     * @param int[] $nsnLengths
     * @param string $countryCallingCode
     * @param string[] $trunkPrefixes
     */
    public function __construct($countryCode, array $nsnLengths, $countryCallingCode, array $trunkPrefixes)
    {
        $this
            ->setCountryCode($countryCode)
            ->setNsnLengths($nsnLengths)
            ->setCountryCallingCode($countryCallingCode)
            ->setTrunkPrefixes($trunkPrefixes)
            ->populateMinIntlLength()
            ->populateMinNationalLength()
        ;
    }

    private function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    private function setNsnLengths(array $lengths)
    {
        $this->nsnLengths = $lengths;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getNsnLengths()
    {
        return $this->nsnLengths;
    }

    private function setCountryCallingCode($code)
    {
        $this->countryCallingCode = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCallingCode()
    {
        return $this->countryCallingCode;
    }

    private function setTrunkPrefixes(array $prefixes)
    {
        $this->trunkPrefixes = $prefixes;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTrunkPrefixes()
    {
        return $this->trunkPrefixes;
    }

    public function hasTrunkPrefix()
    {
        return [] != $this->getTrunkPrefixes();
    }

    private function populateMinIntlLength()
    {
        $this->minIntlLength = strlen($this->getCountryCallingCode()) + min($this->getNsnLengths());

        return $this;
    }

    /**
     * Returns the minimum length of a telephone number that can be dialled from outside of the country - minus
     * international call prefix.
     *
     * @return int
     */
    public function getMinIntlLength()
    {
        return $this->minIntlLength;
    }

    private function populateMinNationalLength()
    {
        $minTrunkPrefixLength = null;

        foreach ($this->getTrunkPrefixes() as $trunkPrefix) {
            $trunkPrefixLength = strlen($trunkPrefix);

            if ($trunkPrefixLength < $minTrunkPrefixLength || null === $minTrunkPrefixLength) {
                $minTrunkPrefixLength = $trunkPrefixLength;
            }
        }

        $this->minNationalLength = $minTrunkPrefixLength + min($this->getNsnLengths());

        return $this;
    }

    /**
     * Returns the minimum length of a telephone number that can be dialled anywhere within the country.
     *
     * @return int
     */
    public function getMinNationalLength()
    {
        return $this->minNationalLength;
    }
}
