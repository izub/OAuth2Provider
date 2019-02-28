<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\GrantType\RefreshTokenConfigurations;
use OAuth2Provider\Service\Factory\GrantTypeStrategy\RefreshTokenFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * RefreshTokenFactory test case.
 */
class RefreshTokenFactoryTest extends \PHPUnit\Framework\TestCase
{

    /**
     *
     * @var RefreshTokenFactory
     */
    private $RefreshTokenFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->RefreshTokenFactory = new RefreshTokenFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->RefreshTokenFactory = null;

        parent::tearDown();
    }

    /**
     * Tests RefreshTokenFactory->__invoke()
     */
    public function test__invoke()
    {
        $mainSm = new ServiceManager();

        $storageCont = new StorageContainer();
        $options = array(
            'refresh_token_storage' => new \OAuth2ProviderTests\Assets\Storage\RefreshTokenStorage(),
        );
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/GrantType/RefreshToken', new RefreshTokenConfigurations());

        $s = $this->RefreshTokenFactory->__invoke($mainSm, '');
        $r = $s('OAuth2\GrantType\RefreshToken', $options, 'server3');
        $this->assertInstanceOf('OAuth2\GrantType\GrantTypeInterface', $r);
    }

    /**
     * Tests RefreshTokenFactory->__invoke()
     * @expectedException \OAuth2Provider\Exception\InvalidServerException
     */
    public function testCreateServiceReturnsException()
    {
        $mainSm = new ServiceManager();
        $storageCont = new StorageContainer();
        $options = array(
            'refresh_token_storage' => 'nothing',
        );
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageCont);
        $mainSm->setService('OAuth2Provider/Options/GrantType/RefreshToken', new RefreshTokenConfigurations());

        $s = $this->RefreshTokenFactory->__invoke($mainSm, '');
        $s('OAuth2\GrantType\RefreshToken', $options, 'server3');
    }
}

