<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Layout\Form\CreateType;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    public function setUp(): void
    {
        $layoutType1 = LayoutType::fromArray(
            [
                'name' => '4 zones A',
                'identifier' => '4_zones_a',
                'isEnabled' => true,
            ]
        );

        $layoutType2 = LayoutType::fromArray(
            [
                'name' => '4 zones B',
                'identifier' => '4_zones_b',
                'isEnabled' => false,
            ]
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry(
            [
                '4_zones_a' => $layoutType1,
                '4_zones_b' => $layoutType2,
            ]
        );

        parent::setUp();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::__construct
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::buildForm
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::finishView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'name' => 'My layout',
            'layoutType' => '4_zones_a',
            'shared' => true,
        ];

        $struct = new LayoutCreateStruct();

        $form = $this->factory->create(
            CreateType::class,
            $struct
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame('My layout', $struct->name);
        self::assertTrue($struct->shared);

        self::assertInstanceOf(LayoutType::class, $struct->layoutType);
        self::assertSame('4_zones_a', $struct->layoutType->getIdentifier());

        $view = $form->createView();

        $layoutTypes = $this->layoutTypeRegistry->getLayoutTypes(true);
        $childViews = $view->children['layoutType']->children;

        self::assertCount(count($layoutTypes), $childViews);

        foreach ($layoutTypes as $identifier => $layoutType) {
            self::assertArrayHasKey($identifier, $childViews);

            self::assertArrayHasKey('layout_type', $childViews[$identifier]->vars);
            self::assertSame($layoutType, $childViews[$identifier]->vars['layout_type']);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new LayoutCreateStruct();

        $options = $optionsResolver->resolve(
            [
                'data' => $struct,
            ]
        );

        self::assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\\BlockManager\\API\\Values\\Layout\\LayoutCreateStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'data' => '',
            ]
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new CreateType($this->layoutTypeRegistry);
    }
}
