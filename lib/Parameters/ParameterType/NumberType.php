<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate any kind of number.
 */
final class NumberType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'number';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('min', null);
        $optionsResolver->setDefault('max', null);
        $optionsResolver->setDefault('scale', 3);

        $optionsResolver->setRequired(['min', 'max', 'scale']);

        $optionsResolver->setAllowedTypes('min', ['numeric', 'null']);
        $optionsResolver->setAllowedTypes('max', ['numeric', 'null']);
        $optionsResolver->setAllowedTypes('scale', 'int');

        $optionsResolver->setNormalizer(
            'max',
            function (Options $options, $value) {
                if ($value === null || $options['min'] === null) {
                    return $value;
                }

                if ($value < $options['min']) {
                    return $options['min'];
                }

                return $value;
            }
        );

        $optionsResolver->setDefault(
            'default_value',
            function (Options $options, $previousValue) {
                if ($options['required'] === true) {
                    return $options['min'];
                }

                return $previousValue;
            }
        );
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
    {
        return $value === null;
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        $min = $parameterDefinition->getOption('min');
        $max = $parameterDefinition->getOption('max');

        $constraints = [
            new Constraints\Type(['type' => 'numeric']),
        ];

        if ($min !== null) {
            $constraints[] = new Constraints\GreaterThanOrEqual(['value' => $min]);
        }

        if ($max !== null) {
            $constraints[] = new Constraints\LessThanOrEqual(['value' => $max]);
        }

        return $constraints;
    }
}
