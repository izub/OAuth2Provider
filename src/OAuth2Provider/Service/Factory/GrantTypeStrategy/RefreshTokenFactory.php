<?php
namespace OAuth2Provider\Service\Factory\GrantTypeStrategy;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Exception;
use OAuth2Provider\Lib\Utilities;
use Zend\ServiceManager\Factory\FactoryInterface;

class RefreshTokenFactory implements FactoryInterface
{
    /**
     * Identifiers
     * This will be used for defaults
     * @var string
     */
    const IDENTIFIER = 'refresh_token';

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
        return function ($refreshTokenClassName, $options, $serverKey) use ($container) {

            $options = $container->get('OAuth2Provider/Options/GrantType/RefreshToken')->setFromArray($options);

            // check if there is a direct defined 'token storage'
            $refreshTokenStorage = Utilities::storageLookup(
                $serverKey,
                $options->getRefreshTokenStorage() ?: $options->getStorage(),
                $container->get('OAuth2Provider/Containers/StorageContainer'),
                $container,
                RefreshTokenFactory::IDENTIFIER
            );

            if (empty($refreshTokenStorage)) {
                throw new Exception\InvalidServerException(sprintf(
                    "Class '%s' error: storage of type '%s' is required for '%s'",
                    __METHOD__,
                    RefreshTokenFactory::IDENTIFIER,
                    $refreshTokenClassName
                ));
            }

            return new $refreshTokenClassName($refreshTokenStorage, $options->getConfigs());
        };
    }
}
