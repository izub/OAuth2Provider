<?php
namespace OAuth2Provider\Service\Factory\GrantTypeStrategy;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Exception;
use OAuth2Provider\Lib\Utilities;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserCredentialsFactory implements FactoryInterface
{
    const IDENTIFIER = 'user_credentials';

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
        return function ($grantTypeClassName, $options, $serverKey) use ($container) {

            $options = $container->get('OAuth2Provider/Options/GrantType/UserCredentials')->setFromArray($options);

            $storage = Utilities::storageLookup(
                $serverKey,
                $options->getUserCredentialsStorage() ?: $options->getStorage(),
                $container->get('OAuth2Provider/Containers/StorageContainer'),
                $container,
                UserCredentialsFactory::IDENTIFIER
            );

            if (empty($storage)) {
                throw new Exception\InvalidServerException(sprintf(
                    "Class '%s' error: storage of type '%s' is required for grant type '%s'",
                    __METHOD__,
                    UserCredentialsFactory::IDENTIFIER,
                    $grantTypeClassName
                ));
            }

            return new $grantTypeClassName($storage);
        };
    }
}
