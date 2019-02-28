<?php
namespace OAuth2Provider\Service\Factory\ServerFeature;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Builder\StrategyBuilder;
use OAuth2Provider\Service\Factory\GrantTypeStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class GrantTypeFactory implements FactoryInterface
{
    /**
     * List of available strategies
     * @var array
     */
    protected $availableStrategy = array(
        GrantTypeStrategy\AuthorizationCodeFactory::IDENTIFIER => 'OAuth2Provider/GrantTypeStrategy/AuthorizationCode',
        GrantTypeStrategy\ClientCredentialsFactory::IDENTIFIER => 'OAuth2Provider/GrantTypeStrategy/ClientCredentials',
        //'jwt_bearer'         => 'OAuth2Provider/GrantTypeStrategy/JwtBearer',
        GrantTypeStrategy\RefreshTokenFactory::IDENTIFIER      => 'OAuth2Provider/GrantTypeStrategy/RefreshToken' ,
        GrantTypeStrategy\UserCredentialsFactory::IDENTIFIER   => 'OAuth2Provider/GrantTypeStrategy/UserCredentials',
    );

    /**
     * Concrete FQNS implementation of grant types taken from OAuthServer
     * @var array
     */
    protected $concreteClasses = array(
        GrantTypeStrategy\AuthorizationCodeFactory::IDENTIFIER => 'OAuth2\GrantType\AuthorizationCode',
        GrantTypeStrategy\ClientCredentialsFactory::IDENTIFIER => 'OAuth2\GrantType\ClientCredentials',
        //'jwt_bearer'         => 'OAuth2\GrantType\JwtBearer',
        GrantTypeStrategy\RefreshTokenFactory::IDENTIFIER      => 'OAuth2\GrantType\RefreshToken',
        GrantTypeStrategy\UserCredentialsFactory::IDENTIFIER   => 'OAuth2\GrantType\UserCredentials',
    );

    /**
     * The interface to validate against
     * @var string FQNS
     */
    protected $strategyInterface = 'OAuth2\GrantType\GrantTypeInterface';

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

        return function ($strategyTypes, $serverKey) use ($container, $strategies, $concreteClasses, $interface) {
            $strategy = new StrategyBuilder(
                $strategyTypes,
                $serverKey,
                $strategies,
                $concreteClasses,
                $container->get('OAuth2Provider/Containers/GrantTypeContainer'),
                $interface
            );
            return $strategy->initStrategyFeature($container);
        };
    }
}
