<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace DanBettles\Telex;

/**
 * The rough: finds strings of digits and symbols that *might* comprise telephone numbers.
 *
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 * @todo Rename this.
 */
class NumberFinder
{
    /**
     * @var string
     */
    private static $prefixes = '+(';

    /**
     * @var string
     */
    private static $separators = ' ()-.';

    /**
     * Finds sensibly-formatted monetary strings in the specified string.
     *
     * @param string $string
     * @return string[]
     * @todo Extract this.
     */
    private function findMonetaryStrings($string)
    {
        $moneyNumberPattern = '(\d{1,3})([,\. ]?\d{3})*([\.,]\d{2})?';

        $matches = [];

        $numMatches = preg_match_all(
            "/\p{Sc}\s*{$moneyNumberPattern}|{$moneyNumberPattern}\s*\p{Sc}/u",
            $string,
            $matches
        );

        return $numMatches
            ? $matches[0]
            : [];
    }

    /**
     * Returns an array containing the details of numbers found in the specified string.
     *
     * @param string $string
     * @return Candidate[]
     */
    public function find($string)
    {
        //Remove some numeric noise from the string.
        $filteredString = str_replace($this->findMonetaryStrings($string), '', $string);

        $numChars = strlen($filteredString);
        $collecting = false;
        $candidateNo = 0;

        $candidateSources = [];

        for ($charNo = 0; $charNo < $numChars; $charNo += 1) {
            $currChar = $filteredString[$charNo];
            $charIsDigit = is_numeric($currChar);

            if ($collecting) {
                if ($charIsDigit || false !== strpos(self::$separators, $currChar)) {
                    $candidateSources[$candidateNo] .= $currChar;
                } else {
                    $collecting = false;
                    $candidateNo += 1;
                }
            } else {
                if ($charIsDigit || false !== strpos(self::$prefixes, $currChar)) {
                    $collecting = true;
                    $candidateSources[$candidateNo] = $currChar;
                }
            }
        }

        $candidates = array_map(function ($candidateSource) {
            return new Candidate($candidateSource);
        }, $candidateSources);

        $candidatesContainingNumbers = array_filter($candidates, function (Candidate $candidate) {
            return 0 < $candidate->getNumberLength();
        });

        return $candidatesContainingNumbers;
    }
}
