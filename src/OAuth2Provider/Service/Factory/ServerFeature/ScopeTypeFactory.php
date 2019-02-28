<?php
namespace OAuth2Provider\Service\Factory\ServerFeature;

use Interop\Container\ContainerInterface;
use OAuth2Provider\Builder\StrategyBuilder;
use OAuth2Provider\Lib\Utilities;
use OAuth2Provider\Service\Factory\ScopeStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class ScopeTypeFactory implements FactoryInterface
{
    /**
     * List of available strategies
     * @var array
     */
    protected $availableStrategies = array(
        ScopeStrategy\ScopeFactory::IDENTIFIER => 'OAuth2Provider/ScopeStrategy/Scope',
    );

    /**
     * Concrete FQNS implementation taken from OAuthServer
     * @var array
    */
    protected $concreteClasses = array(
        ScopeStrategy\ScopeFactory::IDENTIFIER => 'OAuth2\Scope',
    );

    /**
     * The interface to validate against
     * @var string FQNS
    */
    protected $strategyInterface = 'OAuth2\ScopeInterface';

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
        $availableStrategies = $this->availableStrategies;
        $concreteClasses     = $this->concreteClasses;
        $interface           = $this->strategyInterface;

        return function ($strategy, $serverKey) use (
            $availableStrategies,
            $concreteClasses,
            $interface,
            $container
        ) {
            if (!empty($strategy)) {
                $strategy = new StrategyBuilder(
                    Utilities::singleStrategyOptionExtractor($strategy),
                    $serverKey,
                    $availableStrategies,
                    $concreteClasses,
                    $container->get('OAuth2Provider/Containers/ScopeTypeContainer'),
                    $interface
                );
                $strategy = $strategy->initStrategyFeature($container);

                // check if valid, if not explicitly return null
                if (!empty($strategy)) {
                    return array_shift($strategy);
                }
            }
        };
    }
}