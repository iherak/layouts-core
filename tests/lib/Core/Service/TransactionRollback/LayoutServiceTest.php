<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;

final class LayoutServiceTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     */
    public function testLinkZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(PersistenceLayout::fromArray(['shared' => false])));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('loadZone')
            ->will(self::returnValue(PersistenceZone::fromArray(['layoutId' => 1])));

        $this->layoutHandler
            ->expects(self::at(2))
            ->method('loadLayout')
            ->will(self::returnValue(PersistenceLayout::fromArray(['shared' => true])));

        $this->layoutHandler
            ->expects(self::at(3))
            ->method('loadZone')
            ->will(self::returnValue(PersistenceZone::fromArray(['layoutId' => 2])));

        $this->layoutHandler
            ->expects(self::at(4))
            ->method('updateZone')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->linkZone(
            Zone::fromArray(['identifier' => 'right', 'status' => Value::STATUS_DRAFT]),
            Zone::fromArray(['identifier' => 'left', 'status' => Value::STATUS_PUBLISHED])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::unlinkZone
     */
    public function testUnlinkZone(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadZone')
            ->will(self::returnValue(new PersistenceZone()));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('updateZone')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->unlinkZone(Zone::fromArray(['identifier' => 'right', 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('layoutNameExists')
            ->will(self::returnValue(false));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('createLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->name = 'Name';
        $layoutCreateStruct->mainLocale = 'en';
        $layoutCreateStruct->layoutType = LayoutType::fromArray(['identifier' => 'layout_type']);

        $this->layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::addTranslation
     */
    public function testAddTranslation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(
                self::returnValue(
                    PersistenceLayout::fromArray(
                        [
                            'mainLocale' => 'en',
                            'availableLocales' => ['en'],
                        ]
                    )
                )
            );

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('createLayoutTranslation')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->addTranslation(Layout::fromArray(['status' => Value::STATUS_DRAFT]), 'hr', 'en');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     */
    public function testRemoveTranslation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(
                self::returnValue(
                    PersistenceLayout::fromArray(
                        [
                            'mainLocale' => 'en',
                            'availableLocales' => ['en', 'hr'],
                        ]
                    )
                )
            );

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('deleteLayoutTranslation')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->removeTranslation(Layout::fromArray(['status' => Value::STATUS_DRAFT]), 'hr');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     */
    public function testUpdateLayout(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('layoutNameExists')
            ->will(self::returnValue(false));

        $this->layoutHandler
            ->expects(self::at(2))
            ->method('updateLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $layoutUpdateStruct = new LayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';

        $this->layoutService->updateLayout(
            Layout::fromArray(['status' => Value::STATUS_DRAFT]),
            $layoutUpdateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     */
    public function testCopyLayout(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('layoutNameExists')
            ->will(self::returnValue(false));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandler
            ->expects(self::at(2))
            ->method('copyLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $layoutCopyStruct = new LayoutCopyStruct();
        $layoutCopyStruct->name = 'Name';

        $this->layoutService->copyLayout(
            Layout::fromArray(['id' => 42, 'status' => Layout::STATUS_DRAFT]),
            $layoutCopyStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     */
    public function testChangeLayoutType(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('loadLayoutZones')
            ->will(self::returnValue([]));

        $this->layoutHandler
            ->expects(self::at(2))
            ->method('changeLayoutType')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->changeLayoutType(
            Layout::fromArray(['status' => Layout::STATUS_DRAFT]),
            LayoutType::fromArray(['identifier' => '4_zones_a']),
            []
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('layoutExists')
            ->will(self::returnValue(false));

        $this->layoutHandler
            ->expects(self::at(2))
            ->method('deleteLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->createDraft(Layout::fromArray(['status' => Value::STATUS_PUBLISHED]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::discardDraft
     */
    public function testDiscardDraft(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('deleteLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->discardDraft(Layout::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     */
    public function testPublishLayout(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('deleteLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->publishLayout(Layout::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::restoreFromArchive
     */
    public function testRestoreFromArchive(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandler
            ->expects(self::at(2))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandler
            ->expects(self::at(3))
            ->method('deleteLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->restoreFromArchive(Layout::fromArray(['status' => Layout::STATUS_ARCHIVED]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     */
    public function testDeleteLayout(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->layoutHandler
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandler
            ->expects(self::at(1))
            ->method('deleteLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->deleteLayout(Layout::fromArray(['id' => 42, 'status' => Layout::STATUS_DRAFT]));
    }
}
