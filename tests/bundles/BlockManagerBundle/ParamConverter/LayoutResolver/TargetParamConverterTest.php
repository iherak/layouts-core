<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter;
use PHPUnit\Framework\TestCase;

final class TargetParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new TargetParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['targetId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('target', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Target::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $target = new Target();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadTarget')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($target));

        self::assertSame(
            $target,
            $this->paramConverter->loadValue(
                [
                    'targetId' => 42,
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $target = new Target();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadTargetDraft')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($target));

        self::assertSame(
            $target,
            $this->paramConverter->loadValue(
                [
                    'targetId' => 42,
                    'status' => 'draft',
                ]
            )
        );
    }
}
