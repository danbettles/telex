<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return string
     */
    private function getFixturesDir()
    {
        $relativePath = str_replace(
            '\\',
            '/',
            preg_replace('|^' . preg_quote(__NAMESPACE__ . '\\') . '|', '', get_class($this))
        );

        return __DIR__ . '/' . $relativePath;
    }

    /**
     * @param string $basename
     * @return string
     */
    public function getFixtureFilename($basename)
    {
        return $this->getFixturesDir() . '/' . $basename;
    }
}
