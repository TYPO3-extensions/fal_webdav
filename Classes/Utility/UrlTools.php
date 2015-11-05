<?php
namespace TYPO3\FalWebdav\Utility;

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
 * Utility methods for encrypting/decrypting data. Currently only supports Blowfish encryption in CBC mode,
 *
 * @author Andreas Wolf <dev@a-w.io>
 */
class UrlTools
{
    /**
     * Helper method to extract the username and password from a url. Returns username, password and the url without
     * authentication info.
     *
     * @param string
     * @return array An array with the cleaned url, the username and the password as entries
     */
    public static function extractUsernameAndPasswordFromUrl($url)
    {
        $urlInfo = parse_url($url);

        $user = $pass = '';
        if (isset($urlInfo['user'])) {
            $user = $urlInfo['user'];
            unset($urlInfo['user']);
        }
        if (isset($urlInfo['pass'])) {
            $pass = $urlInfo['pass'];
            unset($urlInfo['pass']);
        }

        return array(\TYPO3\CMS\Core\Utility\HttpUtility::buildUrl($urlInfo), $user, $pass);
    }
}
