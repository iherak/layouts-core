<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Validator\BlockViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;

class BlockViewTypeValidatorTest extends ValidatorTest
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    /**
     * @var \Netgen\BlockManager\Validator\BlockViewTypeValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $config = new Configuration(
            'block',
            array(),
            array(
                'large' => new ViewType('large', 'Large'),
            )
        );

        $this->blockDefinition = new BlockDefinition(
            'block',
            $this->getMock(BlockDefinitionHandlerInterface::class),
            $config
        );

        $this->validator = new BlockViewTypeValidator();
        $this->validator->initialize($this->executionContextMock);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     */
    public function testValidate()
    {
        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('large', new BlockViewType(array('definition' => $this->blockDefinition)));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     */
    public function testValidateFailedWithNoViewType()
    {
        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate('small', new BlockViewType(array('definition' => $this->blockDefinition)));
    }
}
