<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Type\DataMapper;

use ArrayIterator;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\LinkType;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Tests\Form\DataMapper\DataMapperTest;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;

final class LinkDataMapperTest extends DataMapperTest
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper
     */
    private $mapper;

    public function setUp(): void
    {
        parent::setUp();

        $parameterDefinition = new ParameterDefinition(
            [
                'type' => new LinkType(
                    new ValueTypeRegistry([]),
                    new RemoteIdConverter($this->createMock(CmsItemLoaderInterface::class))
                ),
            ]
        );

        $this->mapper = new LinkDataMapper($parameterDefinition);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper::mapDataToForms
     */
    public function testMapDataToForms(): void
    {
        $linkValue = new LinkValue(
            [
                'linkType' => 'url',
                'link' => 'http://www.google.com',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ]
        );

        $forms = new ArrayIterator(
            [
                'link_type' => $this->getForm('link_type'),
                'link_suffix' => $this->getForm('link_suffix'),
                'new_window' => $this->getForm('new_window'),
                'url' => $this->getForm('url'),
            ]
        );

        $this->mapper->mapDataToForms($linkValue, $forms);

        $this->assertSame('url', $forms['link_type']->getData());
        $this->assertSame('?suffix', $forms['link_suffix']->getData());
        $this->assertSame('1', $forms['new_window']->getData());
        $this->assertSame('http://www.google.com', $forms['url']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithInvalidData(): void
    {
        $linkValue = 42;

        $forms = new ArrayIterator(
            [
                'link_type' => $this->getForm('link_type'),
                'link_suffix' => $this->getForm('link_suffix'),
                'new_window' => $this->getForm('new_window'),
            ]
        );

        $this->mapper->mapDataToForms($linkValue, $forms);

        $this->assertNull($forms['link_type']->getData());
        $this->assertNull($forms['link_suffix']->getData());
        $this->assertNull($forms['new_window']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper::mapFormsToData
     */
    public function testMapFormsToData(): void
    {
        $forms = new ArrayIterator(
            [
                'link_type' => $this->getForm('link_type', 'url'),
                'link_suffix' => $this->getForm('link_suffix', '?suffix'),
                'new_window' => $this->getForm('new_window', '1'),
                'url' => $this->getForm('url', 'http://www.google.com'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertInstanceOf(LinkValue::class, $data);

        $this->assertSame(
            [
                'linkType' => 'url',
                'link' => 'http://www.google.com',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ],
            $this->exportObject($data)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithInvalidFormData(): void
    {
        $forms = new ArrayIterator(
            [
                'link_type' => $this->getForm('link_type'),
                'link_suffix' => $this->getForm('link_suffix'),
                'new_window' => $this->getForm('new_window'),
            ]
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertInstanceOf(LinkValue::class, $data);

        $this->assertSame(
            [
                'linkType' => null,
                'link' => null,
                'linkSuffix' => null,
                'newWindow' => false,
            ],
            $this->exportObject($data)
        );
    }
}
