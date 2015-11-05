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
 * Testcase for the WebDAV driver encryption tools
 *
 * @author Andreas Wolf <dev@a-w.io>
 * @package TYPO3
 * @subpackage fal_webdav
 */
class EncryptionTest extends \Tx_Phpunit_TestCase
{
    /**
     * @test
     */
    public function passwordsCanBeEncryptedAndDecrypted()
    {
        $password = uniqid();

        $encryptedPassword = \TYPO3\FalWebdav\Utility\Encryption::encryptPassword($password);
        $decryptedPassword = \TYPO3\FalWebdav\Utility\Encryption::decryptPassword($encryptedPassword);

        $this->assertEquals($password, $decryptedPassword);
    }

    /**
     * @test
     */
    public function encryptedPasswordContainsAlgorithm()
    {
        $password = uniqid();

        $encryptedPassword = \TYPO3\FalWebdav\Utility\Encryption::encryptPassword($password);

        $this->assertStringStartsWith(
            sprintf('$%s$%s$',
                \TYPO3\FalWebdav\Utility\Encryption::getEncryptionMethod(),
                \TYPO3\FalWebdav\Utility\Encryption::getEncryptionMode()
            ),
            $encryptedPassword
        );
    }

    /**
     * @test
     */
    public function encryptingEmptyStringReturnsEmptyString()
    {
        $encryptedPassword = \TYPO3\FalWebdav\Utility\Encryption::encryptPassword('');

        $this->assertEmpty($encryptedPassword);
    }

    /**
     * @test
     */
    public function decryptingEmptyStringReturnsEmptyString()
    {
        $this->assertEquals('', \TYPO3\FalWebdav\Utility\Encryption::decryptPassword(''));
    }
}