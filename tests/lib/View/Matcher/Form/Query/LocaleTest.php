<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Query;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Query\Locale;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

final class LocaleTest extends TestCase
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

        $this->matcher = new Locale();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'query' => Query::fromArray(
                    [
                        'locale' => 'en',
                    ]
                ),
            ]
        );

        self::assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['fr'], false],
            [['en'], true],
            [['fr', 'de'], false],
            [['en', 'fr'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
     */
    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
     */
    public function testMatchWithNoQuery(): void
    {
        $form = $this->formFactory->create(Form::class);

        self::assertFalse($this->matcher->match(new FormView($form), ['Locale']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
     */
    public function testMatchWithInvalidQuery(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['query' => 'Locale']);

        self::assertFalse($this->matcher->match(new FormView($form), ['Locale']));
    }
}
