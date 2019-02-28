<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\RequestContainer;
use OAuth2Provider\Options\Configuration;
use OAuth2Provider\Service\AbstractFactory\RequestAbstractFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * RequestAbstractFactory test case.
 */
class RequestAbstractFactoryTest extends \PHPUnit\Framework\TestCase
{

    /**
     *
     * @var RequestAbstractFactory
     */
    private $RequestAbstractFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->RequestAbstractFactory = new RequestAbstractFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->RequestAbstractFactory = null;

        parent::tearDown();
    }

    /**
     * Tests RequestAbstractFactory->canCreateServiceWithName()
     */
    public function testCanCreateServiceWithNameReturnsFalseOnMalformedRname()
    {
        $sm = new ServiceManager();
        $r = $this->RequestAbstractFactory->canCreateServiceWithName($sm, '', 'ouath2provide.xxx');
        $this->assertFalse($r);
    }

    /**
     * Tests RequestAbstractFactory->canCreateServiceWithName()
     *
     */
    public function testCanCreateServiceWithNameUsingMainAndExistingSMInstance()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService("oauth2provider.server.{$serverKey}", new \stdClass());
        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration());
        $sm->get('OAuth2Provider/Options/Configuration')->setMainServer($serverKey);

        $r = $this->RequestAbstractFactory->canCreateServiceWithName($sm, '', "oauth2provider.server.{$serverKey}.request");
        $this->assertTrue($r);
    }

    /**
     * Tests RequestAbstractFactory->canCreateServiceWithName()
     * @expectedException \OAuth2Provider\Exception\ErrorException
     */
    public function testCanCreateServiceWithNameReturnsException()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration());
        $sm->get('OAuth2Provider/Options/Configuration')->setMainServer($serverKey);

        $r = $this->RequestAbstractFactory->canCreateServiceWithName($sm, '', "oauth2provider.server.{$serverKey}.request");
    }

    /**
     * Tests RequestAbstractFactory->canCreateServiceWithName()
     */
    public function testCanCreateServiceReturnsFalseOnMalformedInvalidRequestName()
    {
        $sm = new ServiceManager();
        $serverKey = "*&*";

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration());
        $sm->get('OAuth2Provider/Options/Configuration')->setMainServer($serverKey);

        $r = $this->RequestAbstractFactory->canCreateServiceWithName($sm, '', "oauth2provider.server.{$serverKey}.request");
        $this->assertFalse($r);
    }

    /**
     * Tests RequestAbstractFactory->createServiceWithName()
     */
    public function testCreateServiceWithName()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService("oauth2provider.server.{$serverKey}", new \stdClass());
        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration());
        $sm->setService('OAuth2Provider/Containers/RequestContainer', new RequestContainer());
        $sm->get('OAuth2Provider/Options/Configuration')->setMainServer($serverKey);

        // execute
        $this->RequestAbstractFactory->canCreateServiceWithName($sm, '', "oauth2provider.server.{$serverKey}.request");

        $r = $this->RequestAbstractFactory->createServiceWithName($sm, '', "oauth2provider.server.{$serverKey}.request");
        $this->assertInstanceOf('OAuth2\Request', $r);
    }
}

