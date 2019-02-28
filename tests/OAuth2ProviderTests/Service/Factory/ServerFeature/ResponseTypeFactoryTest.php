<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\ResponseTypeContainer;
use OAuth2Provider\Service\Factory\ServerFeature\ResponseTypeFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * ResponseTypeFactory test case.
 */
class ResponseTypeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResponseTypeFactory
     */
    private $ResponseTypeFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ResponseTypeFactory = new ResponseTypeFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->ResponseTypeFactory = null;
        parent::tearDown();
    }


    /**
     * Tests ResponseTypeFactory->createService()
     */
    public function testCreateService()
    {
        $storage = new \OAuth2\ResponseType\AccessToken(new \OAuth2ProviderTests\Assets\Storage\AccessTokenStorage);
        $strategies = array(
            $storage,
        );

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/ResponseTypeContainer', new ResponseTypeContainer());
        $r = $this->ResponseTypeFactory->createService($mainSm);
        $this->assertSame(array('token' => $storage), $r($strategies, 'server1'));
    }
}

