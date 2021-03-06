<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter;
use PHPUnit\Framework\TestCase;

final class ConditionParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new ConditionParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['conditionId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('condition', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Condition::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $condition = new Condition();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadCondition')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($condition));

        self::assertSame(
            $condition,
            $this->paramConverter->loadValue(
                [
                    'conditionId' => 42,
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $condition = new Condition();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadConditionDraft')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($condition));

        self::assertSame(
            $condition,
            $this->paramConverter->loadValue(
                [
                    'conditionId' => 42,
                    'status' => 'draft',
                ]
            )
        );
    }
}
