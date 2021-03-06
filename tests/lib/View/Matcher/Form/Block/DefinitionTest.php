<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\NullBlockDefinition;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Block\Definition;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

final class DefinitionTest extends TestCase
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->matcher = new Definition();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'block' => Block::fromArray(
                    [
                        'definition' => BlockDefinition::fromArray(['identifier' => 'block']),
                    ]
                ),
            ]
        );

        self::assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionTrait::doMatch
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNullBlockDefinition(): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'block' => Block::fromArray(
                    [
                        'definition' => new NullBlockDefinition('definition'),
                    ]
                ),
            ]
        );

        self::assertTrue($this->matcher->match(new FormView($form), ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionTrait::doMatch
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNullBlockDefinitionReturnsFalse(): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'block' => Block::fromArray(
                    [
                        'definition' => new NullBlockDefinition('definition'),
                    ]
                ),
            ]
        );

        self::assertFalse($this->matcher->match(new FormView($form), ['test']));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['other_block'], false],
            [['block'], true],
            [['other_block', 'second_block'], false],
            [['block', 'other_block'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNoBlock(): void
    {
        $form = $this->formFactory->create(Form::class);

        self::assertFalse($this->matcher->match(new FormView($form), ['block']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithInvalidBlock(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['block' => 'block']);

        self::assertFalse($this->matcher->match(new FormView($form), ['block']));
    }
}
