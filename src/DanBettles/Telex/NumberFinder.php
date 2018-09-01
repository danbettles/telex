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
    private static $separators = ' ()-+.'; // @todo Review this.

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
     * @param string $string
     *
     * @return string[]
     */
    private function findDatetimeStrings($string)
    {
        // The following regexps are adapted from `https://stackoverflow.com/questions/13194322/php-regex-to-check-date-is-in-yyyy-mm-dd-format`.
        $datePatterns = [
            // yyyy/mm/dd, yyyy-mm-dd, yyyy.mm.dd
            "((((19|[2-9]\d)\d{2})(\/|-|\.)(0[13578]|1[02])(\/|-|\.)(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})(\/|-|\.)(0[13456789]|1[012])(\/|-|\.)(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})(\/|-|\.)02(\/|-|\.)(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))(\/|-|\.)02(\/|-|\.)29))",

            // mm/dd/yyyy, mm-dd-yyyy, mm.dd.yyyy
            "(((0[13578]|1[02])(\/|-|\.)(0[1-9]|[12]\d|3[01])(\/|-|\.)((19|[2-9]\d)\d{2}))|((0[13456789]|1[012])(\/|-|\.)(0[1-9]|[12]\d|30)(\/|-|\.)((19|[2-9]\d)\d{2}))|(02(\/|-|\.)(0[1-9]|1\d|2[0-8])(\/|-|\.)((19|[2-9]\d)\d{2}))|(02(\/|-|\.)29(\/|-|\.)((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))",

            // dd/mm/yyyy, dd-mm-yyyy, dd.mm.yyyy
            "(((0[1-9]|[12]\d|3[01])(\/|-|\.)(0[13578]|1[02])(\/|-|\.)((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)(\/|-|\.)(0[13456789]|1[012])(\/|-|\.)((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])(\/|-|\.)02(\/|-|\.)((19|[2-9]\d)\d{2}))|(29(\/|-|\.)02(\/|-|\.)((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))",

            // mm/dd, mm-dd, mm.dd
            "(((0[13578]|1[02])(\/|-|\.)(0[1-9]|[12]\d|3[01]))|((0[13456789]|1[012])(\/|-|\.)(0[1-9]|[12]\d|30))|(02(\/|-|\.)(0[1-9]|1\d|2[0-9])))",

            // dd/mm, dd-mm, dd.mm
            "(((0[1-9]|[12]\d|3[01])(\/|-|\.)(0[13578]|1[02]))|((0[1-9]|[12]\d|30)(\/|-|\.)(0[13456789]|1[012]))|((0[1-9]|1\d|2[0-9])(\/|-|\.)02))",
        ];

        $finalDatePattern = implode('|', $datePatterns);

        // The following regexp is adapted from `https://stackoverflow.com/questions/11296536/regex-for-time-validation`.
        $finalTimePattern = "(([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?)";

        $matches = [];

        $numMatches = preg_match_all("/{$finalDatePattern}|{$finalTimePattern}/", $string, $matches);

        return $numMatches
            ? $matches[0]
            : []
        ;
    }

    /**
     * Removes numeric noise from the specified string to help us correctly identify telephone numbers in `find()`.
     *
     * @param string $string
     * @return string
     */
    private function filterString($string)
    {
        $filteredString = $string;

        $filteredString = str_replace($this->findDatetimeStrings($filteredString), '', $filteredString);
        $filteredString = str_replace($this->findMonetaryStrings($filteredString), '', $filteredString);

        return $filteredString;
    }

    /**
     * Returns an array containing the details of numbers found in the specified string.
     *
     * @param string $string
     * @return Candidate[]
     */
    public function find($string)
    {
        $filteredString = $this->filterString($string);

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

        $candidates = [];

        foreach ($candidateSources as $candidateSource) {
            //Remove non-numeric trailing characters - these are definitely not significant in any telephone number.
            $candidateSource = preg_replace('/\D+$/', '', $candidateSource);

            $candidate = new Candidate($candidateSource);

            if (0 === $candidate->getNumberLength()) {
                continue;
            }

            $candidates[] = $candidate;
        }

        //@todo Filter the candidates.

        return $candidates;
    }
}
