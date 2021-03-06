<?php
namespace OAuth2ProviderTests;

use OAuth2\Request;
use OAuth2\Response;
use OAuth2Provider\Containers\ClientAssertionTypeContainer;
use OAuth2Provider\Containers\ConfigContainer;
use OAuth2Provider\Containers\GrantTypeContainer;
use OAuth2Provider\Containers\ResponseTypeContainer;
use OAuth2Provider\Containers\ScopeTypeContainer;
use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Containers\TokenTypeContainer;
use OAuth2Provider\Options\Configuration;
use OAuth2Provider\Options\GrantType\UserCredentialsConfigurations;
use OAuth2Provider\Options\ServerConfigurations;
use OAuth2Provider\Service\AbstractFactory\ServerAbstractFactory;
use OAuth2Provider\Service\Factory\GrantTypeStrategy\UserCredentialsFactory;
use OAuth2Provider\Service\Factory\ServerFeature\ClientAssertionTypeFactory;
use OAuth2Provider\Service\Factory\ServerFeature\ConfigFactory;
use OAuth2Provider\Service\Factory\ServerFeature\GrantTypeFactory;
use OAuth2Provider\Service\Factory\ServerFeature\ResponseTypeFactory;
use OAuth2Provider\Service\Factory\ServerFeature\ScopeTypeFactory;
use OAuth2Provider\Service\Factory\ServerFeature\StorageFactory;
use OAuth2Provider\Service\Factory\ServerFeature\TokenTypeFactory;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

/**
 * ServerAbstractFactory test case.
 */
class ServerAbstractFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ServerAbstractFactory
     */
    private $ServerAbstractFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ServerAbstractFactory = new ServerAbstractFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->ServerAbstractFactory = null;
        parent::tearDown();
    }

    /**
     * Tests ServerAbstractFactory->canCreate()
     * @group test1
     */
    public function testCanCreate()
    {
        $serverKey = uniqid();

        $sm = new ServiceManager();

        $oauthconfig = array(
                'servers' => array(
                    $serverKey => array(
                        'storages' => array(
                            'user_credentials' => new \OAuth2ProviderTests\Assets\StorageUserCredentials(),
                        ),
                        'grant_types' => array(
                            'user_credentials'
                        ),
                        'server_class' => 'OAuth2ProviderTests\Assets\Foo',
                    ),
                ),
        );

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration($oauthconfig));
        $application = $this->createMock(Application::class);
        $application->method('getMvcEvent')->willReturn(new MvcEvent());
        $sm->setService('Application', $application);

        $r = $this->ServerAbstractFactory->canCreate($sm, "oauth2provider.server.{$serverKey}");
        $this->assertTrue($r);
    }

    /**
     * Tests ServerAbstractFactory->canCreate()
     * @group test2
     * @expectedException \OAuth2Provider\Exception\InvalidServerException
     */
    public function testCanCreateServiceWithNameReturnException()
    {
        $config = array(
            'myconfig' => array(),
        );

        $configMock = $this->createPartialMock('stdClass', array('getServers'));
        $configMock->expects($this->once())
            ->method('getServers')
            ->will($this->returnValue($config));

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/Configuration', $configMock);

        $r = $this->ServerAbstractFactory->canCreate($mainSm, 'oauth2provider.server.notexist');
        $this->assertTrue($r);
    }

    /**
     * Tests ServerAbstractFactory->canCreate()
     * @group test3
     */
    public function testCanCreateServiceWithNameReturnFalseOnRegularRequest()
    {
        $mainSm = new ServiceManager();

        $r = $this->ServerAbstractFactory->canCreate($mainSm, 'unmatched');
        $this->assertFalse($r);
    }

    /**
     * Tests ServerAbstractFactory->canCreate()
     * @group test4
     */
    public function testCanCreateServiceWithNameReturnFalseOnMismatchedReges()
    {
        $mainSm = new ServiceManager();

        $r = $this->ServerAbstractFactory->canCreate($mainSm, 'oauth2provider.server.noMatch&here');
        $this->assertFalse($r);
    }

    /**
     * Tests ServerAbstractFactory->__invoke()
     * @group test5
     */
    public function testCreateServiceWithName()
    {
        $serverKey = uniqid();

        $sm = new ServiceManager();

        $oauthconfig = array(
                'servers' => array(
                    $serverKey => array(
                        'storages' => array(
                            'user_credentials' => new \OAuth2ProviderTests\Assets\StorageUserCredentials(),
                        ),
                        'grant_types' => array(
                            'user_credentials'
                        ),
                    ),
                ),
        );

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration($oauthconfig));
        $application = $this->createMock(Application::class);
        $application->method('getMvcEvent')->willReturn(new MvcEvent());
        $sm->setService('Application', $application);
        $sm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $sm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Options/Server', new ServerConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());
        $sm->setService('OAuth2Provider/Containers/ConfigContainer', new ConfigContainer());
        $sm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ResponseTypeContainer', new ResponseTypeContainer());
        $sm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ScopeTypeContainer', new ScopeTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ClientAssertionTypeContainer', new ClientAssertionTypeContainer());
        $sm->setService('OAuth2Provider/Service/ServerFeature/StorageFactory', (new StorageFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ConfigFactory', (new ConfigFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/GrantTypeFactory', (new GrantTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ResponseTypeFactory', (new ResponseTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/TokenTypeFactory', (new TokenTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ScopeTypeFactory', (new ScopeTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ClientAssertionTypeFactory', (new ClientAssertionTypeFactory())->__invoke($sm, ''));
        $sm->setService('oauth2provider.server.main.request', new Request());
        $sm->setService('oauth2provider.server.main.response', new Response());

        // initialize
        $this->ServerAbstractFactory->canCreate($sm, "oauth2provider.server.{$serverKey}");

        $r = $this->ServerAbstractFactory->__invoke($sm, "oauth2provider.server.{$serverKey}");
        $this->assertInstanceOf('OAuth2Provider\Server', $r);
    }

    /**
     * Tests ServerAbstractFactory->__invoke()
     * @group test6
     */
    public function testCreateServiceWithNameWillReturnOriginalServer()
    {
        $serverKey = uniqid();

        $sm = new ServiceManager();

        $oauthconfig = array(
                'servers' => array(
                    $serverKey => array(
                        'storages' => array(
                            'user_credentials' => new \OAuth2ProviderTests\Assets\StorageUserCredentials(),
                        ),
                        'grant_types' => array(
                            'user_credentials'
                        ),
                        'server_class' => 'OAuth2ProviderTests\Assets\Foo',
                    ),
                ),
        );

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration($oauthconfig));
        $application = $this->createMock(Application::class);
        $application->method('getMvcEvent')->willReturn(new MvcEvent());
        $sm->setService('Application', $application);
        $sm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $sm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Options/Server', new ServerConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());
        $sm->setService('OAuth2Provider/Containers/ConfigContainer', new ConfigContainer());
        $sm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ResponseTypeContainer', new ResponseTypeContainer());
        $sm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ScopeTypeContainer', new ScopeTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ClientAssertionTypeContainer', new ClientAssertionTypeContainer());
        $sm->setService('OAuth2Provider/Service/ServerFeature/StorageFactory', (new StorageFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ConfigFactory', (new ConfigFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/GrantTypeFactory', (new GrantTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ResponseTypeFactory', (new ResponseTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/TokenTypeFactory', (new TokenTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ScopeTypeFactory', (new ScopeTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ClientAssertionTypeFactory', (new ClientAssertionTypeFactory())->__invoke($sm, ''));

        // initialize
        $this->ServerAbstractFactory->canCreate($sm, "oauth2provider.server.{$serverKey}");

        $r = $this->ServerAbstractFactory->__invoke($sm, "oauth2provider.server.{$serverKey}");
        $this->assertInstanceOf('OAuth2\Server', $r);
    }

    /**
     * Tests ServerAbstractFactory->__invoke()
     * @group test7
     */
    public function testCreateServiceWithNameWillMatchServerWithVersion()
    {
        $serverKey = uniqid();

        $sm = new ServiceManager();

        // mock the route match

        $oauthconfig = array(
                'servers' => array(
                    $serverKey => array(
                        'storages' => array(
                            'user_credentials' => new \OAuth2ProviderTests\Assets\StorageUserCredentials(),
                        ),
                        'grant_types' => array(
                            'user_credentials'
                        ),
                        'server_class' => 'OAuth2ProviderTests\Assets\Foo',
                        'version' => 'v2',
                    ),
                ),
        );

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration($oauthconfig));
        $application = $this->createMock(Application::class);
        $application->method('getMvcEvent')->willReturn(new MvcEvent());
        $sm->setService('Application', $application);
        $sm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $sm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Options/Server', new ServerConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());
        $sm->setService('OAuth2Provider/Containers/ConfigContainer', new ConfigContainer());
        $sm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ResponseTypeContainer', new ResponseTypeContainer());
        $sm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ScopeTypeContainer', new ScopeTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ClientAssertionTypeContainer', new ClientAssertionTypeContainer());
        $sm->setService('OAuth2Provider/Service/ServerFeature/StorageFactory', (new StorageFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ConfigFactory', (new ConfigFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/GrantTypeFactory', (new GrantTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ResponseTypeFactory', (new ResponseTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/TokenTypeFactory', (new TokenTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ScopeTypeFactory', (new ScopeTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ClientAssertionTypeFactory', (new ClientAssertionTypeFactory())->__invoke($sm, ''));
        $routeMatch = new \Zend\Router\RouteMatch(array('version' => 'v2'));
        $sm->get('Application')->getMvcEvent()->setRouteMatch($routeMatch);

        // initialize
        $this->ServerAbstractFactory->canCreate($sm, "oauth2provider.server.{$serverKey}");

        $r = $this->ServerAbstractFactory->__invoke($sm, "oauth2provider.server.{$serverKey}");
        $this->assertInstanceOf('OAuth2\Server', $r);
    }

    /**
     * Tests ServerAbstractFactory->__invoke()
     * @group test8
     */
    public function testCreateServiceWithNameWillReturnServerWithMultipleVersion()
    {
        $serverKey = uniqid();

        $sm = new ServiceManager();

        // mock the route match

        $oauthconfig = array(
                'servers' => array(
                    $serverKey => array(
                        array(
                            'storages' => array(
                                'user_credentials' => new \OAuth2ProviderTests\Assets\StorageUserCredentials(),
                            ),
                            'grant_types' => array(
                                'user_credentials'
                            ),
                            'server_class' => 'OAuth2ProviderTests\Assets\Foo',
                            'version' => 'v1',
                        ),
                        array(
                            'storages' => array(
                                'user_credentials' => new \OAuth2ProviderTests\Assets\StorageUserCredentials(),
                            ),
                            'grant_types' => array(
                                'user_credentials'
                            ),
                            'server_class' => 'OAuth2ProviderTests\Assets\Foo',
                            'version' => 'v2',
                        ),
                    ),
                ),
        );

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration($oauthconfig));
        $application = $this->createMock(Application::class);
        $application->method('getMvcEvent')->willReturn(new MvcEvent());
        $sm->setService('Application', $application);
        $sm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $sm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Options/Server', new ServerConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());
        $sm->setService('OAuth2Provider/Containers/ConfigContainer', new ConfigContainer());
        $sm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ResponseTypeContainer', new ResponseTypeContainer());
        $sm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ScopeTypeContainer', new ScopeTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ClientAssertionTypeContainer', new ClientAssertionTypeContainer());
        $sm->setService('OAuth2Provider/Service/ServerFeature/StorageFactory', (new StorageFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ConfigFactory', (new ConfigFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/GrantTypeFactory', (new GrantTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ResponseTypeFactory', (new ResponseTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/TokenTypeFactory', (new TokenTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ScopeTypeFactory', (new ScopeTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ClientAssertionTypeFactory', (new ClientAssertionTypeFactory())->__invoke($sm, ''));
        $routeMatch = new \Zend\Router\RouteMatch(array('version' => 'v2'));
        $sm->get('Application')->getMvcEvent()->setRouteMatch($routeMatch);

        // initialize
        $this->ServerAbstractFactory->canCreate($sm, "oauth2provider.server.{$serverKey}");

        $r = $this->ServerAbstractFactory->__invoke($sm, "oauth2provider.server.{$serverKey}");
        $this->assertInstanceOf('OAuth2\Server', $r);
    }

    /**
     * Tests ServerAbstractFactory->__invoke()
     * @group test9
     */
    public function testCreateServiceWithNameWillMatchServerWithMainVersion()
    {
        $serverKey = uniqid();

        $sm = new ServiceManager();

        $oauthconfig = array(
                'servers' => array(
                    $serverKey => array(
                        'storages' => array(
                            'user_credentials' => new \OAuth2ProviderTests\Assets\StorageUserCredentials(),
                        ),
                        'grant_types' => array(
                            'user_credentials'
                        ),
                        'server_class' => 'OAuth2ProviderTests\Assets\Foo',
                        'version' => 'v2',
                    ),
                ),
                'main_server'  => $serverKey,
                'main_version' => 'v2',
        );

        $sm->setService('OAuth2Provider/Options/Configuration', new Configuration($oauthconfig));
        $application = $this->createMock(Application::class);
        $application->method('getMvcEvent')->willReturn(new MvcEvent());
        $sm->setService('Application', $application);
        $sm->setService('OAuth2Provider/Options/GrantType/UserCredentials', new UserCredentialsConfigurations());
        $sm->setService('OAuth2Provider/GrantTypeStrategy/UserCredentials', (new UserCredentialsFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Options/Server', new ServerConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());
        $sm->setService('OAuth2Provider/Containers/ConfigContainer', new ConfigContainer());
        $sm->setService('OAuth2Provider/Containers/GrantTypeContainer', new GrantTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ResponseTypeContainer', new ResponseTypeContainer());
        $sm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ScopeTypeContainer', new ScopeTypeContainer());
        $sm->setService('OAuth2Provider/Containers/ClientAssertionTypeContainer', new ClientAssertionTypeContainer());
        $sm->setService('OAuth2Provider/Service/ServerFeature/StorageFactory', (new StorageFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ConfigFactory', (new ConfigFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/GrantTypeFactory', (new GrantTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ResponseTypeFactory', (new ResponseTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/TokenTypeFactory', (new TokenTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ScopeTypeFactory', (new ScopeTypeFactory())->__invoke($sm, ''));
        $sm->setService('OAuth2Provider/Service/ServerFeature/ClientAssertionTypeFactory', (new ClientAssertionTypeFactory())->__invoke($sm, ''));


        // initialize
        $this->ServerAbstractFactory->canCreate($sm, "oauth2provider.server.{$serverKey}");

        $r = $this->ServerAbstractFactory->__invoke($sm, "oauth2provider.server.{$serverKey}");
        $this->assertInstanceOf('OAuth2\Server', $r);
    }
}
