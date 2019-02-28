<?php
namespace OAuth2Provider\Controller;

use OAuth2Provider\Server;
use OAuth2Provider\ServerAwareInterface;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class UserCredentialsController extends AbstractRestfulController implements ControllerInterface, ServerAwareInterface
{
    /** @var Server */
    private $server;

    public function authorizeAction()
    {
        return new JsonModel(array(
            'error' => 'Error: The authorize endpoint is not supported for user credentials.',
        ));
    }

    public function requestAction()
    {
        $response = $this->server->handleTokenRequest();
        $params   = $response->getParameters();

        return new JsonModel($params);
    }

    public function resourceAction($scope = null)
    {
        $isValid       = $this->server->verifyResourceRequest($scope);
        $responseParam = $this->server->getResponse()->getParameters();

        $params = array();
        $params['success'] = $isValid;

        if (!$isValid) {
            $params['error']   = isset($responseParam['error']) ? $responseParam['error'] : "Invalid Request";
            $params['message'] = isset($responseParam['error_description']) ? $responseParam['error_description'] : "Access Token is invalid";
        } else {
            $params['message'] = "Access Token is Valid!";
        }

        return new JsonModel($params);
    }

    /**
     * Set the server instance
     *
     * @param Server $server
     */
    public function setServer(Server $server)
    {
        $this->server = $server;
    }
}
