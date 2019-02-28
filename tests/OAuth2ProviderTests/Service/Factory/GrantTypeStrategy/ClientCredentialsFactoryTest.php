<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\GrantType\ClientCredentialsConfigurations;
use OAuth2Provider\Service\Factory\GrantTypeStrategy\ClientCredentialsFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * ClientCredentialsFactory test case.
 */
class ClientCredentialsFactoryTest extends \PHPUnit\Framework\TestCase
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
     * Tests ClientCredentialsFactory->__invoke()
     */
    public function test__invoke()
    {
        $mainSm = new ServiceManager();
        $storageCont = new StorageContainer();
        $options = array(
            'client_credentials_storage' => new \OAuth2ProviderTests\Assets\Storage\ClientCredentialsStorage(),
            'configs' => array(),
        );
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/GrantType/ClientCredentials', new ClientCredentialsConfigurations());


        $r = $this->ClientCredentialsFactory->__invoke($mainSm, '');
        $r = $r('OAuth2\GrantType\ClientCredentials', $options, 'server3');
        $this->assertInstanceOf('OAuth2\GrantType\ClientCredentials', $r);
    }

    /**
     * Tests ClientCredentialsFactory->__invoke()
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


        $r = $this->ClientCredentialsFactory->__invoke($mainSm, '');
        $r('OAuth2\GrantType\ClientCredentials', $options, 'server3');
    }
}

