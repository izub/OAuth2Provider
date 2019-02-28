<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Service\Factory\ConfigurationFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * ConfigurationFactory test case.
 */
class ConfigurationFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConfigurationFactory
     */
    private $ConfigurationFactory;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        // TODO Auto-generated ConfigurationFactoryTestx::setUp()
        $this->ConfigurationFactory = new ConfigurationFactory(/* parameters */);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated ConfigurationFactoryTestx::tearDown()
        $this->ConfigurationFactory = null;
        parent::tearDown();
    }

    /**
     * Tests ConfigurationFactory->__invoke()
     */
    public function test__invoke()
    {
        $config = array(
            'oauth2provider' => array(
                'servers' => array('OauthServer'),
                'default_controller' => 'OauthController',
                'main_server' => 'client',
            ),
        );

        $mainSm = new ServiceManager();
        $mainSm->setService('Config', $config);

        $configOption = $this->ConfigurationFactory->__invoke($mainSm, '');
        $this->assertEquals(array('OauthServer'), $configOption->getServers());
        $this->assertEquals('OauthController', $configOption->getDefaultController());
        $this->assertEquals('client', $configOption->getMainServer());
    }

    /**
     * Tests ConfigurationFactory->__invoke()
     */
    public function testCreateServiceWithMainServerEmptyValueShouldUseDefault()
    {
        $config = array(
            'oauth2provider' => array(
                'main_server' => '',
            ),
        );

        $mainSm = new ServiceManager();
        $mainSm->setService('Config', $config);

        $configOption = $this->ConfigurationFactory->__invoke($mainSm, '');
        $this->assertEquals('default', $configOption->getMainServer());
    }

    /**
     * Tests ConfigurationFactory->__invoke()
     * @expectedException \OAuth2Provider\Exception\InvalidConfigException
     */
    public function testCreateServiceReturnsException()
    {
        $config = array(); //empty array

        $mainSm = new ServiceManager();
        $mainSm->setService('Config', $config);

        $configOption = $this->ConfigurationFactory->__invoke($mainSm, '');
    }
}

