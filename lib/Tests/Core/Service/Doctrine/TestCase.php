<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Core\Service\CollectionService;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCase as PersistenceTestCase;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Service\BlockService;

trait TestCase
{
    use PersistenceTestCase;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * Prepares the prerequisites for using services in tests.
     */
    public function preparePersistence()
    {
        $this->prepareHandlers();

        $this->persistenceHandler = $this->createPersistenceHandler();
    }

    /**
     * Creates a layout service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry $layoutTypeRegistry
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    protected function createLayoutService(LayoutValidator $validator, LayoutTypeRegistry $layoutTypeRegistry)
    {
        return new LayoutService(
            $validator,
            $this->createLayoutMapper(),
            $this->persistenceHandler,
            $layoutTypeRegistry
        );
    }

    /**
     * Creates a block service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry $layoutTypeRegistry
     *
     * @return \Netgen\BlockManager\Core\Service\BlockService
     */
    protected function createBlockService(BlockValidator $validator, LayoutTypeRegistry $layoutTypeRegistry)
    {
        return new BlockService(
            $validator,
            $this->createBlockMapper(),
            $this->persistenceHandler,
            $layoutTypeRegistry
        );
    }

    /**
     * Creates a collection service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $validator
     *
     * @return \Netgen\BlockManager\Core\Service\CollectionService
     */
    protected function createCollectionService(CollectionValidator $validator)
    {
        return new CollectionService(
            $validator,
            $this->createCollectionMapper(),
            $this->persistenceHandler
        );
    }

    /**
     * Creates the block mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected function createBlockMapper()
    {
        return new BlockMapper(
            $this->persistenceHandler
        );
    }

    /**
     * Creates the layout mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected function createLayoutMapper()
    {
        return new LayoutMapper(
            $this->createBlockMapper(),
            $this->persistenceHandler
        );
    }

    /**
     * Creates the collection mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected function createCollectionMapper()
    {
        return new CollectionMapper(
            $this->persistenceHandler
        );
    }
}
