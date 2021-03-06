<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterBuilderFactory implements ParameterBuilderFactoryInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    private $parameterTypeRegistry;

    public function __construct(ParameterTypeRegistryInterface $parameterTypeRegistry)
    {
        $this->parameterTypeRegistry = $parameterTypeRegistry;
    }

    public function createParameterBuilder(array $config = []): ParameterBuilderInterface
    {
        $config = $this->resolveOptions($config);

        $parameterBuilder = new ParameterBuilder(
            $this,
            $config['name'],
            $config['type'],
            $config['options'],
            $config['parent']
        );

        return $parameterBuilder;
    }

    /**
     * Resolves the provided parameter builder configuration.
     */
    protected function resolveOptions(array $config): array
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setDefault('name', null);
        $optionsResolver->setDefault('type', null);
        $optionsResolver->setDefault('options', []);
        $optionsResolver->setDefault('parent', null);

        $optionsResolver->setRequired(['name', 'type', 'options', 'parent']);

        $optionsResolver->setAllowedTypes('name', ['null', 'string']);
        $optionsResolver->setAllowedTypes('type', ['null', 'string', ParameterTypeInterface::class]);
        $optionsResolver->setAllowedTypes('options', 'array');
        $optionsResolver->setAllowedTypes('parent', ['null', ParameterBuilderInterface::class]);

        $optionsResolver->setNormalizer(
            'type',
            function (Options $options, $value) {
                if (!is_string($value)) {
                    return $value;
                }

                return $this->parameterTypeRegistry->getParameterTypeByClass($value);
            }
        );

        $config = $optionsResolver->resolve($config);

        return $config;
    }
}
