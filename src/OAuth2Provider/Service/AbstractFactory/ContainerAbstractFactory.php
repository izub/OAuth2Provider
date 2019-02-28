<?php
namespace OAuth2Provider\Service\AbstractFactory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ContainerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Pattern example (must be underscore separated):
     *
     *                      [server] [container] [container_key]
     * oauth2provider.server.server1.grant_type.user_credentials
     *
     * @var string
     */
    const REGEX_CONTAINER_PATTERN = '~^oauth2provider.server(?:.([a-zA-Z0-9_]+))(?:.(%s))(?:.([a-zA-Z0-9_]+))*$~';

    /**
     * List of available server containers
     * container keys/concrete classes mappings
     * @var array
     */
    protected $containers = array(
        'config'       => 'OAuth2Provider/Containers/ConfigContainer',
        'grant_type'   => 'OAuth2Provider/Containers/GrantTypeContainer',
        'reponse_type' => 'OAuth2Provider/Containers/ResponseTypeContainer',
        'scope_type'   => 'OAuth2Provider/Containers/ScopeTypeContainer',
        'storage'      => 'OAuth2Provider/Containers/StorageContainer',
        'token_type'   => 'OAuth2Provider/Containers/TokenTypeContainer',
        'client_assertion_type' => 'OAuth2Provider/Containers/ClientAssertionContainer',
    );

    /**
     * Matched Server from pattern
     * @var string
     */
    protected $server;

    /**
     * Matched Container from pattern
     * @var string
     */
    protected $container;

    /**
     * Matched Container Key from pattern
     * Note: not all container accept keys
     * @var string
     */
    protected $containerKey;

    /**
     * Actual container contents
     * @var array
     */
    protected $contents;

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

        $pattern = sprintf(static::REGEX_CONTAINER_PATTERN, implode('|', array_keys($this->containers)));
        if (preg_match($pattern, $requestedName, $matches) && !empty($matches)) {

            $this->serverKey = ($matches[1] === 'main')
                ? $container->get('OAuth2Provider/Options/Configuration')->getMainServer()
                : $matches[1];
            $this->container    = $matches[2];
            $this->containerKey = isset($matches[3]) ? $matches[3] : null;

            // initialize the server
            if (!$container->has("oauth2provider.server.{$this->serverKey}")) {
                $container->get("oauth2provider.server.{$this->serverKey}");
            }

            if (isset($this->containers[$this->container])) {
                $container = $container->get($this->containers[$this->container]);
                if (isset($this->containerKey)) {
                    if ($container->isExistingServerContentInKey($this->serverKey, $this->containerKey)) {
                        $this->contents = $container->getServerContentsFromKey($this->serverKey, $this->containerKey);
                        return true;
                    } else {
                        return false;
                    }
                }

                $this->contents = $container->getServerContents($this->serverKey);
                return true;
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
        return $this->contents;
    }
}
