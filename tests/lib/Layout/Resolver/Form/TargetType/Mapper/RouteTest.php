<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\Route;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class RouteTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new Route();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\Route::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
