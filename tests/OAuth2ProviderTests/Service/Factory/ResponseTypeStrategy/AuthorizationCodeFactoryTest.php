<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\ResponseType\AuthorizationCodeConfigurations;
use OAuth2Provider\Service\Factory\ResponseTypeStrategy\AuthorizationCodeFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * AuthorizationCodeFactory test case.
 */
class AuthorizationCodeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AuthorizationCodeFactory
     */
    private $AuthorizationCodeFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->AuthorizationCodeFactory = new AuthorizationCodeFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->AuthorizationCodeFactory = null;
        parent::tearDown();
    }

    /**
     * Tests UserCredentialsFactory->__invoke()
     */
    public function test__invoke()
    {
        $mainSm = new ServiceManager();

        // seed the storage
        $storageCont = new StorageContainer();
        $storageCont['server1']['authorization_code'] = new \OAuth2ProviderTests\Assets\Storage\AuthorizationCodeStorage();
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/ResponseType/AuthorizationCode', new AuthorizationCodeConfigurations());

        $classname = 'OAuth2\ResponseType\AuthorizationCode';
        $options = array('storage' => 'authorization_code');
        $r = $this->AuthorizationCodeFactory->__invoke($mainSm, '');
        $r = $r($classname, $options, 'server1');
        $this->assertInstanceOf('OAuth2\ResponseType\AuthorizationCodeInterface', $r);
    }

    /**
     * Tests UserCredentialsFactory->__invoke()
     * @expectedException \OAuth2Provider\Exception\InvalidServerException
     */
    public function testCreateServiceReturnsException()
    {
        $mainSm = new ServiceManager();

        // seed the storage
        $storageCont = new StorageContainer();
        $storageCont['server1']['authorization_code'] = null;
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/ResponseType/AuthorizationCode', new AuthorizationCodeConfigurations());

        $classname = 'OAuth2\ResponseType\AuthorizationCode';
        $options = array('storage' => 'authorization_code');
        $r = $this->AuthorizationCodeFactory->__invoke($mainSm, '');
        $r = $r($classname, $options, 'server1');
        $this->assertInstanceOf('OAuth2\ResponseType\AuthorizationCodeInterface', $r);
    }
}

