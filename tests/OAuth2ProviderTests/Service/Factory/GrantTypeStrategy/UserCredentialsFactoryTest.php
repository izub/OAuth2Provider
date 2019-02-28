<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\GrantType\UserCredentialsConfigurations;
use OAuth2Provider\Service\Factory\GrantTypeStrategy\UserCredentialsFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * UserCredentialsFactory test case.
 */
class UserCredentialsFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
	 * @var UserCredentialsFactory
	 */
    private $UserCredentialsFactory;

    /**
	 * Prepares the environment before running a test.
	 */
    protected function setUp()
    {
        parent::setUp();
        $this->UserCredentialsFactory = new UserCredentialsFactory(/* parameters */);
    }

    /**
	 * Cleans up the environment after running a test.
	 */
    protected function tearDown()
    {
        $this->UserCredentialsFactory = null;
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
        $storageCont['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\Storage\UserCredentialsStorage();
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());

        $classname = 'OAuth2ProviderTests\Assets\GrantTypeCustomUserCredentials';
        $options = array('storage' => 'user_credentials');
        $r = $this->UserCredentialsFactory->__invoke($mainSm, '');
        $r = $r($classname, $options, 'server1');
        $this->assertInstanceOf('OAuth2\GrantType\GrantTypeInterface', $r);
    }

    /**
     * @expectedException \OAuth2Provider\Exception\InvalidServerException
     */
    public function testCreateServiceReturnsException()
    {
        $mainSm = new ServiceManager();

        // seed the storage
        $storageCont = new StorageContainer();
        $storageCont['server1']['user_credentials'] = '';
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());

        $classname = 'OAuth2ProviderTests\Assets\GrantTypeCustomUserCredentials';
        $options = array('storage' => 'user_credentials');
        $r = $this->UserCredentialsFactory->__invoke($mainSm, '');
        $r($classname, $options, 'server1');
    }
}

