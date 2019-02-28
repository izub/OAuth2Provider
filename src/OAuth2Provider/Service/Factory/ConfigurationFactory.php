<?php
namespace OAuth2Provider\Service\Factory;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Exception;
use OAuth2Provider\Options;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConfigurationFactory implements FactoryInterface
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
        $config = $container->get('Config');

        if (!isset($config['oauth2provider'])) {
            throw new Exception\InvalidConfigException(sprintf(
                "Class '%s' error: config api_oauth_provider does not exist.",
                __CLASS__ . ":" . __METHOD__
            ));
        }

        return new Options\Configuration($config['oauth2provider']);
    }
}
