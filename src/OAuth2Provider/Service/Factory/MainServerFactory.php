<?php
namespace OAuth2Provider\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MainServerFactory implements FactoryInterface
{
    /**
     * Initialized the Main Server used by the controllers
     *
     * The main server call is: oauth2provider.server.main
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $configuration = $container->get('OAuth2Provider/Options/Configuration');

        // initialize the main server via the abstract server factory;
        return $container->get('oauth2provider.server.' . $configuration->getMainServer());
    }
}
