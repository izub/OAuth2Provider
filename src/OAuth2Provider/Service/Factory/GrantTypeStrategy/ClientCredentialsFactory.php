<?php
namespace OAuth2Provider\Service\Factory\GrantTypeStrategy;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Exception;
use OAuth2Provider\Lib\Utilities;
use Zend\ServiceManager\Factory\FactoryInterface;

class ClientCredentialsFactory implements FactoryInterface
{
    const IDENTIFIER = 'client_credentials';

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
        return function ($clientCredentialsClassName, $options, $serverKey) use ($container) {

            $options = $container->get('OAuth2Provider/Options/GrantType/ClientCredentials')->setFromArray($options);

            $storage = Utilities::storageLookup(
                $serverKey,
                $options->getClientCredentialsStorage() ?: $options->getStorage(),
                $container->get('OAuth2Provider/Containers/StorageContainer'),
                $container,
                ClientCredentialsFactory::IDENTIFIER
            );

            if (empty($storage)) {
                throw new Exception\InvalidServerException(sprintf(
                    "Class '%s' error: storage of type '%s' is required for grant type '%s'",
                    __METHOD__,
                    ClientCredentialsFactory::IDENTIFIER,
                    $clientCredentialsClassName
                ));
            }

            return new $clientCredentialsClassName($storage, $options->getConfigs());
        };
    }
}
