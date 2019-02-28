<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\ScopeTypeContainer;
use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\ScopeType\ScopeConfigurations;
use OAuth2Provider\Options\ServerFeatureTypeConfiguration;
use OAuth2Provider\Service\Factory\ScopeStrategy\ScopeFactory;
use OAuth2Provider\Service\Factory\ServerFeature\ScopeTypeFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * ScopeTypeFactory test case.
 */
class ScopeTypeFactoryTest extends \PHPUnit\Framework\TestCase
{

    /**
     *
     * @var ScopeTypeFactory
     */
    private $ScopeTypeFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ScopeTypeFactory = new ScopeTypeFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->ScopeTypeFactory = null;

        parent::tearDown();
    }

    /**
     * Tests ScopeTypeFactory->__invoke()
     */
    public function test__invoke()
    {
        $serverKey = uniqid();
        $sm = new ServiceManager();

        $storage = new StorageContainer();
        $storage[$serverKey]['scope'] = new \OAuth2ProviderTests\Assets\Storage\ScopeStorage();

        $sm->setService('OAuth2Provider/Containers/ScopeTypeContainer', new ScopeTypeContainer());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', $storage);
        $sm->setService('OAuth2Provider/Options/ScopeType/Scope', new ScopeConfigurations());
        $sm->setService('OAuth2Provider/ScopeStrategy/Scope', (new ScopeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());

        $options = array(
            'name' => 'scope'
        );

        $f = $this->ScopeTypeFactory->__invoke($sm, '');
        $r = $f($options, $serverKey);

        $this->assertInstanceOf('OAuth2\Scope', $r);
    }

    /**
     * Tests ScopeTypeFactory->__invoke()
     */
    public function testCreateServiceWhereOptionInsideArray()
    {
        $serverKey = uniqid();
        $sm = new ServiceManager();

        $storage = new StorageContainer();
        $storage[$serverKey]['scope'] = new \OAuth2ProviderTests\Assets\Storage\ScopeStorage();

        $sm->setService('OAuth2Provider/Containers/ScopeTypeContainer', new ScopeTypeContainer());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', $storage);
        $sm->setService('OAuth2Provider/Options/ScopeType/Scope', new ScopeConfigurations());
        $sm->setService('OAuth2Provider/ScopeStrategy/Scope', (new ScopeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());

        $options = array(
            array(
                'name' => 'scope'
            ),
        );

        $f = $this->ScopeTypeFactory->__invoke($sm, '');
        $r = $f($options, $serverKey);

        $this->assertInstanceOf('OAuth2\Scope', $r);
    }

    /**
     * Tests ScopeTypeFactory->__invoke()
     */
    public function testCreateServiceUsingManualScope()
    {
        $serverKey = uniqid();
        $sm = new ServiceManager();

        $sm->setService('OAuth2Provider/Containers/ScopeTypeContainer', new ScopeTypeContainer());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());
        $sm->setService('OAuth2Provider/Options/ScopeType/Scope', new ScopeConfigurations());
        $sm->setService('OAuth2Provider/ScopeStrategy/Scope', (new ScopeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());

        $options = array(
            'name' => 'scope',
            'options' => array(
                'default_scope' => 'basic',
                'client_supported_scopes' => array('basic', 'read', 'write', 'delete'),
                'client_default_scopes' => array('basic'),
                'supported_scopes' => array('basic', 'read', 'write', 'delete')
            )
        );

        $f = $this->ScopeTypeFactory->__invoke($sm, '');
        $r = $f($options, $serverKey);

        $this->assertInstanceOf('OAuth2\Scope', $r);
    }

    /**
     * Tests ScopeTypeFactory->__invoke()
     */
    public function testCreateServiceReturnsNull()
    {
        $serverKey = uniqid();
        $sm = new ServiceManager();

        $options = array();

        $f = $this->ScopeTypeFactory->__invoke($sm, '');
        $r = $f($options, $serverKey);

        $this->assertNull($r);
    }
}

