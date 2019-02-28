<?php
namespace OAuth2ProviderTests;

use OAuth2\GrantType\UserCredentials;
use OAuth2Provider\Builder\StrategyBuilder;
use OAuth2Provider\Containers\GrantTypeContainer;
use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\GrantType\UserCredentialsConfigurations;
use OAuth2Provider\Options\ServerFeatureTypeConfiguration;
use OAuth2Provider\Service\Factory\GrantTypeStrategy\UserCredentialsFactory;
use Zend\Filter\FilterPluginManager;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\ServiceManager\ServiceManager;

/**
 * GrantTypeFactory test case.
 */
class StrategyBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Tests new StrategyBuilder->__construct()
     * @group test1a
     */
    public function testConstruct()
    {
        $mainSm = new ServiceManager();
        $container = new GrantTypeContainer();
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder(array(), 'serverkey1', array('strategies'), array('concreteclasses'), $container, $interface);
        $this->assertInstanceOf(StrategyBuilder::class, $builder);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test1b
     */
    public function testInitStrategyFeatureIsPHPObject()
    {
        $storage = new \OAuth2\GrantType\UserCredentials(new \OAuth2ProviderTests\Assets\StorageUserCredentials());
        $strategiesConfig = array(
            $storage,
        );

        $mainSm = new ServiceManager();

        $subjects  = $strategiesConfig;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertSame(array('user_credentials' => $storage), $r);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test2
     */
    public function testInitStrategyFeatureIsPHPObjectWithUserCredentialAsParent()
    {
        $storage = new \OAuth2ProviderTests\Assets\GrantTypeWithParentUserCredentials(new \OAuth2ProviderTests\Assets\StorageUserCredentials());
        $strategiesConfig = array(
            $storage,
        );

        $mainSm = new ServiceManager();

        $subjects  = $strategiesConfig;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertSame(array('user_credentials' => $storage), $r);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test2b
     */
    public function testInitStrategyFeatureIsPHPObjectWithCustomUserCredentials()
    {
        $storage = new \OAuth2ProviderTests\Assets\GrantTypeCustomUserCredentials();
        $strategiesConfig = array(
            $storage,
        );

        $filterManager = new FilterPluginManager();
        $mainSm = new ServiceManager();
        $mainSm->setService('FilterManager', $filterManager);
        $filterManager->setService('wordcamelcasetounderscore', new CamelCaseToUnderscore());

        $subjects  = $strategiesConfig;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertSame(array('user_credentials' => $storage), $r);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test3
     */
    public function testInitStrategyFeatureIsAServiceManagerElement()
    {
        $storage = new \OAuth2ProviderTests\Assets\GrantTypeWithParentUserCredentials(new \OAuth2ProviderTests\Assets\StorageUserCredentials());
        $config = array(
            'name' => 'namespace_user_credentials'
        );

        $mainSm = new ServiceManager();
        $mainSm->setService('namespace_user_credentials', $storage);

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertSame(array('user_credentials' => $storage), $r);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test4
     */
    public function testInitStrategyFeatureIsAServiceManagerElementUsingConfigAsArrayAndDirectNameAsSMElement()
    {
        $storage = new \OAuth2ProviderTests\Assets\GrantTypeWithParentUserCredentials(new \OAuth2ProviderTests\Assets\StorageUserCredentials());
        $config = array(
            array(
                'name' => 'user_credentials_x1'
            )
        );

        $mainSm = new ServiceManager();
        $mainSm->setService('user_credentials_x1', $storage);
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertSame(array('user_credentials' => $storage), $r);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test5
     */
    public function testInitStrategyFeatureWithStorageAsDirectKey()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            array(
                'name' => 'OAuth2\GrantType\UserCredentials',
                'options' => array(
                    'storage' => 'user_credentials'
                )
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test6
     */
    public function testInitStrategyFeatureWithStorageAsGrantTypeKey()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            'user_credentials' => array(
                'name' => 'OAuth2\GrantType\UserCredentials',
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test5b
     */
    public function testInitStrategyFeatureWithFeatureNameAsAvailableStrategyKey()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server2']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            'user_credentials',
        );

        $subjects  = $config;
        $serverKey = 'server2';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test6b
     */
    public function testInitStrategyFeatureWithParentAsConcreteGrantTypeAndNotInsideArray()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            'OAuth2ProviderTests\Assets\GrantTypeWithParentUserCredentials',
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test7
     */
    public function testInitStrategyFeatureWithParentAsConcreteGrantType()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            array(
                'name' => 'OAuth2ProviderTests\Assets\GrantTypeWithParentUserCredentials',
                'options' => array(
                    'storage' => 'user_credentials'
                )
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test7a
     */
    public function testInitStrategyFeatureWithParentAsConcreteGrantTypeAndDirectArrayUse()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            array(
                'OAuth2ProviderTests\Assets\GrantTypeWithParentUserCredentials',
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test7aa
     */
    public function testInitStrategyFeatureWithParentAsConcreteGrantTypeAndKeyasExtendedConcrete()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            'user_credentials' => 'OAuth2ProviderTests\Assets\GrantTypeWithParentUserCredentials',
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test7ab
     */
    public function testInitStrategyFeatureWithNameAsAliasedKeyAndDirectArrayUse()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            array(
                'user_credentials',
                'options' => array(),
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test7ba
     */
    public function testInitStrategyFeatureWithParentAsSMAware()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            array(
                'name' => 'OAuth2ProviderTests\Assets\CustomUserCredentialsSMAware',
                'options' => array(
                    'storage' => 'user_credentials'
                )
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test7bb
     */
    public function testInitStrategyFeatureWithParentAsSLAware()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            array(
                'name' => 'OAuth2ProviderTests\Assets\CustomUserCredentialsSLAware',
                'options' => array(
                    'storage' => 'user_credentials'
                )
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test7b
     */
    public function testInitStrategyFeatureWithParentAsKeyAndOptions()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            'user_credentials' => array(
                'options' => array(),
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test7ba
     */
    public function testInitStrategyFeatureWithParentAsKeyAndNameAndOptions()
    {
        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $mainSm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $mainSm->setService('OAuth2Provider/Containers/StorageContainer', $storageContainer);
        $mainSm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->createService($mainSm));

        $config = array(
            'user_credentials' => array(
                'name' => 'user_credentials',
                'options' => array(),
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertInstanceOf('OAuth2\GrantType\UserCredentials', $r['user_credentials']);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test7c
     */
    public function testInitStrategyFeatureWithEmptyConfigString()
    {
        $mainSm = new ServiceManager();

        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $config = '';

        $subjects  = $config;
        $serverKey = uniqid();
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertEmpty($r);
        $this->assertInternalType('array', $r);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @group test7d
     */
    public function testInitStrategyFeatureWithEmptyConfigArray()
    {
        $mainSm = new ServiceManager();

        // seed the storagecontainer
        $storageContainer = new StorageContainer();
        $storageContainer['server1']['user_credentials'] = new \OAuth2ProviderTests\Assets\StorageUserCredentials();

        $config = array();

        $subjects  = $config;
        $serverKey = uniqid();
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertEmpty($r);
        $this->assertInternalType('array', $r);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @expectedException \OAuth2Provider\Exception\InvalidServerException
     * @group test8
     */
    public function testInitStrategyFeatureReturnsExceptionOnNoClassKey()
    {
        $mainSm = new ServiceManager();

        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());

        $config = array(
            array(
                'options' => array(
                    'storage' => 'user_credentials'
                )
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @expectedException \OAuth2Provider\Exception\InvalidClassException
     * @group test9
     */
    public function testInitStrategyFeatureReturnsExceptionOnInvalidStrategy()
    {
        $mainSm = new ServiceManager();

        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());

        $config = array(
            array(
                'name' => 'OAuth2ProviderTests\Nothing',
                'options' => array(
                    'storage' => 'user_credentials',
                ),
            )
        );

        $subjects  = $config;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\GrantType\GrantTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
    }

    /**
     * Tests GrantTypeFactory->createService()
     * @expectedException \OAuth2Provider\Exception\InvalidClassException
     * @group test10
     */
    public function testInitStrategyFeatureReturnsExceptionOnInvalidException()
    {
        $storage = new \OAuth2\GrantType\UserCredentials(new \OAuth2ProviderTests\Assets\StorageUserCredentials());
        $strategiesConfig = array(
            $storage,
        );

        $mainSm = new ServiceManager();

        $subjects  = $strategiesConfig;
        $serverKey = 'server1';
        $container = new GrantTypeContainer();
        $strategies = array('user_credentials' => 'OAuth2Provider/GrantTypeStrategy/UserCredentials');
        $concreteClasses = array('user_credentials'   => 'OAuth2\GrantType\UserCredentials');
        $interface = 'OAuth2\ResponseType\ResponseTypeInterface';
        $builder = new StrategyBuilder($subjects, $serverKey, $strategies, $concreteClasses, $container, $interface);

        $r = $builder->initStrategyFeature($mainSm);
        $this->assertSame(array('user_credentials' => $storage), $r);
    }
}

