<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\ClientAssertionTypeContainer;
use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\ClientAssertionType\HttpBasicConfigurations;
use OAuth2Provider\Options\ServerFeatureTypeConfiguration;
use OAuth2Provider\Service\Factory\ClientAssertionTypeStrategy\HttpBasicFactory;
use OAuth2Provider\Service\Factory\ServerFeature\ClientAssertionTypeFactory;
use OAuth2ProviderTests\Bootstrap;
use Zend\ServiceManager\ServiceManager;

/**
 * ClientAssertionTypeFactory test case.
 */
class ClientAssertionTypeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ClientAssertionTypeFactory
     */
    private $ClientAssertionTypeFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ClientAssertionTypeFactory = new ClientAssertionTypeFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->ClientAssertionTypeFactory = null;
        parent::tearDown();
    }

    /**
     * Tests ClientAssertionTypeFactory->createService()
     */
    public function testCreateService()
    {
        $sm = new ServiceManager();

        $sm->setService('OAuth2Provider/Containers/ClientAssertionContainer', new ClientAssertionTypeContainer());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());
        $sm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $sm->setService('OAuth2Provider/Options/ClientAssertionType/HttpBasic', new HttpBasicConfigurations());
        $sm->setService('OAuth2Provider/ClientAssertionStrategy/HttpBasic', (new HttpBasicFactory())->createService($sm));

        $serverKey = uniqid();
        $storage = $sm->get('OAuth2Provider/Containers/StorageContainer');
        $storage[$serverKey]['client_credentials'] = new \OAuth2ProviderTests\Assets\Storage\ClientCredentialsStorage();

        $strategies = array(
            'name' => 'http_basic',
            'options' => array(
                'configs' => array(
                    'allow_credentials_in_request_body' => false,
                ),
            ),
        );

        $service = $this->ClientAssertionTypeFactory->createService($sm);
        $r = $service($strategies, $serverKey);
        $this->assertInstanceOf('OAuth2\ClientAssertionType\HttpBasic', $r);
    }
}
