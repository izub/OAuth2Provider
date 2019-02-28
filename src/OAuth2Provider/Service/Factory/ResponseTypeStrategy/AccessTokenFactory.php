<?php
namespace OAuth2Provider\Service\Factory\ResponseTypeStrategy;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Exception;
use OAuth2Provider\Lib\Utilities;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccessTokenFactory implements FactoryInterface
{
    /**
     * Main identifier
     * @var string
     */
    const IDENTIFIER = 'access_token';

    /**
     * Storage identifiers
     * @var string
     */
    const ACCESS_TOKEN_IDENTIFIER = 'access_token';
    const REFRESH_TOKEN_IDENTIFIER = 'refresh_token';

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
        return function ($accessTokenClassName, $options, $serverKey) use ($container) {

            $storageContainer = $container->get('OAuth2Provider/Containers/StorageContainer');
            $options = $container->get('OAuth2Provider/Options/ResponseType/AccessToken')->setFromArray($options);

            $tokenStorageName        = $options->getTokenStorage() ?: $options->getStorage();
            $refreshTokenStorageName = $options->getRefreshStorage();

            // check if there is a direct defined 'token storage'
            $tokenStorage = Utilities::storageLookup(
                $serverKey,
                $tokenStorageName,
                $storageContainer,
                $container,
                AccessTokenFactory::ACCESS_TOKEN_IDENTIFIER
            );

            // check if there is a direct defined 'refresh token'
            $refreshTokenStorage = Utilities::storageLookup(
                $serverKey,
                $refreshTokenStorageName,
                $storageContainer,
                $container,
                AccessTokenFactory::REFRESH_TOKEN_IDENTIFIER
            );

            if (empty($tokenStorage)) {
                throw new Exception\InvalidServerException(sprintf(
                    "Class '%s' error: storage of type '%s' is required for Access Token '%s'",
                    __METHOD__,
                    AccessTokenFactory::ACCESS_TOKEN_IDENTIFIER,
                    $accessTokenClassName
                ));
            }

            return new $accessTokenClassName($tokenStorage, $refreshTokenStorage, $options->getConfigs());
        };
    }
}
