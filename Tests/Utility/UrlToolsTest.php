<?php
namespace TYPO3\FalWebdav\Tests\Utility;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Testcase for the url tools class
 *
 * @author Andreas Wolf <dev@a-w.io>
 */
class UrlToolsTest extends \Tx_Phpunit_TestCase
{

    public function urlDataProvider()
    {
        return array(
            'regular URL with username and password' => array(
                'http://someuser:somepass@localhost/test.php',
                array('http://localhost/test.php', 'someuser', 'somepass')
            ),
            'URL with just user' => array(
                'http://someuser@localhost/test.php',
                array('http://localhost/test.php', 'someuser', '')
            ),
            'HTTPS URL with username and password' => array(
                'https://someuser:somepass@localhost/test.php',
                array('https://localhost/test.php', 'someuser', 'somepass')
            ),
            'URL without authentication' => array(
                'http://localhost/test.php',
                array('http://localhost/test.php', '', '')
            )
        );
    }

    /**
     * @test
     * @dataProvider urlDataProvider
     */
    public function usernameAndPasswordAreProperlyExtractedFromUrl($url, $expectedOutput)
    {
        $output = \TYPO3\FalWebdav\Utility\UrlTools::extractUsernameAndPasswordFromUrl($url);

        $this->assertEquals($expectedOutput, $output);
    }
}
