<?php
namespace TYPO3\FalWebdav\Backend;

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
 * TCEmain integration for the WebDAV driver.
 *
 * @author Andreas Wolf <dev@a-w.io>
 */
class TceMainHook
{
    /**
     * @param array $incomingFieldArray
     * @param string $table
     * @param integer|string $id
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $tceMainObject
     * @return mixed
     */
    public function processDatamap_preProcessFieldArray(
        &$incomingFieldArray,
        $table,
        $id,
        \TYPO3\CMS\Core\DataHandling\DataHandler $tceMainObject
    ) {
        if ($table !== 'sys_file_storage') {
            return;
        }
        if ($incomingFieldArray['driver'] !== 'WebDav') {
            return;
        }

        $url = &$incomingFieldArray['configuration']['data']['sDEF']['lDEF']['baseUrl']['vDEF'];
        $username = &$incomingFieldArray['configuration']['data']['sDEF']['lDEF']['username']['vDEF'];
        $password = &$incomingFieldArray['configuration']['data']['sDEF']['lDEF']['password']['vDEF'];

        list($cleanedUrl, $extractedUsername, $extractedPassword) = \TYPO3\FalWebdav\Utility\UrlTools::extractUsernameAndPasswordFromUrl($url);
        if ($cleanedUrl != $url) {
            $url = $cleanedUrl;
        }
        // if we found authentication information in the URL, use it instead of the information currently stored
        if ($extractedUsername !== '') {
            $username = $extractedUsername;
            $password = $extractedPassword;
        }

        // skip encryption if we have no password set or the password is already encrypted
        if ($password === '' || substr($password, 0, 1) === '$') {
            return;
        }

        $password = \TYPO3\FalWebdav\Utility\EncryptionUtility::encryptPassword($password);
    }

}
