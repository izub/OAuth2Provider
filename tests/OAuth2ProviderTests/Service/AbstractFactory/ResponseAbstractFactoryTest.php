<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\ResponseContainer;
use OAuth2Provider\Options\Configuration;
use OAuth2Provider\Service\AbstractFactory\ResponseAbstractFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * ResponseAbstractFactory test case.
 */
class ResponseAbstractFactoryTest extends \PHPUnit\Framework\TestCase
{

    /**
     *
     * @var ResponseAbstractFactory
     */
    private $ResponseAbstractFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ResponseAbstractFactory = new ResponseAbstractFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->ResponseAbstractFactory = null;

        parent::tearDown();
    }

    /**
     * Tests ResponseAbstractFactory->canCreate()
     */
    public function testCanCreateServiceWithNameReturnsFalseOnMalformedRname()
    {
        $sm = new ServiceManager();
        $r = $this->ResponseAbstractFactory->canCreate($sm, 'ouath2provide.xxx');
        $this->assertFalse($r);
    }

    /**
     * Tests ResponseAbstractFactory->canCreate()
     *
     */
    public function testCanCreateServiceWithNameUsingMainAndExistingSMInstance()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration());
        $sm->setService("oauth2provider.server.{$serverKey}", new \stdClass());
        $sm->get('OAuth2Provider/Options/Configuration')->setMainServer($serverKey);

        $r = $this->ResponseAbstractFactory->canCreate($sm, "oauth2provider.server.{$serverKey}.response");
        $this->assertTrue($r);
    }

    /**
     * Tests ResponseAbstractFactory->canCreate()
     * @expectedException \OAuth2Provider\Exception\ErrorException
     */
    public function testCanCreateServiceWithNameReturnsException()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration());
        $sm->get('OAuth2Provider/Options/Configuration')->setMainServer($serverKey);

        $r = $this->ResponseAbstractFactory->canCreate($sm, "oauth2provider.server.{$serverKey}.response");
    }

    /**
     * Tests ResponseAbstractFactory->canCreate()
     */
    public function testCanCreateServiceReturnsFalseOnMalformedInvalidRequestName()
    {
        $sm = new ServiceManager();
        $serverKey = "*&*";

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration());
        $sm->get('OAuth2Provider/Options/Configuration')->setMainServer($serverKey);

        $r = $this->ResponseAbstractFactory->canCreate($sm, "oauth2provider.server.{$serverKey}.response");
        $this->assertFalse($r);
    }

    /**
     * Tests ResponseAbstractFactory->createServiceWithName()
     */
    public function testCreateServiceWithName()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService("oauth2provider.server.{$serverKey}", new \stdClass());
        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration());
        $sm->setService('OAuth2Provider/Containers/ResponseContainer', new ResponseContainer());
        $sm->get('OAuth2Provider/Options/Configuration')->setMainServer($serverKey);

        // execute
        $this->ResponseAbstractFactory->canCreate($sm, "oauth2provider.server.{$serverKey}.response");

        $r = $this->ResponseAbstractFactory->__invoke($sm, "oauth2provider.server.{$serverKey}.response");
        $this->assertInstanceOf('OAuth2\Response', $r);
    }
}

