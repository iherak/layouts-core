<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithRequiredParameter;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Utils\Hydrator;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockCreateStruct as BlockCreateStructConstraint;
use Netgen\BlockManager\Validator\Structs\BlockCreateStructValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class BlockCreateStructValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        $this->constraint = new BlockCreateStructConstraint();

        parent::setUp();
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\BlockCreateStructValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $value, bool $isValid): void
    {
        $blockCreateStruct = new BlockCreateStruct($value['definition']);
        (new Hydrator())->hydrate($value, $blockCreateStruct);

        $this->assertValid($isValid, $blockCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\BlockCreateStructValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\Validator\\Constraint\\Structs\\BlockCreateStruct", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockCreateStruct(new BlockDefinition()));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\BlockCreateStructValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\API\\Values\\Block\\BlockCreateStruct", "integer" given');

        $this->assertValid(true, 42);
    }

    public function validateDataProvider(): array
    {
        return [
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'nonexistent',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => '',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'nonexistent',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => '',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => null,
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => '',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 42,
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => null,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => 42,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => null,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => 42,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => '',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => null,
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => '',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => null,
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [],
                ],
                false,
            ],

            // Container block definitions

            [
                [
                    'definition' => $this->getContainerDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],

            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new BlockCreateStructValidator();
    }

    private function getBlockDefinition(): BlockDefinitionInterface
    {
        $handler = new BlockDefinitionHandlerWithRequiredParameter();

        return BlockDefinition::fromArray(
            [
                'parameterDefinitions' => $handler->getParameterDefinitions(),
                'viewTypes' => [
                    'large' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );
    }

    private function getContainerDefinition(): ContainerDefinitionInterface
    {
        return ContainerDefinition::fromArray(
            [
                'handler' => new ContainerDefinitionHandler([], ['main']),
                'viewTypes' => [
                    'large' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );
    }
}
