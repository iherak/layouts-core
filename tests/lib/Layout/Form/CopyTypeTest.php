<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\Layout\Form\CopyType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CopyTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    public function setUp(): void
    {
        parent::setUp();

        $this->layout = new Layout();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CopyType::buildForm
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'name' => 'New name',
            'description' => 'New description',
        ];

        $struct = new LayoutCopyStruct();

        $form = $this->factory->create(
            CopyType::class,
            $struct,
            [
                'layout' => $this->layout,
            ]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame('New name', $struct->name);
        self::assertSame('New description', $struct->description);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CopyType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new LayoutCopyStruct();

        $options = $optionsResolver->resolve(
            [
                'layout' => $this->layout,
                'data' => $struct,
            ]
        );

        self::assertSame($this->layout, $options['layout']);
        self::assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CopyType::configureOptions
     */
    public function testConfigureOptionsWithMissingLayout(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "layout" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CopyType::configureOptions
     */
    public function testConfigureOptionsWithInvalidLayout(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "layout" with value "" is expected to be of type "Netgen\\BlockManager\\API\\Values\\Layout\\Layout", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'layout' => '',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CopyType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\\BlockManager\\API\\Values\\Layout\\LayoutCopyStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'layout' => $this->layout,
                'data' => '',
            ]
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new CopyType();
    }
}
