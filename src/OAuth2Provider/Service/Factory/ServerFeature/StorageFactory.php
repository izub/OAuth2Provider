<?php
namespace OAuth2Provider\Service\Factory\ServerFeature;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Exception;
use OAuth2Provider\Lib\Utilities;
use Zend\ServiceManager\Factory\FactoryInterface;

class StorageFactory implements FactoryInterface
{
    /**
     * Valid storage name keys
     * @var array
     */
    protected $storageNames = array(
        'access_token',
        'authorization_code',
        'client_credentials',
        'client',
        'refresh_token',
        'user_credentials',
        'jwt_bearer',
        'scope',
    );

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
        $storageNames = $this->storageNames;
        return function ($storages, $serverKey) use ($container, $storageNames) {

            /** @var StorageContainer $storageContainer */
            $storageContainer = $container->get('OAuth2Provider/Containers/StorageContainer');
            foreach ($storages as $storageName => $storage) {
                if (!in_array($storageName, $storageNames)) {
                    throw new Exception\InvalidConfigException(sprintf(
                        "Class '%s': the storage config '%s' is not valid",
                        __METHOD__,
                        $storageName
                    ));
                }

                $storageObj = Utilities::createClass($storage, $container, sprintf(
                    "Class '%s' does not exist.",
                    is_object($storage) ? get_class($storage) : $storage
                ));
                $storageContainer[$serverKey][$storageName] = $storageObj;
            }

            return $storageContainer->getServerContents($serverKey);
        };
    }
}
