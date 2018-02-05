<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use DateTime;
use DateTimeImmutable;
use Netgen\BlockManager\Parameters\ParameterType\DateTimeType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class DateTimeTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\DateTimeType
     */
    private $type;

    public function setUp()
    {
        $this->type = new DateTimeType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('datetime', $this->type->getIdentifier());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface
     */
    public function getParameter($options = array())
    {
        return new ParameterDefinition(
            array(
                'name' => 'name',
                'type' => $this->type,
                'options' => $options,
            )
        );
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, $isEmpty)
    {
        $this->assertEquals($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     *
     * @return array
     */
    public function emptyProvider()
    {
        return array(
            array(null, true),
            array(new DateTime(), false),
            array(new DateTimeImmutable(), false),
            array(DateTime::createFromFormat(DateTime::RFC3339, '2018-02-01T15:00:00+01:00'), false),
            array(DateTimeImmutable::createFromFormat(DateTime::RFC3339, '2018-02-01T15:00:00+01:00'), false),
        );
    }

    /**
     * @param mixed $value
     * @param bool $convertedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::toHash
     * @dataProvider toHashProvider
     */
    public function testToHash($value, $convertedValue)
    {
        $this->assertEquals($convertedValue, $this->type->toHash(new ParameterDefinition(), $value));
    }

    public function toHashProvider()
    {
        return array(
            array(42, null),
            array(null, null),
            array(DateTimeImmutable::createFromFormat(DateTime::RFC3339, '2018-02-01T15:00:00+01:00'), '2018-02-01T15:00:00+01:00'),
        );
    }

    /**
     * @param mixed $value
     * @param bool $convertedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::fromHash
     * @dataProvider fromHashProvider
     */
    public function testFromHash($value, $convertedValue)
    {
        $this->assertEquals($convertedValue, $this->type->fromHash(new ParameterDefinition(), $value));
    }

    public function fromHashProvider()
    {
        return array(
            array(42, null),
            array(null, null),
            array('01.02.2018 15:00:00', null),
            array('2018-02-01T15:00:00+01:00', DateTimeImmutable::createFromFormat(DateTime::RFC3339, '2018-02-01T15:00:00+01:00')),
        );
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::getValueConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\DateTimeType::getRequiredConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $parameter = $this->getParameter();
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(null, true),
            array(new DateTime(), true),
            array(new DateTimeImmutable(), true),
            array(DateTime::createFromFormat(DateTime::RFC3339, '2018-02-01T15:00:00+01:00'), true),
            array(DateTimeImmutable::createFromFormat(DateTime::RFC3339, '2018-02-01T15:00:00+01:00'), true),
        );
    }
}
