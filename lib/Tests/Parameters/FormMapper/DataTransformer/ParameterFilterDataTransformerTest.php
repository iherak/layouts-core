<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\FormMapper\DataTransformer\ParameterFilterDataTransformer;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterFilter;
use PHPUnit\Framework\TestCase;

class ParameterFilterDataTransformerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\DataTransformer\ParameterFilterDataTransformer
     */
    protected $dataTransformer;

    public function setUp()
    {
        $this->dataTransformer = new ParameterFilterDataTransformer(
            array(new ParameterFilter())
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\DataTransformer\ParameterFilterDataTransformer::__construct
     * @covers \Netgen\BlockManager\Parameters\FormMapper\DataTransformer\ParameterFilterDataTransformer::transform
     */
    public function testTransform()
    {
        self::assertEquals(42, $this->dataTransformer->transform(42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\DataTransformer\ParameterFilterDataTransformer::transform
     */
    public function testTransformNullValue()
    {
        self::assertEquals('', $this->dataTransformer->transform(null));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\DataTransformer\ParameterFilterDataTransformer::reverseTransform
     */
    public function testReverseTransform()
    {
        self::assertEquals('cd/ca', $this->dataTransformer->reverseTransform('ac/dc'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\DataTransformer\ParameterFilterDataTransformer::reverseTransform
     */
    public function testReverseTransformEmptyValue()
    {
        self::assertNull($this->dataTransformer->reverseTransform(''));
    }
}