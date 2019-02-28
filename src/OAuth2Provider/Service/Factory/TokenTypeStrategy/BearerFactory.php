<?php
namespace OAuth2Provider\Service\Factory\TokenTypeStrategy;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Exception;
use Zend\ServiceManager\Factory\FactoryInterface;

class BearerFactory implements FactoryInterface
{
    /**
     * Identifiers
     * This will be used for defaults
     * @var string
     */
    const IDENTIFIER = 'bearer';

    /**
     * Accepted config keys for bearer
     * @var array
     */
    protected $acceptedKeys = array(
        'token_param_name',
        'token_bearer_header_name',
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
        $acceptedKeys = $this->acceptedKeys;
        return function ($bearerClassName, $options, $serverKey) use ($container, $acceptedKeys) {
            $options = $container->get('OAuth2Provider/Options/TokenType/Bearer')->setFromArray($options);
            $configs = $options->getConfigs();

            foreach (array_keys($configs) as $key) {
                if (!in_array($key, $acceptedKeys)) {
                    throw new Exception\InvalidServerException(sprintf(
                        "Class '%s' error: configuration '%s' is not valid. Should be one of: ['%s']",
                        __METHOD__,
                        $key,
                        implode("', '", $acceptedKeys)
                    ));
                }
            }

            return new $bearerClassName($configs);
        };
    }
}
