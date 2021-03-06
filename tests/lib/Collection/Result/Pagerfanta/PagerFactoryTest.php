<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result\Pagerfanta;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use PHPUnit\Framework\TestCase;

final class PagerFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $resultBuilderMock;

    /**
     * @var \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory
     */
    private $pagerFactory;

    public function setUp(): void
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);

        $this->resultBuilderMock->expects(self::any())
            ->method('build')
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000])));

        $this->pagerFactory = new PagerFactory($this->resultBuilderMock, 200);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     * @dataProvider getPagerProvider
     */
    public function testGetPager(int $startPage, int $currentPage): void
    {
        $pager = $this->pagerFactory->getPager(Collection::fromArray(['offset' => 0, 'limit' => 5]), $startPage);

        self::assertTrue($pager->getNormalizeOutOfRangePages());
        self::assertSame(5, $pager->getMaxPerPage());
        self::assertSame($currentPage, $pager->getCurrentPage());
        self::assertSame(200, $pager->getNbPages());
    }

    public function getPagerProvider(): array
    {
        return [
            [-5, 1],
            [-1, 1],
            [0, 1],
            [1, 1],
            [2, 2],
            [5, 5],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     * @dataProvider getMaxPagesPagerProvider
     */
    public function testGetPagerWithMaxPages(int $maxPages, int $currentPage, int $nbPages): void
    {
        $pager = $this->pagerFactory->getPager(Collection::fromArray(['offset' => 0, 'limit' => 5]), 2, $maxPages);

        self::assertTrue($pager->getNormalizeOutOfRangePages());
        self::assertSame(5, $pager->getMaxPerPage());
        self::assertSame($currentPage, $pager->getCurrentPage());
        self::assertSame($nbPages, $pager->getNbPages());
    }

    public function getMaxPagesPagerProvider(): array
    {
        return [
            [-2, 2, 200],
            [-1, 2, 200],
            [0, 2, 200],
            [1, 1, 1],
            [2, 2, 2],
            [3, 2, 3],
            [4, 2, 4],
            [5, 2, 5],
            [200, 2, 200],
            [250, 2, 200],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     * @dataProvider getPagerWithCollectionLimitProvider
     */
    public function testGetPagerWithCollectionLimit(int $limit, ?int $maxPages, int $maxPerPage, int $nbPages): void
    {
        $pager = $this->pagerFactory->getPager(Collection::fromArray(['offset' => 0, 'limit' => $limit]), 2, $maxPages);

        self::assertTrue($pager->getNormalizeOutOfRangePages());
        self::assertSame($maxPerPage, $pager->getMaxPerPage());
        self::assertSame(2, $pager->getCurrentPage());
        self::assertSame($nbPages, $pager->getNbPages());
    }

    public function getPagerWithCollectionLimitProvider(): array
    {
        return [
            [100, null, 100, 10],
            [100, 3, 100, 3],
            [100, 10, 100, 10],
            [199, null, 199, 6],
            [199, 3, 199, 3],
            [199, 10, 199, 6],
            [200, null, 200, 5],
            [200, 3, 200, 3],
            [200, 10, 200, 5],
            [201, null, 200, 5],
            [201, 3, 200, 3],
            [201, 10, 200, 5],
            [500, null, 200, 5],
            [500, 3, 200, 3],
            [500, 10, 200, 5],
        ];
    }
}
