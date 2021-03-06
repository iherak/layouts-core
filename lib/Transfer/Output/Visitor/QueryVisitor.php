<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Generator;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Query value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Query
 */
final class QueryVisitor implements VisitorInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    public function accept($value): bool
    {
        return $value instanceof Query;
    }

    public function visit($query, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Query $query */

        return [
            'id' => $query->getId(),
            'is_translatable' => $query->isTranslatable(),
            'is_always_available' => $query->isAlwaysAvailable(),
            'main_locale' => $query->getMainLocale(),
            'available_locales' => $query->getAvailableLocales(),
            'parameters' => $this->visitParameters($query, $subVisitor),
            'query_type' => $query->getQueryType()->getType(),
        ];
    }

    /**
     * Visit the given $query parameters into hash representation.
     */
    private function visitParameters(Query $query, VisitorInterface $subVisitor): array
    {
        $parametersByLanguage = [
            $query->getLocale() => iterator_to_array(
                $this->visitTranslationParameters($query, $subVisitor)
            ),
        ];

        foreach ($query->getAvailableLocales() as $availableLocale) {
            if ($availableLocale === $query->getLocale()) {
                continue;
            }

            $translatedQuery = $this->collectionService->loadQuery(
                $query->getId(),
                [$availableLocale],
                false
            );

            $parametersByLanguage[$availableLocale] = iterator_to_array(
                $this->visitTranslationParameters(
                    $translatedQuery,
                    $subVisitor
                )
            );
        }

        ksort($parametersByLanguage);

        return $parametersByLanguage;
    }

    /**
     * Return parameters for the given $query.
     */
    private function visitTranslationParameters(Query $query, VisitorInterface $subVisitor): Generator
    {
        foreach ($query->getParameters() as $parameter) {
            yield $parameter->getName() => $subVisitor->visit($parameter);
        }
    }
}
