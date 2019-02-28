<?php
namespace OAuth2Provider\Service\Factory\ResponseTypeStrategy;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Exception;
use OAuth2Provider\Lib\Utilities;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthorizationCodeFactory implements FactoryInterface
{
    /**
     * Identifiers
     * This will be used for defaults
     * @var string
     */
    const IDENTIFIER = 'authorization_code';

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
        return function ($authorizationCodeClassName, $options, $serverKey) use ($container) {
            $options = $container->get('OAuth2Provider/Options/ResponseType/AuthorizationCode')->setFromArray($options);

            // check if there is a direct defined 'token storage'
            $authorizationCodeStorage = Utilities::storageLookup(
                $serverKey,
                $options->getAuthorizationCodeStorage() ?: $options->getStorage(),
                $container->get('OAuth2Provider/Containers/StorageContainer'),
                $container,
                AuthorizationCodeFactory::IDENTIFIER
            );

            if (empty($authorizationCodeStorage)) {
                throw new Exception\InvalidServerException(sprintf(
                    "Class '%s' error: storage of type '%s' is required for Authorization Code '%s'",
                    __METHOD__,
                    AuthorizationCodeFactory::IDENTIFIER,
                    $authorizationCodeClassName
                ));
            }

            return new $authorizationCodeClassName($authorizationCodeStorage, $options->getConfigs());
        };
    }
}
