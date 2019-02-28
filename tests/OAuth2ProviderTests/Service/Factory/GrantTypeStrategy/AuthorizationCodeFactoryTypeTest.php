<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\GrantType\AuthorizationCodeConfigurations;
use OAuth2Provider\Service\Factory\GrantTypeStrategy\AuthorizationCodeFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * AuthorizationCodeFactory test case.
 */
class AuthorizationCodeFactoryTypeTest extends \PHPUnit\Framework\TestCase
{

    /**
     *
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
     * Tests AuthorizationCodeFactory->createService()
     */
    public function testCreateService()
    {
        $mainSm = new ServiceManager();
        $storageCont = new StorageContainer();
        $options = array(
            'authorization_code_storage' => new \OAuth2ProviderTests\Assets\Storage\AuthorizationCodeStorage(),
        );
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/GrantType/AuthorizationCode', new AuthorizationCodeConfigurations());

        $r = $this->AuthorizationCodeFactory->createService($mainSm);
        $r = $r('OAuth2\GrantType\AuthorizationCode', $options, 'server4');
        $this->assertInstanceOf('OAuth2\GrantType\AuthorizationCode', $r);
    }

    /**
     * Tests AuthorizationCodeFactory->createService()
     * @expectedException \OAuth2Provider\Exception\InvalidServerException
     */
    public function testCreateServiceReturnsException()
    {
        $mainSm = new ServiceManager();

        $storageCont = new StorageContainer();
        $options = array(
            'authorization_code_storage' => 'xxXXxx',
        );
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/GrantType/AuthorizationCode', new AuthorizationCodeConfigurations());

        $r = $this->AuthorizationCodeFactory->createService($mainSm);
        $r = $r('OAuth2\GrantType\AuthorizationCode', $options, 'server4');
    }
}

