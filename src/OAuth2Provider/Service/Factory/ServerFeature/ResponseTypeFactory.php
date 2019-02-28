<?php
namespace OAuth2Provider\Service\Factory\ServerFeature;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Builder\StrategyBuilder;
use OAuth2Provider\Service\Factory\ResponseTypeStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class ResponseTypeFactory implements FactoryInterface
{
    /**
     * List of available strategies
     * @var array
     */
    protected $availableStrategy = array(
        ResponseTypeStrategy\AccessTokenFactory::IDENTIFIER       => 'OAuth2Provider/GrantTypeStrategy/AccessToken',
        ResponseTypeStrategy\AuthorizationCodeFactory::IDENTIFIER => 'OAuth2Provider/GrantTypeStrategy/AuthorizationCode',
    );

    /**
     * Concrete FQNS implementation of grant types taken from OAuthServer
     * @var array
     */
    protected $concreteClasses = array(
        ResponseTypeStrategy\AccessTokenFactory::IDENTIFIER       => 'OAuth2\ResponseType\AccessToken',
        ResponseTypeStrategy\AuthorizationCodeFactory::IDENTIFIER => 'OAuth2\ResponseType\AuthorizationCode',
    );

    /**
     * Specific configuration mapping to comply with server
     * @var array
     */
    protected $keyMappings = array(
        'access_token'       => 'token',
        'authorization_code' => 'code',
    );

    /**
     * The interface to validate against
     * @var string FQNS
     */
    protected $strategyInterface = 'OAuth2\ResponseType\ResponseTypeInterface';

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
        $strategies      = $this->availableStrategy;
        $concreteClasses = $this->concreteClasses;
        $interface       = $this->strategyInterface;
        $keyMappings     = $this->keyMappings;

        return function ($strategyTypes, $serverKey) use (
            $container,
            $strategies,
            $concreteClasses,
            $interface,
            $keyMappings
        ) {
            $strategy = new StrategyBuilder(
                $strategyTypes,
                $serverKey,
                $strategies,
                $concreteClasses,
                $container->get('OAuth2Provider/Containers/ResponseTypeContainer'),
                $interface
            );

            // map keys to comply with server
            $result = array();
            foreach ($strategy->initStrategyFeature($container) as $key => $val) {
                if (isset($keyMappings[$key])) {
                    $result[$keyMappings[$key]] = $val;
                }
            }
            unset($strategy);

            return $result;
        };
    }
}
