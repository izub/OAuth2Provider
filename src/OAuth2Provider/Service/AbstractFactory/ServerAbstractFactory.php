<?php
namespace OAuth2Provider\Service\AbstractFactory;

use Interop\Container\ContainerInterface;
use OAuth2\Server as OAuth2Server;
use OAuth2Provider\Exception;
use OAuth2Provider\ServerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ServerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var string
     */
    const REGEX_SERVER_PATTERN = '~^oauth2provider.server.([a-zA-Z0-9_]+)$~';

    /**
     * @var array
     */
    protected $serverConfig;

    /**
     * @var string
     */
    protected $serverKey;

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        // for performance, do a prelim check before checking against regex
        if (0 !== strpos($requestedName, 'oauth2provider.server.')) {
            return false;
        }

        if (preg_match(static::REGEX_SERVER_PATTERN, $requestedName, $serverKeyMatch)
            && !empty($serverKeyMatch[1])
        ) {
            $serverKey = $serverKeyMatch[1];

            $configs       = $container->get('OAuth2Provider/Options/Configuration');
            $serverConfigs = $configs->getServers();
            if (isset($serverConfigs[$serverKey])) {
                $this->serverKey = $serverKey;

                // checks for a version. If no version exists use the first server found
                $mvcEvent = $container->get('Application')->getMvcEvent();
                if (isset($mvcEvent) && null !== $mvcEvent->getRouteMatch()) {
                    $version = $mvcEvent->getRouteMatch()->getParam('version');
                }
                if (empty($version)) {
                    $version = $configs->getMainVersion();
                }

                if (null !== $version) {
                    if (!empty($serverConfigs[$serverKey]['version']) && $version === $serverConfigs[$serverKey]['version']) {
                        $this->serverConfig = $serverConfigs[$serverKey];
                        return true;
                    } else {
                        foreach ($serverConfigs[$serverKey] as $storage) {
                            if (!empty($storage['version']) && $storage['version'] === $version) {
                                $this->serverConfig = $storage;
                                return true;
                            }
                        }
                    }
                }

                $this->serverConfig = $serverConfigs[$serverKey];
                return true;
            }

            throw new Exception\InvalidServerException(sprintf(
                "Class '%s' error: server configuration '%s' does not exist",
                __METHOD__,
                $serverKey
            ));
        }

        return false;
    }

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get('OAuth2Provider/Options/Server')->setFromArray($this->serverConfig);

        $server = $options->getServerClass();
        $server = new $server();

        $storage      = $container->get('OAuth2Provider/Service/ServerFeature/StorageFactory');
        $config       = $container->get('OAuth2Provider/Service/ServerFeature/ConfigFactory');
        $grantType    = $container->get('OAuth2Provider/Service/ServerFeature/GrantTypeFactory');
        $responseType = $container->get('OAuth2Provider/Service/ServerFeature/ResponseTypeFactory');
        $tokenType    = $container->get('OAuth2Provider/Service/ServerFeature/TokenTypeFactory');
        $scopeType    = $container->get('OAuth2Provider/Service/ServerFeature/ScopeTypeFactory');
        $clientAssertionType = $container->get('OAuth2Provider/Service/ServerFeature/ClientAssertionTypeFactory');

        $ouath2server = new OAuth2Server(
            $storage($options->getStorages(), $this->serverKey),
            $config($options->getConfigs(), $this->serverKey),
            $grantType($options->getGrantTypes(), $this->serverKey),
            $responseType($options->getResponseTypes(), $this->serverKey),
            $tokenType($options->getTokenType(), $this->serverKey),
            $scopeType($options->getScopeUtil(), $this->serverKey),
            $clientAssertionType($options->getClientAssertionType(), $this->serverKey)
        );

        if ($server instanceof ServerInterface) {
            $server->setOAuth2Server($ouath2server);
            $server->setRequest($container->get('oauth2provider.server.main.request'));
            $server->setResponse($container->get('oauth2provider.server.main.response'));
            return $server;
        }

        return $ouath2server;
    }
}
