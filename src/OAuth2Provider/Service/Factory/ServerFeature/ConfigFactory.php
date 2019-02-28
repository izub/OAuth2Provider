<?php
namespace OAuth2Provider\Service\Factory\ServerFeature;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConfigFactory implements FactoryInterface
{
    /**
     * For reference only
     * @var array
     */
    protected $defaultConfigs = array(
        'access_lifetime'            => 3600,
        'www_realm'                  => 'Service',
        'token_param_name'           => 'access_token',
        'token_bearer_header_name'   => 'Bearer',
        'enforce_state'              => true,
        'require_exact_redirect_uri' => true,
        'allow_implicit'             => false,
        'allow_credentials_in_request_body' => true,
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
        return function ($configs, $serverKey) use ($container) {
            $configContainer = $container->get('OAuth2Provider/Containers/ConfigContainer');
            $configContainer[$serverKey] = $configs;

            return $configContainer->getServerContents($serverKey);
        };
    }
}
