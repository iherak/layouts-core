<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Value;

use Netgen\BlockManager\Parameters\Value\LinkValue;
use PHPUnit\Framework\TestCase;

final class LinkValueTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getLink
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getLinkSuffix
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getLinkType
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getNewWindow
     */
    public function testSetProperties(): void
    {
        $linkValue = LinkValue::fromArray(
            [
                'linkType' => LinkValue::LINK_TYPE_EMAIL,
                'link' => 'mail@example.com',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ]
        );

        self::assertSame(LinkValue::LINK_TYPE_EMAIL, $linkValue->getLinkType());
        self::assertSame('mail@example.com', $linkValue->getLink());
        self::assertSame('?suffix', $linkValue->getLinkSuffix());
        self::assertTrue($linkValue->getNewWindow());
    }
}
