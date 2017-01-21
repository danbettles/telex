<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace DanBettles\Telex;

/**
 * `Candidate` is a simple, but convenient, container for something that might be a telephone number.
 *
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */
class Candidate
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $number;

    /**
     * @var int
     */
    private $numberLength;

    /**
     * @param string $source
     */
    public function __construct($source)
    {
        $this->setSource($source);
    }

    /**
     * @param string $source
     * @return Candidate $this
     */
    public function setSource($source)
    {
        $this->source = $source;
        $this->number = preg_replace('/\D/', '', $this->source);
        $this->numberLength = strlen($this->number);

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Returns the length of the number.
     *
     * @return int
     */
    public function getNumberLength()
    {
        return $this->numberLength;
    }
}
