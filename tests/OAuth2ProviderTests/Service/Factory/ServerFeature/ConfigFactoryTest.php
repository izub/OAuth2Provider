<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\ConfigContainer;
use OAuth2Provider\Service\Factory\ServerFeature\ConfigFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * ConfigFactory test case.
 */
class ConfigFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConfigFactory
     */
    private $ConfigFactory;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ConfigFactory = new ConfigFactory(/* parameters */);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->ConfigFactory = null;
        parent::tearDown();
    }

    /**
     * Tests ConfigFactory->__invoke()
     */
    public function test__invoke()
    {
        $mainSm = new ServiceManager();

        $mainSm->setService('OAuth2Provider/Containers/ConfigContainer', new ConfigContainer());

        // seed the container
        $configContainer = $mainSm->get('OAuth2Provider/Containers/ConfigContainer');
        $configContainer['server1'] = $expected = array('key1' => 'val1');

        $config = $this->ConfigFactory->__invoke($mainSm, '');
        $r = $config($expected, 'server1');

        $this->assertSame($expected, $r);
    }
}
