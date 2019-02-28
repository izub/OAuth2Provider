<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\GrantType\ClientCredentialsConfigurations;
use OAuth2Provider\Options\GrantType\RefreshTokenConfigurations;
use OAuth2Provider\Service\Factory\GrantTypeStrategy\ClientCredentialsFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * ClientCredentialsFactory test case.
 */
class ClientCredentialsFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ClientCredentialsFactory
     */
    private $ClientCredentialsFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ClientCredentialsFactory = new ClientCredentialsFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->ClientCredentialsFactory = null;

        parent::tearDown();
    }

    /**
     * Tests ClientCredentialsFactory->createService()
     */
    public function testCreateService()
    {
        $mainSm = new ServiceManager();
        $storageCont = new StorageContainer();
        $options = array(
            'client_credentials_storage' => new \OAuth2ProviderTests\Assets\Storage\ClientCredentialsStorage(),
            'configs' => array(),
        );
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/GrantType/ClientCredentials', new ClientCredentialsConfigurations());


        $r = $this->ClientCredentialsFactory->createService($mainSm);
        $r = $r('OAuth2\GrantType\ClientCredentials', $options, 'server3');
        $this->assertInstanceOf('OAuth2\GrantType\ClientCredentials', $r);
    }

    /**
     * Tests ClientCredentialsFactory->createService()
     * @expectedException \OAuth2Provider\Exception\InvalidServerException
     */
    public function testCreateServiceReturnsException()
    {
        $mainSm = new ServiceManager();
        $storageCont = new StorageContainer();
        $options = array(
            'client_credentials_storage' => 'nothere',
        );
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/GrantType/ClientCredentials', new ClientCredentialsConfigurations());


        $r = $this->ClientCredentialsFactory->createService($mainSm);
        $r('OAuth2\GrantType\ClientCredentials', $options, 'server3');
    }
}

