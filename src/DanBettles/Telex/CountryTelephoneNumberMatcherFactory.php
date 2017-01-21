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
class CountryTelephoneNumberMatcherFactory
{
    /**
     * See Wikipedia pages entitled "Telephone numbers in <country name>" and http://www.wtng.info/.
     *
     * @var array[]
     */
    private $countryNumberingPlans = [
        'ae' => [
            'nsn_lengths' => [9],
            'country_calling_code' => '971',
            'trunk_prefixes' => ['0'],
        ],
        'au' => [
            'nsn_lengths' => [9],
            'country_calling_code' => '61',
            'trunk_prefixes' => ['0'],
        ],
        'be' => [
            'nsn_lengths' => [8, 9],
            'country_calling_code' => '32',
            'trunk_prefixes' => ['0'],
        ],
        'ch' => [
            'nsn_lengths' => [7, 8, 9],
            'country_calling_code' => '41',
            'trunk_prefixes' => ['0'],
        ],
        'de' => [
            'nsn_lengths' => [5, 6, 7, 8, 9, 10, 11],
            'country_calling_code' => '49',
            'trunk_prefixes' => ['0'],
        ],
        'dk' => [
            'nsn_lengths' => [8],
            'country_calling_code' => '45',
            'trunk_prefixes' => [],
        ],
        'es' => [
            'nsn_lengths' => [9],
            'country_calling_code' => '34',
            'trunk_prefixes' => [],
        ],
        'fr' => [
            'nsn_lengths' => [9],
            'country_calling_code' => '33',
            'trunk_prefixes' => ['0'],
        ],
        'gb' => [
            'nsn_lengths' => [7, 9, 10],
            'country_calling_code' => '44',
            'trunk_prefixes' => ['0'],
        ],
        'it' => [
            'nsn_lengths' => [6, 7, 8, 9, 10, 11],
            'country_calling_code' => '39',
            'trunk_prefixes' => [],
        ],
        'nl' => [
            'nsn_lengths' => [9],
            'country_calling_code' => '31',
            'trunk_prefixes' => ['0'],
        ],
        'pt' => [
            'nsn_lengths' => [9],
            'country_calling_code' => '351',
            'trunk_prefixes' => [],
        ],
        'ru' => [
            'nsn_lengths' => [10],
            'country_calling_code' => '7',
            'trunk_prefixes' => ['8'],
        ],
        'th' => [
            'nsn_lengths' => [8],
            'country_calling_code' => '66',
            'trunk_prefixes' => ['0'],
        ],
        'us' => [
            'nsn_lengths' => [10],  //3 + 3 + 4
            'country_calling_code' => '1',
            'trunk_prefixes' => ['1', '0'],
        ],
    ];

    /**
     * See http://en.wikipedia.org/wiki/List_of_international_call_prefixes.
     *
     * @var string[]
     */
    private $internationalCallPrefixes = [
        '00',
        '011',   //Any country following the North American numbering plan
        '0011',  //Australia
    ];

    /**
     * Creates a `CountryTelephoneNumberMatcher` for the country with the specified ISO 3166-1 alpha-2 country code.
     *
     * @param string $countryCode
     * @return CountryTelephoneNumberMatcher
     * @throws OutOfBoundsException If the country numbering plan for the country with the specified country code does
     * not exist.
     */
    public function createForCountryByCode($countryCode)
    {
        if (!array_key_exists($countryCode, $this->countryNumberingPlans)) {
            throw new \OutOfBoundsException(
                'The country numbering plan for the country with the specified country code does not exist.'
            );
        }

        $countryNumberingPlanRecord = $this->countryNumberingPlans[$countryCode];

        $countryNumberingPlan = new CountryNumberingPlan(
            $countryCode,
            $countryNumberingPlanRecord['nsn_lengths'],
            $countryNumberingPlanRecord['country_calling_code'],
            $countryNumberingPlanRecord['trunk_prefixes']
        );

        return new CountryTelephoneNumberMatcher($countryNumberingPlan, $this->internationalCallPrefixes);
    }

    /**
     * Returns an array containing a `CountryTelephoneNumberMatcher` for each of the countries we have a country
     * numbering plan for.
     *
     * @return CountryTelephoneNumberMatcher[]
     */
    public function createForAllCountries()
    {
        return array_map(function ($countryCode) {
            return $this->createForCountryByCode($countryCode);
        }, array_keys($this->countryNumberingPlans));
    }
}
