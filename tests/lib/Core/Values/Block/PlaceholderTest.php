<?php

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use Traversable;

class PlaceholderTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getBlocks
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::hasParameter
     */
    public function testSetDefaultProperties()
    {
        $placeholder = new Placeholder();

        $this->assertNull($placeholder->getIdentifier());
        $this->assertEquals(array(), $placeholder->getBlocks());
        $this->assertEquals(array(), $placeholder->getParameters());
        $this->assertFalse($placeholder->hasParameter('test'));

        try {
            $placeholder->getParameter('test');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getBlocks
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getIterator
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::count
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::offsetExists
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::offsetGet
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::offsetSet
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::offsetUnset
     */
    public function testSetProperties()
    {
        $placeholder = new Placeholder(
            array(
                'identifier' => 42,
                'blocks' => array(new Block()),
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
            )
        );

        $this->assertEquals(42, $placeholder->getIdentifier());
        $this->assertEquals(array(new Block()), $placeholder->getBlocks());
        $this->assertEquals('some_value', $placeholder->getParameter('some_param'));
        $this->assertFalse($placeholder->hasParameter('test'));
        $this->assertTrue($placeholder->hasParameter('some_param'));

        $this->assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $placeholder->getParameters()
        );

        try {
            $placeholder->getParameter('test');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }

        $this->assertInstanceOf(Traversable::class, $placeholder->getIterator());
        $this->assertEquals(array(new Block()), iterator_to_array($placeholder->getIterator()));

        $this->assertCount(1, $placeholder);

        $this->assertTrue(isset($placeholder[0]));
        $this->assertEquals(new Block(), $placeholder[0]);

        try {
            $placeholder[1] = new Block();
            $this->fail('Succeeded in setting a new block to placeholder.');
        } catch (RuntimeException $e) {
            // Do nothing
        }

        try {
            unset($placeholder[0]);
            $this->fail('Succeeded in unsetting a block in placeholder.');
        } catch (RuntimeException $e) {
            // Do nothing
        }
    }
}