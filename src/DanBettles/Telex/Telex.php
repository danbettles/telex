<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace DanBettles\Telex;

/**
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */
class Telex
{
    /**
     * @var NumberFinder
     */
    private $numberFinder;

    /**
     * @var CountryTelephoneNumberMatcherFactory
     */
    private $countryTelephoneNumberMatcherFactory;

    public function __construct(
        NumberFinder $numberFinder,
        CountryTelephoneNumberMatcherFactory $countryTelephoneNumberMatcherFactory
    ) {
        $this
            ->setNumberFinder($numberFinder)
            ->setCountryTelephoneNumberMatcherFactory($countryTelephoneNumberMatcherFactory)
        ;
    }

    private function setNumberFinder(NumberFinder $numberFinder)
    {
        $this->numberFinder = $numberFinder;

        return $this;
    }

    /**
     * @return NumberFinder
     */
    public function getNumberFinder()
    {
        return $this->numberFinder;
    }

    private function setCountryTelephoneNumberMatcherFactory(CountryTelephoneNumberMatcherFactory $factory)
    {
        $this->countryTelephoneNumberMatcherFactory = $factory;

        return $this;
    }

    /**
     * @return CountryTelephoneNumberMatcherFactory
     */
    public function getCountryTelephoneNumberMatcherFactory()
    {
        return $this->countryTelephoneNumberMatcherFactory;
    }

    /**
     * Returns an array containing a `Match` object for each substring in the specified text that appears to be a
     * telephone number.
     *
     * @param string $string
     * @return Match[]
     */
    public function findAll($string)
    {
        $candidates = $this->getNumberFinder()->find($string);
        $matchers = $this->getCountryTelephoneNumberMatcherFactory()->createForAllCountries();

        $matches = [];

        foreach ($candidates as $candidate) {
            foreach ($matchers as $matcher) {
                $match = $matcher->matchAny($candidate);

                if (false !== $match) {
                    $matches[] = $match;
                    break;
                }
            }
        }

        return $matches;
    }
}
