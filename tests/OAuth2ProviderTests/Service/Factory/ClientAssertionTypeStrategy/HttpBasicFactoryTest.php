<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\StorageContainer;
use OAuth2Provider\Options\ClientAssertionType\HttpBasicConfigurations;
use OAuth2Provider\Service\Factory\ClientAssertionTypeStrategy\HttpBasicFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * HttpBasicFactory test case.
 */
class HttpBasicFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var HttpBasicFactory
     */
    private $HttpBasicFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->HttpBasicFactory = new HttpBasicFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->HttpBasicFactory = null;
        parent::tearDown();
    }

    /**
     * Tests HttpBasicFactory->createService()
     */
    public function testCreateService()
    {
        $sm = new ServiceManager();

        $sm->setService('OAuth2Provider/Options/ClientAssertionType/HttpBasic', new HttpBasicConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());

        $serverKey = uniqid();
        $options = array(
            'client_credentials_storage' => new \OAuth2ProviderTests\Assets\Storage\ClientCredentialsStorage(),
            'configs' => array(
                'allow_credentials_in_request_body' => false,
            )
        );

        $s = $this->HttpBasicFactory->createService($sm);
        $r = $s('OAuth2\ClientAssertionType\HttpBasic', $options, $serverKey);

        $this->assertInstanceOf('OAuth2\ClientAssertionType\ClientAssertionTypeInterface', $r);
    }

    /**
     * @group test2
     * @expectedException \OAuth2Provider\Exception\InvalidServerException
     */
    public function testCreateServiceReturnsException()
    {
        $serverKey = uniqid();
        $sm = new ServiceManager();

        $sm->setService('OAuth2Provider/Options/ClientAssertionType/HttpBasic', new HttpBasicConfigurations());
        $sm->setService('OAuth2Provider/Containers/StorageContainer', new StorageContainer());

        $options = array(
            'configs' => array(
                'allow_credentials_in_request_body' => false,
            )
        );

        $s = $this->HttpBasicFactory->createService($sm);
        $r = $s('OAuth2\ClientAssertionType\HttpBasic', $options, $serverKey);
    }
}
