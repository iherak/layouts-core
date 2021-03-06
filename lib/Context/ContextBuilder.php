<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Context;

final class ContextBuilder implements ContextBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Context\ContextProviderInterface[]
     */
    private $providers = [];

    public function registerProvider(ContextProviderInterface $contextProvider): void
    {
        $this->providers[] = $contextProvider;
    }

    public function buildContext(ContextInterface $context): void
    {
        foreach ($this->providers as $provider) {
            $provider->provideContext($context);
        }
    }
}
