<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Integration;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\ConfigValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;

abstract class BlockTest extends ServiceTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->prepareParameterTypeRegistry();

        $validator = $this->getValidator();

        $configValidator = new ConfigValidator();
        $configValidator->setValidator($validator);

        $collectionValidator = new CollectionValidator($configValidator);
        $collectionValidator->setValidator($validator);

        $blockValidator = new BlockValidator($configValidator, $collectionValidator);
        $blockValidator->setValidator($validator);

        $layoutValidator = new LayoutValidator();
        $layoutValidator->setValidator($validator);

        $this->blockService = $this->createBlockService($blockValidator);
        $this->layoutService = $this->createLayoutService($layoutValidator);
    }

    /**
     * @param array $parameters
     * @param array $expectedParameters
     * @dataProvider parametersDataProvider
     */
    public function testCreateBlock(array $parameters, array $expectedParameters)
    {
        $blockDefinition = $this->createBlockDefinition(array_keys($expectedParameters));
        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->fill($blockDefinition, $parameters);

        $zone = $this->layoutService->loadZoneDraft(1, 'left');
        $createdBlock = $this->blockService->createBlockInZone($blockCreateStruct, $zone);

        $createdParameters = array();
        foreach ($createdBlock->getParameters() as $parameterName => $parameterValue) {
            $createdParameters[$parameterName] = $parameterValue->getValue();
        }

        $this->assertEquals($expectedParameters, $createdParameters);
    }

    /**
     * @param array $parameters
     * @param array $testedParams
     * @dataProvider invalidParametersDataProvider
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     */
    public function testCreateBlockWithInvalidParameters(array $parameters, array $testedParams = null)
    {
        if (empty($parameters)) {
            throw ValidationException::validationFailed('parameters', 'Invalid parameters');
        }

        $blockDefinition = $this->createBlockDefinition(
            $testedParams !== null ? $testedParams : array_keys($parameters)
        );

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->fill($blockDefinition, $parameters);

        $zone = $this->layoutService->loadZoneDraft(1, 'left');
        $this->blockService->createBlockInZone($blockCreateStruct, $zone);
    }

    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    abstract public function createBlockDefinitionHandler();

    /**
     * @return bool
     */
    public function hasCollection()
    {
        return false;
    }

    /**
     * @return array
     */
    abstract public function parametersDataProvider();

    /**
     * @return array
     */
    abstract public function invalidParametersDataProvider();

    /**
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    public function getParameterTypes()
    {
        return array();
    }

    public function getValidators()
    {
        return array();
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();
    }

    /**
     * @param array $parameterNames
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private function createBlockDefinition(array $parameterNames = array())
    {
        $handler = $this->createBlockDefinitionHandler();

        $builderFactory = new TranslatableParameterBuilderFactory($this->parameterTypeRegistry);
        $parameterBuilder = $builderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameterDefinitions = $parameterBuilder->buildParameterDefinitions();

        $filteredParameterDefinitions = array();
        if (!empty($parameterNames)) {
            foreach ($parameterDefinitions as $parameterName => $parameterDefinition) {
                if (in_array($parameterName, $parameterNames, true)) {
                    $filteredParameterDefinitions[$parameterName] = $parameterDefinition;
                }
            }
        }

        $collections = array();
        if ($this->hasCollection()) {
            $collections['default'] = new Collection(
                array(
                    'identifier' => 'default',
                )
            );
        }

        $blockDefinition = new BlockDefinition(
            array(
                'identifier' => 'definition',
                'handler' => $handler,
                'viewTypes' => array(
                    'default' => new ViewType(
                        array(
                            'itemViewTypes' => array(
                                'standard' => new ItemViewType(),
                            ),
                        )
                    ),
                ),
                'collections' => $collections,
                'parameterDefinitions' => $filteredParameterDefinitions,
                'configDefinitions' => array(),
            )
        );

        $this->blockDefinitionRegistry->addBlockDefinition('definition', $blockDefinition);

        return $blockDefinition;
    }

    private function prepareParameterTypeRegistry()
    {
        $remoteIdConverter = new RemoteIdConverter($this->createMock(ItemLoaderInterface::class));

        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\UrlType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\RangeType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\NumberType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\LinkType(new ValueTypeRegistry(), $remoteIdConverter));
        $this->parameterTypeRegistry->addParameterType(new ParameterType\ItemLinkType(new ValueTypeRegistry(), $remoteIdConverter));
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IntegerType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IdentifierType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\HtmlType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\EmailType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\ChoiceType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\BooleanType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\Compound\BooleanType());

        foreach ($this->getParameterTypes() as $parameterType) {
            $this->parameterTypeRegistry->addParameterType($parameterType);
        }
    }
}
