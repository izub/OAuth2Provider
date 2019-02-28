<?php
namespace OAuth2Provider\Service\AbstractFactory;

use Interop\Container\ContainerInterface;
use OAuth2\Response;
use OAuth2Provider\Exception;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ResponseAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var string
     */
    const REGEX_RESPONSE_PATTERN = '~^oauth2provider.server.([a-zA-Z0-9_]+).response$~';

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

        if (preg_match(static::REGEX_RESPONSE_PATTERN, $requestedName, $matches)
            && !empty($matches[1])
        ) {
            $this->serverKey = ($matches[1] === 'main')
                ? $container->get('OAuth2Provider/Options/Configuration')->getMainServer()
                : $matches[1];

            if ($container->has("oauth2provider.server.{$this->serverKey}")) {
                return true;
            } else {
                throw new Exception\ErrorException(sprintf(
                    "Error '%s': server '%s' is not initialized yet",
                    __METHOD__,
                    "oauth2provider.server.{$this->serverKey}"
                ));
            }
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
        $responseContainer = $container->get('OAuth2Provider/Containers/ResponseContainer');
        $responseContainer[$this->serverKey] = new Response();

        return $responseContainer->getServerContents($this->serverKey);
    }
}
