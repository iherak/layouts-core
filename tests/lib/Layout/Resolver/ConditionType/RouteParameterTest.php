<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class RouteParameterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter
     */
    private $conditionType;

    public function setUp(): void
    {
        $this->conditionType = new RouteParameter();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter::getType
     */
    public function testGetType(): void
    {
        self::assertSame('route_parameter', $this->conditionType::getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, bool $matches): void
    {
        $request = Request::create('/');
        $request->attributes->set(
            '_route_params',
            [
                'the_answer' => 42,
            ]
        );

        self::assertSame($matches, $this->conditionType->matches($request, $value));
    }

    public function validationProvider(): array
    {
        return [
            [['parameter_name' => 'name', 'parameter_values' => ['one', 'two']], true],
            [['parameter_name' => 'name', 'parameter_values' => ['one']], true],
            [['parameter_name' => 'name', 'parameter_values' => ['']], true],
            [['parameter_name' => 'name', 'parameter_values' => [['one']]], false],
            [['parameter_name' => 'name', 'parameter_values' => []], true],
            [['parameter_name' => 'name'], false],
            [['parameter_name' => 42, 'parameter_values' => ['one', 'two']], false],
            [['parameter_name' => 42, 'parameter_values' => ['one']], false],
            [['parameter_name' => 42, 'parameter_values' => ['']], false],
            [['parameter_name' => 42, 'parameter_values' => [['one']]], false],
            [['parameter_name' => 42, 'parameter_values' => []], false],
            [['parameter_name' => 42], false],
            [['parameter_values' => ['one', 'two']], false],
            [['parameter_values' => ['one']], false],
            [['parameter_values' => ['']], false],
            [['parameter_values' => [['one']]], false],
            [['parameter_values' => []], false],
            [[], false],
            [null, false],
        ];
    }

    public function matchesProvider(): array
    {
        return [
            ['not_array', false],
            [[], false],
            [['parameter_name' => []], false],
            [['parameter_values' => []], false],
            [['parameter_name' => null, 'parameter_values' => []], false],
            [['parameter_name' => null, 'parameter_values' => [42]], false],
            [['parameter_name' => null, 'parameter_values' => [24]], false],
            [['parameter_name' => null, 'parameter_values' => [42, 24]], false],
            [['parameter_name' => null, 'parameter_values' => [24, 42]], false],
            [['parameter_name' => null, 'parameter_values' => [24, 25]], false],
            [['parameter_name' => 'the_answer', 'parameter_values' => []], true],
            [['parameter_name' => 'the_answer', 'parameter_values' => [42]], true],
            [['parameter_name' => 'the_answer', 'parameter_values' => [24]], false],
            [['parameter_name' => 'the_answer', 'parameter_values' => [42, 24]], true],
            [['parameter_name' => 'the_answer', 'parameter_values' => [24, 42]], true],
            [['parameter_name' => 'the_answer', 'parameter_values' => [24, 25]], false],
            [['parameter_name' => 'the_other_answer', 'parameter_values' => []], false],
            [['parameter_name' => 'the_other_answer', 'parameter_values' => [42]], false],
            [['parameter_name' => 'the_other_answer', 'parameter_values' => [24]], false],
            [['parameter_name' => 'the_other_answer', 'parameter_values' => [42, 24]], false],
            [['parameter_name' => 'the_other_answer', 'parameter_values' => [24, 42]], false],
            [['parameter_name' => 'the_other_answer', 'parameter_values' => [24, 25]], false],
        ];
    }
}
