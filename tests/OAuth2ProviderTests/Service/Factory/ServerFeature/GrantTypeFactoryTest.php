<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\GrantTypeContainer;
use OAuth2Provider\Service\Factory\ServerFeature\GrantTypeFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * GrantTypeFactory test case.
 */
class GrantTypeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GrantTypeFactory
     */
    private $GrantTypeFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->GrantTypeFactory = new GrantTypeFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->GrantTypeFactory = null;
        parent::tearDown();
    }

    /**
     * Tests GrantTypeFactory->createService()
     */
    public function testCreateService()
    {
        $storage = new \OAuth2\GrantType\UserCredentials(new \OAuth2ProviderTests\Assets\StorageUserCredentials());
        $strategies = array(
            $storage,
        );

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $r = $this->GrantTypeFactory->createService($mainSm);
        $this->assertSame(array('user_credentials' => $storage), $r($strategies, 'server1'));
    }
}

