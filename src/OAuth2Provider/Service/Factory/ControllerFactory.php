<?php
namespace OAuth2Provider\Service\Factory;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Controller\ControllerInterface;
use OAuth2Provider\Exception;
use OAuth2Provider\ServerAwareInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ControllerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $configuration = $container->get('OAuth2Provider/Options/Configuration');

        // check for a specific defined server controller
        $servers   = $configuration->getServers();
        $serverKey = $configuration->getMainServer();
        if (isset($servers[$serverKey]['controller'])) {
            $controller = $servers[$serverKey]['controller'];
        } else {
            $mvcEvent = $container->get('Application')->getMvcEvent();
            if (isset($mvcEvent) && null !== $mvcEvent->getRouteMatch()) {
                $version = $mvcEvent->getRouteMatch()->getParam('version');
            }
            if (empty($version)) {
                $version = $configuration->getMainVersion();
            }

            if (isset($servers[$serverKey])) {
                foreach ($servers[$serverKey] as $server) {
                    // fix for php 5.3 bug which isset outputs true if var is string
                    if (is_array($server) && !empty($server['controller'])
                        && (!empty($server['version']) && $server['version'] === $version)
                    ) {
                        $controller = $server['controller'];
                        break;
                    }
                }
            }
        }

        if (empty($controller)) {
            $controller = $configuration->getDefaultController();
        }

        $controller = new $controller();

        if ($controller instanceof ServerAwareInterface) {
            $server = $container->get('oauth2provider.server.main');
            $controller->setServer($server);
        }

        // check for valid controller
        if (!$controller instanceof ControllerInterface) {
            throw new Exception\InvalidConfigException(sprintf(
                "Class '%s': controller '%s' is not an instance of ControllerInterface",
                __CLASS__ . ":" . __METHOD__,
                get_class($controller)
            ));
        }

        return $controller;
    }
}
