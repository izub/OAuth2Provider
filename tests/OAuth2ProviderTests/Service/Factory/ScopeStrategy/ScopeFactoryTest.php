<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\ScopeType\ScopeConfigurations;
use OAuth2Provider\Service\Factory\ScopeStrategy\ScopeFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * ScopeFactory test case.
 */
class ScopeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     * @var ScopeFactory
     */
    private $ScopeFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ScopeFactory = new ScopeFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->ScopeFactory = null;
        parent::tearDown();
    }

    /**
     * Tests ScopeFactory->__invoke()
     * @group test1
     */
    public function test__invoke()
    {
        $serverKey = uniqid();
        $sm = new ServiceManager();
        $sm->setService('OAuth2Provider/Options/ScopeType/Scope', new ScopeConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());

        $storage = $sm->get('OAuth2Provider/Containers/StorageContainer');
        $storage[$serverKey]['scope'] = new \OAuth2ProviderTests\Assets\Storage\ScopeStorage();

        $f = $this->ScopeFactory->__invoke($sm, '');

        $options = array(
            'use_defined_scope_storage' => true,
        );

        $r = $f('OAuth2\Scope', $options, $serverKey);
        $this->assertInstanceOf('OAuth2\Storage\ScopeInterface', $r);
    }

    public function testCreateServiceUsesUserDefinedScope()
    {
        $serverKey = uniqid();
        $sm = new ServiceManager();
        $sm->setService('OAuth2Provider/Options/ScopeType/Scope', new ScopeConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());
        $f = $this->ScopeFactory->__invoke($sm, '');

        $options = array(
            'use_defined_scope_storage' => true,
            'storage' => 'scope/storage',
        );

        $r = $f('OAuth2\Scope', $options, $serverKey);
        $this->assertInstanceOf('OAuth2\Storage\ScopeInterface', $r);
    }

    public function testCreateServiceIsManualDefined()
    {
        $serverKey = uniqid();
        $sm = new ServiceManager();
        $sm->setService('OAuth2Provider/Options/ScopeType/Scope', new ScopeConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());

        $f = $this->ScopeFactory->__invoke($sm, '');

        $options = array(
            'default_scope' => 'basic',
            'client_supported_scopes' => array('basic', 'read', 'write', 'delete'),
            'client_default_scopes' => array('basic'),
            'supported_scopes' => array('basic', 'read', 'write', 'delete')
        );

        $r = $f('OAuth2\Scope', $options, $serverKey);
        $this->assertInstanceOf('OAuth2\Storage\ScopeInterface', $r);
    }

    /**
     * @expectedException \OAuth2Provider\Exception\InvalidClassException
     */
    public function testCreateServiceReturnsException()
    {
        $serverKey = uniqid();
        $sm = new ServiceManager();

        $storage = new StorageContainer();
        $storage[$serverKey]['scope'] = new Assets\Storage\AccessTokenStorage();
        $sm->setService('OAuth2Provider/Options/ScopeType/Scope', new ScopeConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', $storage);

        $f = $this->ScopeFactory->__invoke($sm, '');

        $options = array(
            'use_defined_scope_storage' => true,
        );

        $r = $f('OAuth2\Scope', $options, $serverKey);
    }
}

