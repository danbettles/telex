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
class TelephoneNumberMatch
{
    /**
     * @var Candidate
     */
    private $candidate;

    /**
     * @var string
     */
    private $match;

    /**
     * @param Candidate $candidate
     * @param string $match
     */
    public function __construct(Candidate $candidate, $match)
    {
        $this->candidate = $candidate;
        $this->match = $match;
    }

    /**
     * @return Candidate
     */
    public function getCandidate()
    {
        return $this->candidate;
    }

    /**
     * @return string
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Returns TRUE if only part of the number was matched, or FALSE otherwise.
     *
     * @return bool
     */
    public function isPartial()
    {
        return $this->getCandidate()->getNumberLength() > strlen($this->getMatch());
    }
}
