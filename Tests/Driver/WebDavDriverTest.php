<?php
namespace TYPO3\FalWebdav\Tests\Driver;

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

use TYPO3\CMS\Core\Cache;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\FalWebdav\Dav\WebDavClient;


/**
 * Testcase for the WebDAV driver
 *
 * @author Andreas Wolf <dev@a-w.io>
 * @package TYPO3
 * @subpackage fal_webdav
 */
class WebDavDriverTest extends \TYPO3\CMS\Core\Tests\Unit\Resource\BaseTestCase
{

    /**
     * @var \TYPO3\FalWebdav\Driver\WebDavDriver
     */
    protected $fixture;

    /**
     * @var string
     */
    protected $baseUrl;

    public function setUp()
    {
        $this->baseUrl = 'http://example.org/webdav-root/';
    }

    protected function mockDavClient()
    {
        return $this->getMock('TYPO3\FalWebdav\Dav\WebDavClient', array(), array(), '', false);
    }

    protected function prepareFixture(WebDavClient $client = null, $storageUid = null)
    {
        if ($client === null) {
            $client = $this->mockDavClient();
        }
        if ($storageUid === null) {
            $storageUid = 1;
        }

        $this->fixture = new \TYPO3\FalWebdav\Driver\WebDavDriver(array('baseUrl' => $this->baseUrl));
        $this->fixture->setStorageUid($storageUid);
        $this->fixture->injectDirectoryListingCache($this->getMock('TYPO3\CMS\Core\Cache\Frontend\FrontendInterface'));
        $this->fixture->processConfiguration();
        $this->fixture->injectDavClient($client);
        $this->fixture->initialize();
    }

    /**
     * @test
     */
    public function createFileIssuesCorrectCommandOnServer()
    {
        /** @var $clientMock WebDavClient */
        $clientMock = $this->mockDavClient();
        $clientMock->expects($this->once())->method('request')->with($this->equalTo('PUT'),
            $this->stringEndsWith('/someFile'));
        $this->prepareFixture($clientMock);
        $mockedFolder = '/';

        $this->fixture->createFile('someFile', $mockedFolder);
    }

    /**
     * @test
     * @return \TYPO3\CMS\Core\Resource\Folder
     */
    public function createFolderIssuesCorrectCreateCommandOnServer()
    {
        /** @var $clientMock WebDavClient */
        $clientMock = $this->mockDavClient();
        $clientMock->expects($this->at(0))->method('request')->with($this->equalTo('MKCOL'),
            $this->stringEndsWith('/mainFolder/subFolder/'));
        $this->prepareFixture($clientMock);
        $mockedFolder = '/mainFolder/';

        return $this->fixture->createFolder('subFolder', $mockedFolder);
    }

    /**
     * @test
     * @depends createFolderIssuesCorrectCreateCommandOnServer
     * @param string $folderIdentifier
     */
    public function createFolderReturnsObjectWithCorrectIdentifier($folderIdentifier)
    {
        $this->assertEquals('/mainFolder/subFolder/', $folderIdentifier);
    }

    /**
     * @test
     */
    public function copyFileToTemporaryPathCreatesLocalCopyOfFile()
    {
        $fileContents = uniqid();

        /** @var $clientMock WebDavClient */
        $clientMock = $this->mockDavClient();
        $clientMock->expects($this->once())->method('request')->with($this->equalTo('GET'),
            $this->stringEndsWith('/mainFolder/file.txt'))
            ->will($this->returnValue(array('body' => $fileContents)));
        $this->prepareFixture($clientMock);
        $mockedFile = '/mainFolder/file.txt';

        $temporaryPath = $this->fixture->copyFileToTemporaryPath($mockedFile);
        $this->assertEquals($fileContents, file_get_contents($temporaryPath));

        unlink($temporaryPath);
    }

    /**
     * @test
     */
    public function moveFileWithinStorageIssuesCorrectCommand()
    {
        $mockedFile = '/someFile';
        $mockedFolder = '/targetFolder/';

        /** @var $clientMock WebDavClient */
        $clientMock = $this->mockDavClient();
        // TODO make the parameter matching here more special as soon as PHPUnit supports doing so
        $clientMock->expects($this->once())->method('request')->with($this->equalTo('MOVE'),
            $this->stringEndsWith('/someFile'),
            null, $this->logicalAnd($this->contains($this->baseUrl . 'targetFolder/movedFile'), $this->contains('T')));
        $this->prepareFixture($clientMock);

        $newFileIdentifier = $this->fixture->moveFileWithinStorage($mockedFile, $mockedFolder, 'movedFile');

        $this->assertEquals('/targetFolder/movedFile', $newFileIdentifier);
    }

    /**
     * @return array
     */
    public function isWithin_dataProvider()
    {
        return array(
            'file in folder' => array(
                '/someFolder',
                '/someFolder/file',
                true
            ),
            'file within subfolder' => array(
                '/someFolder',
                '/someFolder/someOtherFolder/file',
                true
            ),
            'file in root folder' => array(
                '/',
                '/file',
                true
            )
        );
    }

    /**
     * @test
     * @dataProvider isWithin_dataProvider
     */
    public function isWithinCorrectlyDetectsPaths($containerPath, $contentPath, $expectedResult)
    {
        $mockedFolder = $containerPath;
        $this->prepareFixture();

        $actualResult = $this->fixture->isWithin($mockedFolder, $contentPath);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function setFileContentsIssuesCorrectCommandOnServer()
    {
        $fileContents = uniqid();
        /** @var $clientMock WebDavClient */
        $clientMock = $this->mockDavClient();
        $clientMock->expects($this->once())->method('request')->with($this->equalTo('PUT'),
            $this->stringEndsWith('/someFile'), $fileContents);
        $this->prepareFixture($clientMock);
        $mockedFile = '/someFile';

        $this->fixture->setFileContents($mockedFile, $fileContents);
    }

    /**
     * @test
     */
    public function getPublicUrlReturnsCorrectUrlIfStorageIsPublic()
    {
        $mockedStorage = $this->getMock(ResourceStorage::class, array(), array(), '', false);
        $mockedStorage->expects($this->any())->method('isPublic')->will($this->returnValue(true));
        $this->prepareFixture();

        $this->fixture->getPublicUrl('/someFolder/someFile.jpg');
    }

    /**
     * @test
     */
    public function deleteFolderIssuesCorrectCommandOnServer()
    {
        /** @var $clientMock WebDavClient */
        $clientMock = $this->mockDavClient();
        $clientMock->expects($this->once())->method('request')->with($this->equalTo('DELETE'),
            $this->stringEndsWith('/someFolder/'));
        $this->prepareFixture($clientMock);

        $this->fixture->deleteFolder('/someFolder/');
    }

    /**
     * @test
     */
    public function timeoutOnRequestThrowsException()
    {
        $this->markTestSkipped('This test needs to be adjusted (timeout configuration, exception class)');
        $this->setExpectedException('Sabre_DAV_Exception_Timeout');

        // 192.0.2.0/24 is a network that should be used in documentation and for tests, but not in the wild;
        // see http://tools.ietf.org/html/rfc5737
        $client = new WebDavClient(array('baseUri' => 'http://192.0.2.1/', 'timeout' => 5));

        print_r($client->request('GET', '/something'));
    }
}
