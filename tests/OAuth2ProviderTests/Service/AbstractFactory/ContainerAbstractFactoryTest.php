<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\GrantTypeContainer;
use OAuth2Provider\Options\Configuration;
use OAuth2Provider\Server;
use OAuth2Provider\Service\AbstractFactory\ContainerAbstractFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * ContainerAbstractFactory test case.
 */
class ContainerAbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ContainerAbstractFactory
     */
    private $ContainerAbstractFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ContainerAbstractFactory = new ContainerAbstractFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->ContainerAbstractFactory = null;

        parent::tearDown();
    }

    /**
     * Tests ContainerAbstractFactory->canCreateServiceWithName()
     */
    public function testCanCreateServiceWithNameReturnsFalseOnNonMatches()
    {
        $sm = new ServiceManager();
        $r = $this->ContainerAbstractFactory->canCreateServiceWithName($sm, '', 'non-ouath');

        $this->assertFalse($r);
    }

    /**
     * Tests ContainerAbstractFactory->canCreateServiceWithName()
     */
    public function testCanCreateServiceWithNameReturnsFalseOnInvalidReges()
    {
        $sm = new ServiceManager();
        $r = $this->ContainerAbstractFactory->canCreateServiceWithName($sm, '', 'oauth2provider.server.---');

        $this->assertFalse($r);
    }

    /**
     * Tests ContainerAbstractFactory->canCreateServiceWithName()
     * @group test2
     */
    public function testCanCreateServiceWithKeyAsMainAndHasNoSMInstance()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration());
        $sm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $container = $sm->get('OAuth2Provider/Containers/GrantTypeContainer');
        $container[$serverKey]['grant_type'] = new \stdClass();

        $sm->setService('oauth2provider.server.default', new \stdClass());

        $r = $this->ContainerAbstractFactory->canCreateServiceWithName($sm, '', 'oauth2provider.server.main.grant_type');

        $this->assertTrue($r);
    }

    /**
     * Tests ContainerAbstractFactory->canCreateServiceWithName()
     * @group test3
     */
    public function testCanCreateServiceWithContainerKeyAndHasNoSMInstance()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService("oauth2provider.server.{$serverKey}", new \stdClass());

        $sm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $container = $sm->get('OAuth2Provider/Containers/GrantTypeContainer');
        $container[$serverKey]['user_credentials'] = new \stdClass();

        $r = $this->ContainerAbstractFactory->canCreateServiceWithName(
            $sm, '', "oauth2provider.server.{$serverKey}.grant_type.user_credentials"
        );

        $this->assertTrue($r);
    }

    /**
     * Tests ContainerAbstractFactory->canCreateServiceWithName()
     * @group test4
     */
    public function testCanCreateServiceWithContainerKeyAndHasSMInstance()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration());
        $sm->get('OAuth2Provider/Options/Configuration')->setServers(array(
            $serverKey => array(),
        ));

        $sm->setService("oauth2provider.server.{$serverKey}", new \stdClass());
        $sm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $container = $sm->get('OAuth2Provider/Containers/GrantTypeContainer');
        $container[$serverKey]['user_credentials'] = new \OAuth2ProviderTests\Assets\GrantTypeCustomUserCredentials();

        $r = $this->ContainerAbstractFactory->canCreateServiceWithName(
            $sm, '', "oauth2provider.server.{$serverKey}.grant_type.user_credentials"
        );

        $this->assertTrue($r);
    }

    /**
     * Tests ContainerAbstractFactory->canCreateServiceWithName()
     * @group test5
     */
    public function testCanCreateServiceWithContainerKeyHasNoSMInstanceAndInvalidContainerKey()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService("oauth2provider.server.{$serverKey}", new \stdClass());

        $sm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $container = $sm->get('OAuth2Provider/Containers/GrantTypeContainer');
        $container[$serverKey]['user_credentials'] = new \stdClass();

        $r = $this->ContainerAbstractFactory->canCreateServiceWithName(
            $sm, '', "oauth2provider.server.{$serverKey}.grant_type.zzz"
        );

        $this->assertFalse($r);
    }

    /**
     * Tests ContainerAbstractFactory->canCreateServiceWithName()
     * @group test6
     */
    public function testCanCreateServiceWithInvalidContainerKey()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService("oauth2provider.server.{$serverKey}", new \stdClass());

        $r = $this->ContainerAbstractFactory->canCreateServiceWithName(
            $sm, '', "oauth2provider.server.{$serverKey}.invalidcontainer"
        );

        $this->assertFalse($r);
    }

    /**
     * Tests ContainerAbstractFactory->createServiceWithName()
     * @group test7
     */
    public function testCreateServiceWithName()
    {
        $sm = new ServiceManager();
        $serverKey = uniqid();

        $sm->setService("oauth2provider.server.{$serverKey}", new \stdClass());

        $sm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $container = $sm->get('OAuth2Provider/Containers/GrantTypeContainer');
        $container[$serverKey]['user_credentials'] = new \stdClass();

        $this->ContainerAbstractFactory->canCreateServiceWithName(
            $sm, '', "oauth2provider.server.{$serverKey}.grant_type.user_credentials"
        );


        $r = $this->ContainerAbstractFactory->createServiceWithName($sm, '', '');
        $this->assertInstanceOf('stdClass', $r);
    }
}

