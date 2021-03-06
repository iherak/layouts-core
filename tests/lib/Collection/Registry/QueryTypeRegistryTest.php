<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Registry;

use ArrayIterator;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface
     */
    private $queryType1;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface
     */
    private $queryType2;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry
     */
    private $registry;

    public function setUp(): void
    {
        $this->queryType1 = new QueryType('query_type1');
        $this->queryType2 = new QueryType('query_type2', [], null, false, false);

        $this->registry = new QueryTypeRegistry(
            [
                'query_type1' => $this->queryType1,
                'query_type2' => $this->queryType2,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getQueryTypes
     */
    public function testGetEnabledQueryTypes(): void
    {
        self::assertSame(
            [
                'query_type1' => $this->queryType1,
            ],
            $this->registry->getQueryTypes(true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::__construct
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getQueryTypes
     */
    public function testGetQueryTypes(): void
    {
        self::assertSame(
            [
                'query_type1' => $this->queryType1,
                'query_type2' => $this->queryType2,
            ],
            $this->registry->getQueryTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getQueryType
     */
    public function testGetQueryType(): void
    {
        self::assertSame($this->queryType1, $this->registry->getQueryType('query_type1'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getQueryType
     */
    public function testGetQueryTypeThrowsQueryTypeException(): void
    {
        $this->expectException(QueryTypeException::class);
        $this->expectExceptionMessage('Query type with "other_query_type" identifier does not exist.');

        $this->registry->getQueryType('other_query_type');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::hasQueryType
     */
    public function testHasQueryType(): void
    {
        self::assertTrue($this->registry->hasQueryType('query_type1'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::hasQueryType
     */
    public function testHasQueryTypeWithNoQueryType(): void
    {
        self::assertFalse($this->registry->hasQueryType('other_query_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $queryTypes = [];
        foreach ($this->registry as $identifier => $queryType) {
            $queryTypes[$identifier] = $queryType;
        }

        self::assertSame($this->registry->getQueryTypes(), $queryTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(2, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('query_type1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->queryType1, $this->registry['query_type1']);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['query_type1'] = $this->queryType1;
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['query_type1']);
    }
}
