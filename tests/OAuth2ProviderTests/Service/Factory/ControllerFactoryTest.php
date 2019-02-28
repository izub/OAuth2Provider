<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Options\Configuration;
use OAuth2Provider\Service\Factory\ControllerFactory;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

/**
 * ControllerFactory test case.
 */
class ControllerFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ControllerFactory
     */
    private $ControllerFactory;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        // TODO Auto-generated ControllerFactoryTest::setUp()
        $this->ControllerFactory = new ControllerFactory(/* parameters */);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated ControllerFactoryTest::tearDown()
        $this->ControllerFactory = null;
        parent::tearDown();
    }

    /**
     * Tests ControllerFactory->createService()
     * @group test1
     */
    public function testCreateServiceWithValidControllerUsesDefaultController()
    {
        $serverKey = uniqid();

        $config = array(
            'servers' => array(
                $serverKey => array(),
            ),
            'main_server' => $serverKey,
            'default_controller' => 'OAuth2ProviderTests\Assets\ImplementingController',
        );

        $application = $this->createMock(Application::class);
        $application->method('getMvcEvent')->willReturn(new MvcEvent());

        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Options/Configuration', new Configuration($config));
        $mainSm->setService('Application', $application);


        $pluginSM = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')
            ->setMethods(array('getServiceLocator'))
            ->getMock();
        $pluginSM->expects($this->any())
            ->method('getServiceLocator')
            ->will($this->returnValue($mainSm));

        $r = $this->ControllerFactory->createService($pluginSM);
        $this->assertInstanceOf('OAuth2Provider\Controller\ControllerInterface', $r);
    }

    /**
     * Tests ControllerFactory->createService()
     * @group test2
     */
    public function testCreateServiceWithValidControllerUsesServerSpecificController()
    {
        $serverKey = uniqid();

        $mainSm = new ServiceManager();

        $config = array(
            'servers' => array(
                $serverKey => array(
                    'controller' => 'OAuth2ProviderTests\Assets\ImplementingController'
                ),
            ),
            'main_server' => $serverKey,
        );

        $application = $this->createMock(Application::class);
        $application->method('getMvcEvent')->willReturn(new MvcEvent());

        $mainSm->setService('OAuth2Provider/Options/Configuration', new Configuration($config));
        $mainSm->setService('Application', $application);

        $pluginSM = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')
            ->setMethods(array('getServiceLocator'))
            ->getMock();
        $pluginSM->expects($this->any())
            ->method('getServiceLocator')
            ->will($this->returnValue($mainSm));

        $r = $this->ControllerFactory->createService($pluginSM);
        $this->assertInstanceOf('OAuth2Provider\Controller\ControllerInterface', $r);
    }

    /**
     * Tests ControllerFactory->createService()
     * @group test3
     */
    public function testCreateServiceWithValidControllerUsesServerSpecificControllerOnMultiServers()
    {
        $serverKey = uniqid();

        $application = $this->createMock(Application::class);
        $application->method('getMvcEvent')->willReturn(new MvcEvent());

        $mainSm = new ServiceManager();
        $mainSm->setService('Application', $application);

        $routeMatch = new \Zend\Mvc\Router\RouteMatch(array('version' => 'v2'));
        $mainSm->get('Application')->getMvcEvent()->setRouteMatch($routeMatch);

        $config = array(
                'servers' => array(
                    $serverKey => array(
                        array(
                            'controller' => 'xxx',
                            'version' => 'v1',
                        ),
                        array(
                            'controller' => 'OAuth2ProviderTests\Assets\ImplementingController',
                            'version' => 'v2',
                        ),
                    ),
                ),
                'main_server'  => $serverKey,
                'main_version' => 'v2',
                'default_controller' => 'OAuth2ProviderTests\Assets\ImplementingController'
        );

        $mainSm->setService('OAuth2Provider/Options/Configuration', new Configuration($config));

        $pluginSM = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')
            ->setMethods(array('getServiceLocator'))
            ->getMock();
        $pluginSM->expects($this->exactly(2))
            ->method('getServiceLocator')
            ->will($this->returnValue($mainSm));

        $r = $this->ControllerFactory->createService($pluginSM);
        $this->assertInstanceOf('OAuth2Provider\Controller\ControllerInterface', $r);
    }

    /**
     * Tests ControllerFactory->createService()
     * @group test4
     * @expectedException \OAuth2Provider\Exception\InvalidConfigException
     */
    public function testCreateServiceReturnsException()
    {
        $serverKey = uniqid();

        $application = $this->createMock(Application::class);
        $application->method('getMvcEvent')->willReturn(new MvcEvent());

        $mainSm = new ServiceManager();

        $config = new Configuration();
        $config->setFromArray(array(
            'servers' => array(
                $serverKey => array(
                    'controller' => 'OAuth2ProviderTests\Assets\RegularController'
                ),
            ),
            'main_server' => $serverKey,
            'default_controller' => 'invalid'
        ));

        $mainSm->setService('OAuth2Provider/Options/Configuration', $config);
        $mainSm->setService('Application', $application);
        $mainSm->setService('Config', $config);

        $pluginSM = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')
            ->setMethods(array('getServiceLocator'))
            ->getMock();
        $pluginSM
            ->method('getServiceLocator')
            ->will($this->returnValue($mainSm));

        $r = $this->ControllerFactory->createService($pluginSM);
    }
}

