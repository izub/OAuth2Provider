<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Options\TokenType\BearerConfigurations;
use OAuth2Provider\Service\Factory\TokenTypeStrategy\BearerFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * BearerFactory test case.
 */
class BearerFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BearerFactory
     */
    private $BearerFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->BearerFactory = new BearerFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->BearerFactory = null;
        parent::tearDown();
    }

    /**
     * Tests BearerFactory->__invoke()
     */
    public function test__invoke()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());

        $config = array(
            'configs' => array(
                'token_param_name' => 'test',
            ),
        );

        $r = $this->BearerFactory->__invoke($mainSm, '');
        $r = $r('OAuth2\TokenType\Bearer', $config, 'server2');
        $this->assertInstanceOf('OAuth2\TokenType\TokenTypeInterface', $r);
    }

    /**
     * Tests BearerFactory->__invoke()
     * @expectedException \OAuth2Provider\Exception\InvalidServerException
     */
    public function testCreateServiceReturnsException()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());

        $config = array(
            'configs' => array(
                'Xtoken_param_nameX' => 'test',
            ),
        );

        $r = $this->BearerFactory->__invoke($mainSm, '');
        $r('OAuth2\TokenType\Bearer', $config, 'server2');
    }
}

