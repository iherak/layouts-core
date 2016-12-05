<?php

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Page\BlockCreateStruct;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterFilter;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Structs\ParameterStructValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParameterStructValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $compoundParameter = new CompoundParameter(
            'checkbox',
            new ParameterType\Compound\BooleanType()
        );

        $compoundParameter->setParameters(
            array(
                'param' => new Parameter('param', new ParameterType\IdentifierType(), array(), true),
            )
        );

        $this->constraint = new ParameterStruct(
            array(
                'parameterCollection' => new ParameterCollection(
                    array(
                        'css_id' => new Parameter('css_id', new ParameterType\TextLineType(), array(), true),
                        'checkbox' => $compoundParameter,
                    )
                ),
                'allowMissingFields' => true,
            )
        );

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        $parameterFilterRegistry = new ParameterFilterRegistry();
        $parameterFilterRegistry->addParameterFilters('text_line', array(new ParameterFilter()));

        return new ParameterStructValidator($parameterFilterRegistry);
    }

    /**
     * @param string $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::__construct
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::validate
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::filterParameters
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::buildConstraintFields
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $required, $isValid)
    {
        $this->constraint->allowMissingFields = !$required;

        $this->assertValid(
            $isValid,
            new BlockCreateStruct(array('parameterValues' => $value))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => 'value'), true, true),
            array(array('css_id' => '', 'checkbox' => true, 'param' => 'value'), true, false),
            array(array('css_id' => null, 'checkbox' => true, 'param' => 'value'), true, false),
            array(array('checkbox' => true, 'param' => 'value'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => 'value'), false, true),
            array(array('css_id' => '', 'checkbox' => true, 'param' => 'value'), false, false),
            array(array('css_id' => null, 'checkbox' => true, 'param' => 'value'), false, false),
            array(array('checkbox' => true, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => 'value'), true, true),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => ''), true, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => null), true, true),
            array(array('css_id' => 'ID', 'checkbox' => true), true, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => 'value'), true, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => ''), true, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => null), true, true),
            array(array('css_id' => 'ID', 'checkbox' => false), true, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => 'value'), true, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => ''), true, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => null), true, true),
            array(array('css_id' => 'ID', 'checkbox' => null), true, true),
            array(array('css_id' => 'ID', 'param' => 'value'), true, true),
            array(array('css_id' => 'ID', 'param' => '?'), true, false),
            array(array('css_id' => 'ID', 'param' => ''), true, false),
            array(array('css_id' => 'ID', 'param' => null), true, true),
            array(array('css_id' => 'ID'), true, true),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => ''), false, false),
            array(array('css_id' => 'ID', 'checkbox' => true, 'param' => null), false, true),
            array(array('css_id' => 'ID', 'checkbox' => true), false, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => ''), false, false),
            array(array('css_id' => 'ID', 'checkbox' => false, 'param' => null), false, true),
            array(array('css_id' => 'ID', 'checkbox' => false), false, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => ''), false, false),
            array(array('css_id' => 'ID', 'checkbox' => null, 'param' => null), false, true),
            array(array('css_id' => 'ID', 'checkbox' => null), false, true),
            array(array('css_id' => 'ID', 'param' => 'value'), false, true),
            array(array('css_id' => 'ID', 'param' => '?'), false, false),
            array(array('css_id' => 'ID', 'param' => ''), false, false),
            array(array('css_id' => 'ID', 'param' => null), false, true),
            array(array('css_id' => 'ID'), false, true),
        );
    }
}