<?php
namespace TYPO3\FalWebdav\Utility;

/**
 * Utility methods for encrypting/decrypting data. Currently only supports Blowfish encryption in CBC mode,
 *
 * @author Andreas Wolf <andreas.wolf@ikt-werk.de>
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
