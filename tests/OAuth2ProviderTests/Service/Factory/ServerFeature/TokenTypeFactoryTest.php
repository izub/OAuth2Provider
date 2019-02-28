<?php
namespace OAuth2ProviderTests;

use OAuth2Provider\Containers\TokenTypeContainer;
use OAuth2Provider\Options\ServerFeatureTypeConfiguration;
use OAuth2Provider\Options\TokenType\BearerConfigurations;
use OAuth2Provider\Service\Factory\ServerFeature\TokenTypeFactory;
use OAuth2Provider\Service\Factory\TokenTypeStrategy\BearerFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * TokenTypeFactory test case.
 */
class TokenTypeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TokenTypeFactory
     */
    private $TokenTypeFactory;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->TokenTypeFactory = new TokenTypeFactory(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->TokenTypeFactory = null;
        parent::tearDown();
    }

    /**
     * Tests TokenTypeFactory->createService()
     * @group test1
     */
    public function testCreateServiceWithConfigAsDirectName()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());
        $mainSm->setService('OAuth2Provider/TokenTypeStrategy/Bearer', (new BearerFactory())->createService($mainSm));
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $config = 'bearer';

        $ser = $this->TokenTypeFactory->createService($mainSm);
        $r = $ser($config, 'server3');
        $this->assertInstanceOf('OAuth2\TokenType\TokenTypeInterface', $r);
    }

    /**
     * Tests TokenTypeFactory->createService()
     * @group test2
     */
    public function testCreateServiceWithConfigAsDirectInsideArray()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());
        $mainSm->setService('OAuth2Provider/TokenTypeStrategy/Bearer', (new BearerFactory())->createService($mainSm));
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $config = array('bearer');

        $ser = $this->TokenTypeFactory->createService($mainSm);
        $r = $ser($config, 'server3');
        $this->assertInstanceOf('OAuth2\TokenType\TokenTypeInterface', $r);
    }

    /**
     * Tests TokenTypeFactory->createService()
     * @group test3
     */
    public function testCreateServiceWithConfigAsDirectWithNameInsideArray()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());
        $mainSm->setService('OAuth2Provider/TokenTypeStrategy/Bearer', (new BearerFactory())->createService($mainSm));
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $config = array(
            'name' => 'bearer',
        );

        $ser = $this->TokenTypeFactory->createService($mainSm);
        $r = $ser($config, 'server3');
        $this->assertInstanceOf('OAuth2\TokenType\TokenTypeInterface', $r);
    }

    /**
     * Tests TokenTypeFactory->createService()
     * @group test4
     */
    public function testCreateServiceWithConfigAsArrayWithNameAndOptions()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());
        $mainSm->setService('OAuth2Provider/TokenTypeStrategy/Bearer', (new BearerFactory())->createService($mainSm));
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $config = array(
            array(
                'name' => 'bearer',
                'options' => array(
                    'configs' => array(
                        'token_bearer_header_name' => 'franz',
                    ),
                ),
            ),
        );

        $ser = $this->TokenTypeFactory->createService($mainSm);
        $r = $ser($config, 'server3');
        $this->assertInstanceOf('OAuth2\TokenType\TokenTypeInterface', $r);
    }

    /**
     * Tests TokenTypeFactory->createService()
     * @group test5
     */
    public function testCreateServiceWithConfigAsDirectWithNameAndOptions()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());
        $mainSm->setService('OAuth2Provider/TokenTypeStrategy/Bearer', (new BearerFactory())->createService($mainSm));
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $config = array(
            'name' => 'bearer',
            'options' => array(
                'configs' => array(
                    'token_bearer_header_name' => 'franz',
                ),
            ),
        );

        $ser = $this->TokenTypeFactory->createService($mainSm);
        $r = $ser($config, 'server3');
        $this->assertInstanceOf('OAuth2\TokenType\TokenTypeInterface', $r);
    }

    /**
     * Tests TokenTypeFactory->createService()
     * @group test6
     */
    public function testCreateServiceWithConfigAsWithNameArrayInsideArray()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());
        $mainSm->setService('OAuth2Provider/TokenTypeStrategy/Bearer', (new BearerFactory())->createService($mainSm));
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $config = array(
            array(
                'name' => 'bearer'
            ),
        );

        $ser = $this->TokenTypeFactory->createService($mainSm);
        $r = $ser($config, 'server3');
        $this->assertInstanceOf('OAuth2\TokenType\TokenTypeInterface', $r);
    }

    /**
     * Tests TokenTypeFactory->createService()
     * @group test7
     */
    public function testCreateServiceWithConfigAsWithNameArrayInsideArrayWithMultipleInputs()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());
        $mainSm->setService('OAuth2Provider/TokenTypeStrategy/Bearer', (new BearerFactory())->createService($mainSm));
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $config = array(
            array(
                'name' => 'bearer'
            ),
            array(
                'name' => 'bearer'
            ),
        );

        $ser = $this->TokenTypeFactory->createService($mainSm);
        $r = $ser($config, 'server3');
        $this->assertInstanceOf('OAuth2\TokenType\TokenTypeInterface', $r);
    }

    /**
     * Tests TokenTypeFactory->createService()
     * @group test8
     */
    public function testCreateServiceWithConfigIsNull()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());
        $mainSm->setService('OAuth2Provider/TokenTypeStrategy/Bearer', (new BearerFactory())->createService($mainSm));
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $config = null;

        $ser = $this->TokenTypeFactory->createService($mainSm);
        $r = $ser($config, 'server3');
        $this->assertNull($r);
    }

    /**
     * Tests TokenTypeFactory->createService()
     * @group test8
     */
    public function testCreateServiceWithConfigIsEmpty()
    {
        $mainSm = new ServiceManager();
        $mainSm->setService('OAuth2Provider/Containers/TokenTypeContainer', new TokenTypeContainer());
        $mainSm->setService('OAuth2Provider/Options/TokenType/Bearer', new BearerConfigurations());
        $mainSm->setService('OAuth2Provider/TokenTypeStrategy/Bearer', (new BearerFactory())->createService($mainSm));
        $mainSm->setService('OAuth2Provider/Options/ServerFeatureType', new ServerFeatureTypeConfiguration());
        $config = array();

        $ser = $this->TokenTypeFactory->createService($mainSm);
        $r = $ser($config, 'server3');
        $this->assertNull($r);
    }
}

