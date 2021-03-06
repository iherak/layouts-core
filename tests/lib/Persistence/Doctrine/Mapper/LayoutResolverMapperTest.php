<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class LayoutResolverMapperTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new LayoutResolverMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapRules
     */
    public function testMapRules(): void
    {
        $data = [
            [
                'id' => '42',
                'layout_id' => '24',
                'enabled' => '1',
                'priority' => '2',
                'comment' => 'Comment',
                'status' => '1',
            ],
            [
                'id' => '43',
                'layout_id' => '25',
                'enabled' => '0',
                'priority' => '3',
                'comment' => null,
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'layoutId' => 24,
                'enabled' => true,
                'priority' => 2,
                'comment' => 'Comment',
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'layoutId' => 25,
                'enabled' => false,
                'priority' => 3,
                'comment' => null,
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $rules = $this->mapper->mapRules($data);

        self::assertContainsOnlyInstancesOf(Rule::class, $rules);
        self::assertSame($expectedData, $this->exportObjectList($rules));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapTargets
     */
    public function testMapTargets(): void
    {
        $data = [
            [
                'id' => '42',
                'rule_id' => '1',
                'type' => 'target',
                'value' => '32',
                'status' => '1',
            ],
            [
                'id' => 43,
                'rule_id' => 2,
                'type' => 'target2',
                'value' => '42',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'ruleId' => 1,
                'type' => 'target',
                'value' => '32',
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'ruleId' => 2,
                'type' => 'target2',
                'value' => '42',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $targets = $this->mapper->mapTargets($data);

        self::assertContainsOnlyInstancesOf(Target::class, $targets);
        self::assertSame($expectedData, $this->exportObjectList($targets));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapConditions
     */
    public function testMapConditions(): void
    {
        $data = [
            [
                'id' => '42',
                'rule_id' => '1',
                'type' => 'condition',
                'value' => '24',
                'status' => '1',
            ],
            [
                'id' => 43,
                'rule_id' => 2,
                'type' => 'condition2',
                'value' => '{"param":"value"}',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'ruleId' => 1,
                'type' => 'condition',
                'value' => 24,
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'ruleId' => 2,
                'type' => 'condition2',
                'value' => [
                    'param' => 'value',
                ],
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $conditions = $this->mapper->mapConditions($data);

        self::assertContainsOnlyInstancesOf(Condition::class, $conditions);
        self::assertSame($expectedData, $this->exportObjectList($conditions));
    }
}
