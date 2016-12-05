<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Core\Service\BlockService;
use Netgen\BlockManager\Core\Service\CollectionService;
use Netgen\BlockManager\Core\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithoutCollection;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use PHPUnit\Framework\TestCase;

abstract class ServiceTestCase extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    protected $conditionTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $parameterTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected $blockMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected $collectionMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    protected $layoutResolverMapper;

    public function setUp()
    {
        $this->prepareRegistries();
        $this->preparePersistence();
    }

    /**
     * Prepares the persistence handler used in tests.
     */
    abstract public function preparePersistence();

    /**
     * Prepares the registries used in tests.
     */
    protected function prepareRegistries()
    {
        $layoutType1 = new LayoutType(
            array(
                'identifier' => '4_zones_a',
                'zones' => array(
                    'top' => new Zone(),
                    'left' => new Zone(),
                    'right' => new Zone(array('allowedBlockDefinitions' => array('title', 'list'))),
                    'bottom' => new Zone(array('allowedBlockDefinitions' => array('title'))),
                ),
            )
        );

        $layoutType2 = new LayoutType(
            array(
                'identifier' => '4_zones_b',
                'zones' => array(
                    'top' => new Zone(),
                    'left' => new Zone(),
                    'right' => new Zone(),
                    'bottom' => new Zone(),
                ),
            )
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry();
        $this->layoutTypeRegistry->addLayoutType($layoutType1);
        $this->layoutTypeRegistry->addLayoutType($layoutType2);

        $this->queryTypeRegistry = new QueryTypeRegistry();
        $this->queryTypeRegistry->addQueryType('ezcontent_search', new QueryType('ezcontent_search'));

        $blockDefinition1 = new BlockDefinition('title', array('small' => array('standard')), new BlockDefinitionHandlerWithoutCollection());
        $blockDefinition2 = new BlockDefinition('text', array('standard' => array('standard')), new BlockDefinitionHandlerWithoutCollection());
        $blockDefinition3 = new BlockDefinition('gallery', array('standard' => array('standard')));
        $blockDefinition4 = new BlockDefinition('list', array('standard' => array('standard')));

        $this->blockDefinitionRegistry = new BlockDefinitionRegistry();
        $this->blockDefinitionRegistry->addBlockDefinition('title', $blockDefinition1);
        $this->blockDefinitionRegistry->addBlockDefinition('text', $blockDefinition2);
        $this->blockDefinitionRegistry->addBlockDefinition('gallery', $blockDefinition3);
        $this->blockDefinitionRegistry->addBlockDefinition('list', $blockDefinition4);

        $this->targetTypeRegistry = new TargetTypeRegistry();
        $this->targetTypeRegistry->addTargetType(new TargetType('target'));
        $this->targetTypeRegistry->addTargetType(new TargetType('route'));
        $this->targetTypeRegistry->addTargetType(new TargetType('route_prefix'));
        $this->targetTypeRegistry->addTargetType(new TargetType('path_info'));
        $this->targetTypeRegistry->addTargetType(new TargetType('path_info_prefix'));
        $this->targetTypeRegistry->addTargetType(new TargetType('request_uri'));
        $this->targetTypeRegistry->addTargetType(new TargetType('request_uri_prefix'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ezcontent'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ezlocation'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ezchildren'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ezsubtree'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ez_semantic_path_info'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ez_semantic_path_info_prefix'));

        $this->conditionTypeRegistry = new ConditionTypeRegistry();
        $this->conditionTypeRegistry->addConditionType(new ConditionType('condition'));
        $this->conditionTypeRegistry->addConditionType(new ConditionType('ez_site_access'));
        $this->conditionTypeRegistry->addConditionType(new ConditionType('route_parameter'));

        $this->parameterTypeRegistry = new ParameterTypeRegistry();
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IntegerType());
    }

    /**
     * Creates a layout service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    protected function createLayoutService(LayoutValidator $validator = null)
    {
        if ($validator === null) {
            $validator = $this->createMock(LayoutValidator::class);
        }

        return new LayoutService(
            $validator,
            $this->createLayoutMapper(),
            $this->persistenceHandler
        );
    }

    /**
     * Creates a block service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    protected function createBlockService(BlockValidator $validator = null)
    {
        if ($validator === null) {
            $validator = $this->createMock(BlockValidator::class);
        }

        return new BlockService(
            $validator,
            $this->createBlockMapper(),
            $this->createParameterMapper(),
            $this->persistenceHandler,
            $this->layoutTypeRegistry
        );
    }

    /**
     * Creates a collection service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\CollectionService
     */
    protected function createCollectionService(CollectionValidator $validator = null)
    {
        if ($validator === null) {
            $validator = $this->createMock(CollectionValidator::class);
        }

        return new CollectionService(
            $validator,
            $this->createCollectionMapper(),
            $this->createParameterMapper(),
            $this->persistenceHandler
        );
    }

    /**
     * Creates a layout resolver service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected function createLayoutResolverService(LayoutResolverValidator $validator = null)
    {
        if ($validator === null) {
            $validator = $this->createMock(LayoutResolverValidator::class);
        }

        return new LayoutResolverService(
            $validator,
            $this->createLayoutResolverMapper(),
            $this->persistenceHandler
        );
    }

    /**
     * Creates a layout mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected function createLayoutMapper()
    {
        return new LayoutMapper(
            $this->createBlockMapper(),
            $this->persistenceHandler,
            $this->layoutTypeRegistry
        );
    }

    /**
     * Creates a block mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected function createBlockMapper()
    {
        return new BlockMapper(
            $this->persistenceHandler,
            $this->createCollectionMapper(),
            $this->createParameterMapper(),
            $this->blockDefinitionRegistry
        );
    }

    /**
     * Creates a collection mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected function createCollectionMapper()
    {
        return new CollectionMapper(
            $this->persistenceHandler,
            $this->createParameterMapper(),
            $this->queryTypeRegistry
        );
    }

    /**
     * Creates a layout resolver mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    protected function createLayoutResolverMapper()
    {
        return new LayoutResolverMapper(
            $this->persistenceHandler,
            $this->createLayoutMapper(),
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry
        );
    }

    /**
     * Creates the parameter mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected function createParameterMapper()
    {
        return new ParameterMapper();
    }
}