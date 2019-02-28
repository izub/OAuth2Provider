<?php
namespace OAuth2Provider\Service\Factory\ClientAssertionTypeStrategy;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Exception;
use OAuth2Provider\Lib\Utilities;
use Zend\ServiceManager\Factory\FactoryInterface;

class HttpBasicFactory implements FactoryInterface
{
    /**
     * Strategy identifier
     * @var string
     */
    const IDENTIFIER = 'http_basic';

    /**
     * Storage identifier
     * @var string
     */
    const HTTP_BASIC_IDENTIFIER = 'client_credentials';

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return callable
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return function ($className, $options, $serverKey) use ($container) {

            $options = $container->get('OAuth2Provider/Options/ClientAssertionType/HttpBasic')->setFromArray($options);
            $configs = $options->getConfigs();

            $storage = Utilities::storageLookup(
                $serverKey,
                $options->getClientCredentialsStorage() ?: $options->getStorage(),
                $container->get('OAuth2Provider/Containers/StorageContainer'),
                $container,
                HttpBasicFactory::HTTP_BASIC_IDENTIFIER
            );

            if (empty($storage)) {
                throw new Exception\InvalidServerException(sprintf(
                    "Class '%s' error: storage of type '%s' is required for Http Basic '%s'",
                    __METHOD__,
                    HttpBasicFactory::IDENTIFIER,
                    $className
                ));
            }

            return new $className($storage, $configs);
        };
    }
}
