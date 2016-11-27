<?php

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use stdClass;

class QueryUpdateStructValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new QueryUpdateStructConstraint();

        $this->constraint->payload = new Query(array('queryType' => new QueryType('query_type')));

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        return new QueryUpdateStructValidator();
    }

    /**
     * @param string $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, new QueryUpdateStruct($value));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new QueryUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlock()
    {
        $this->constraint->payload = new stdClass();
        $this->assertValid(true, new QueryUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->constraint->payload = new Query();
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array(
                array(
                    'identifier' => 'my_query',
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => null,
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => '',
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 42,
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'parameterValues' => array(
                        'param' => '',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'parameterValues' => array(
                        'param' => null,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'parameterValues' => array(),
                ),
                true,
            ),
        );
    }
}