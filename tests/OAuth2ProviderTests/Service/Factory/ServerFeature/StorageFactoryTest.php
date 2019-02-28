<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Service\Factory\ServerFeature\StorageFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * GrantTypeFactory test case.
 */
class StorageFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var StorageFactory
     */
    private $storageFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        // TODO Auto-generated GrantTypeFactoryTest::setUp()
        $this->storageFactory = new StorageFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated GrantTypeFactoryTest::tearDown()
        $this->storageFactory = null;
        parent::tearDown();
    }

    /**
     * Tests GrantTypeFactory->__invoke()
     */
    public function test__invoke()
    {
        $storages = array(
            'user_credentials' => new \stdClass(),
        );

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());

        $storageFactory = $this->storageFactory->__invoke($mainSm, '');
        $r = $storageFactory($storages, 'server_key');

        $this->assertIsArray($r);
    }

    /**
     * Tests GrantTypeFactory->__invoke()
     * @expectedException \OAuth2Provider\Exception\InvalidConfigException
     */
    public function testCreateServiceReturnsException()
    {
        $storages = array(
            'not_in_valid' => new \stdClass(),
        );

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());

        $storageFactory = $this->storageFactory->__invoke($mainSm, '');
        $r = $storageFactory($storages, 'server_key');
    }
}

