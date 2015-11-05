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

class StatusReport implements \TYPO3\CMS\Reports\StatusProviderInterface
{
    /**
     * Returns the status of an extension or (sub)system
     *
     * @return array An array of tx_reports_reports_status_Status objects
     */
    public function getStatus()
    {
        return array(
            'mcryptAvailability' => $this->getMcryptAvailability()
        );
    }

    protected function getMcryptAvailability()
    {
        $mcryptAvailable = \TYPO3\FalWebdav\Utility\EncryptionUtility::isMcryptAvailable();
        $severity = $mcryptAvailable === true ? \TYPO3\CMS\Reports\Status::OK : \TYPO3\CMS\Reports\Status::ERROR;
        $status = ($mcryptAvailable ? '' : 'Not ') . 'Available';

        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Reports\\Status',
            'PHP extension mcrypt', $status, '', $severity);
    }

}