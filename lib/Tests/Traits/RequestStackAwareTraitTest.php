<?php

namespace Netgen\BlockManager\Tests\Traits;

use Netgen\BlockManager\Tests\Traits\Stubs\RequestStackAwareValue;
use Symfony\Component\HttpFoundation\RequestStack;
use PHPUnit\Framework\TestCase;

class RequestStackAwareTraitTest extends TestCase
{
    public function testDefaultRequestStackValue()
    {
        $value = new RequestStackAwareValue();
        self::assertNull($value->getRequestStack());
    }

    /**
     * @covers \Netgen\BlockManager\Traits\RequestStackAwareTrait::setRequestStack
     */
    public function testSetRequestStack()
    {
        $requestStack = new RequestStack();

        $value = new RequestStackAwareValue();
        $value->setRequestStack($requestStack);

        self::assertEquals($requestStack, $value->getRequestStack());
    }
}
