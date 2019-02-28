<?php
namespace OAuth2ProviderTests;

use OAuth2\Response as OAuth2Response;
use OAuth2Provider\Controller\UserCredentialsController;
use OAuth2Provider\Version;
use PHPUnit\Framework\TestCase;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\JsonModel;

/**
 * UserCredentialsController test case.
 */
class UserCredentialsControllerTest extends TestCase
{
    /**
     * @var UserCredentialsController
     */
    private $UserCredentialsController;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var RouteMatch
     */
    private $routeMatch;
    /**
     * @var MvcEvent
     */
    private $event;
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->UserCredentialsController = new UserCredentialsController();
        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setAlias('oauth2provider.server.main', 'oauth2provider.server.default');
        $this->UserCredentialsController->setServiceLocator($this->serviceManager);

        $this->request = new Request();
        $this->routeMatch = new RouteMatch(['controller' => UserCredentialsController::class]);
        $this->event = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->UserCredentialsController->setEvent($this->event);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->UserCredentialsController = null;
    }

    /**
     * Tests UserCredentialsController->authorizeAction()
     * @group test1
     */
    public function testAuthorizeAction()
    {
        $this->routeMatch->setParam('action', 'authorize');
        /** @var JsonModel $response */
        $response = $this->UserCredentialsController->dispatch($this->request);

        $this->assertNotEmpty($response->getVariable('error'));
    }

    /**
     * Tests UserCredentialsController->requestAction()
     * @group test2
     */
    public function testRequestAction()
    {
        $result = array(
            "access_token" => "b43fde87a6e2cdbd001b09c27f68f2b60a201a06",
            "token_type" => "bearer",
            "scope" => "get",
            "access_expires_in" => 3600,
            "refresh_token" => "f934c943a5f5d5a3f2db8889b2d734fd7ca4aa64",
            "refresh_expires_in" => "952659",
        );

        $serverMock = $this->getMockBuilder('OAuth2Provider\Server')
            ->setMethods(array('handleTokenRequest'))
            ->getMock();
        $serverMock->expects($this->once())
            ->method('handleTokenRequest')
            ->will($this->returnValue(new OAuth2Response($result)));

        // having set to server.default provides some sort if integration test
        $this->serviceManager->setService('oauth2provider.server.default', $serverMock);

        $this->routeMatch->setParam('action', 'request');

        /** @var JsonModel $response */
        $response = $this->UserCredentialsController->dispatch($this->request);
        $this->assertSame($result, $response->getVariables());
    }

    /**
     * Tests UserCredentialsController->resourceAction()
     * @group test3
     */
    public function testResourceActionWhereRequestIsValid()
    {
        $serverMock = $this->getMockBuilder('OAuth2Provider\Server')
            ->setMethods(array('verifyResourceRequest', 'getResponse'))
            ->getMock();
        $serverMock->expects($this->once())
            ->method('verifyResourceRequest')
            ->with($this->isNull())
            ->will($this->returnValue(true));
        $serverMock->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue(new OAuth2Response(array(
                'success' => true
            ))));

        // having set to server.default provides some sort if integration test
        $this->serviceManager->setService('oauth2provider.server.default', $serverMock);
        $this->routeMatch->setParam('action', 'resource');

        /** @var JsonModel $response */
        $response = $this->UserCredentialsController->dispatch($this->request);

        $this->assertEquals('Access Token is Valid!', $response->getVariable('message'));
    }

    /**
     * Tests UserCredentialsController->resourceAction()
     * @group test4
     */
    public function testResourceActionWhereRequestIsInValidWithErrorDescription()
    {
        $serverMock = $this->getMockBuilder('OAuth2Provider\Server')
            ->setMethods(array('verifyResourceRequest', 'getResponse'))
            ->getMock();
        $serverMock->expects($this->once())
            ->method('verifyResourceRequest')
            ->with($this->isNull())
            ->will($this->returnValue(false));
        $serverMock->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue(new OAuth2Response(array(
                'error'   => 'some error',
                'error_description' => 'some message',
            ))));

        $this->serviceManager->setService('oauth2provider.server.default', $serverMock);

        $this->routeMatch->setParam('action', 'resource');

        /** @var JsonModel $response */
        $response = $this->UserCredentialsController->dispatch($this->request);
        $this->assertFalse($response->getVariable('success'));
        $this->assertEquals('some error', $response->getVariable('error'));
        $this->assertEquals('some message', $response->getVariable('message'));
    }

    /**
     * Tests UserCredentialsController->resourceAction()
     * @group test4
     */
    public function testResourceActionWhereRequestIsInValidWithErrorDefaultDescription()
    {
        $serverMock = $this->getMockBuilder('OAuth2Provider\Server')
            ->setMethods(array('verifyResourceRequest', 'getResponse'))
            ->getMock();
        $serverMock->expects($this->once())
            ->method('verifyResourceRequest')
            ->with($this->isNull())
            ->will($this->returnValue(false));
        $serverMock->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue(new OAuth2Response(array(
                'nonexistingkeyplaceholder'   => 'zXxXz',
            ))));

        $this->serviceManager->setService('oauth2provider.server.default', $serverMock);

        $this->routeMatch->setParam('action', 'resource');

        /** @var JsonModel $response */
        $response = $this->UserCredentialsController->dispatch($this->request);
        $this->assertFalse($response->getVariable('success'));
        $this->assertEquals('Invalid Request', $response->getVariable('error'));
        $this->assertEquals('Access Token is invalid', $response->getVariable('message'));
    }
}
