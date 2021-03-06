<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\IdentifierMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class IdentifierMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\IdentifierMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new IdentifierMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\IdentifierMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
