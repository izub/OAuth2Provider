<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Options\Configuration;
use OAuth2Provider\Server;
use OAuth2Provider\Service\Factory\MainServerFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * MainServerFactory test case.
 */
class MainServerFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     * @var MainServerFactory
     */
    private $MainServerFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->MainServerFactory = new MainServerFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->MainServerFactory = null;
        parent::tearDown();
    }

    /**
     * Tests MainServerFactory->createService()
     */
    public function testCreateService()
    {
        $oauthconfig = array(
            'servers' => array(
                'default' => array(
                    'storages' => array(
                        'user_credentials' => new \OAuth2ProviderTests\Assets\StorageUserCredentials(),
                    ),
                    'grant_types' => array(
                        'user_credentials'
                    ),
                ),
            ),
        );

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/Configuration', new Configuration($oauthconfig));
        $mainSm->setService('oauth2provider.server.default', new Server());

        $r = $this->MainServerFactory->createService($mainSm);
        $this->assertInstanceOf('OAuth2Provider\Server', $r);
    }
}
