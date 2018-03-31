<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\CollectionRunnerFactory;
use Netgen\BlockManager\Collection\Result\DynamicCollectionRunner;
use Netgen\BlockManager\Collection\Result\ManualCollectionRunner;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class CollectionRunnerFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemBuilderMock;

    /**
     * @var \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory
     */
    private $factory;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);

        $this->factory = new CollectionRunnerFactory(
            $this->itemLoaderMock,
            $this->itemBuilderMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::getCollectionRunner
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::getQueryRunner
     */
    public function testGetCollectionRunnerWithManualCollection()
    {
        $runner = $this->factory->getCollectionRunner(new Collection());

        $this->assertInstanceOf(ManualCollectionRunner::class, $runner);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::getCollectionRunner
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::getQueryRunner
     */
    public function testGetCollectionRunnerWithDynamicCollection()
    {
        $runner = $this->factory->getCollectionRunner(
            new Collection(
                array(
                    'query' => new Query(
                        array(
                            'queryType' => new QueryType('type'),
                        )
                    ),
                )
            )
        );

        $this->assertInstanceOf(DynamicCollectionRunner::class, $runner);
    }
}