<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler\LayoutResolver\TargetHandler;

use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\TestCase;

abstract class AbstractTargetHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler
     */
    protected $handler;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->createDatabase();

        $this->handler = new LayoutResolverHandler(
            new LayoutResolverQueryHandler(
                $this->databaseConnection,
                new ConnectionHelper($this->databaseConnection),
                array($this->getTargetIdentifier() => $this->getTargetHandler())
            ),
            new LayoutResolverMapper()
        );
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabase();
    }

    /**
     * Returns the target identifier under test.
     *
     * @return string
     */
    abstract protected function getTargetIdentifier();

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler
     */
    abstract protected function getTargetHandler();
}