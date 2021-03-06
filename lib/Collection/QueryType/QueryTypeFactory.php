<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\QueryType;

use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;

final class QueryTypeFactory
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    private $parameterBuilderFactory;

    public function __construct(ParameterBuilderFactoryInterface $parameterBuilderFactory)
    {
        $this->parameterBuilderFactory = $parameterBuilderFactory;
    }

    /**
     * Builds the query type.
     */
    public function buildQueryType(
        string $type,
        QueryTypeHandlerInterface $handler,
        array $config
    ): QueryTypeInterface {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameterDefinitions = $parameterBuilder->buildParameterDefinitions();

        return QueryType::fromArray(
            [
                'type' => $type,
                'isEnabled' => $config['enabled'],
                'name' => $config['name'] ?? '',
                'handler' => $handler,
                'parameterDefinitions' => $parameterDefinitions,
            ]
        );
    }
}
