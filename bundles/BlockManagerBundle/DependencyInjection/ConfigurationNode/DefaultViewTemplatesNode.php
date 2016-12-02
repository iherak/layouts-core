<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;

class DefaultViewTemplatesNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for template resolvers.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('default_view_templates');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('view')
            ->prototype('array')
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('context')
                ->prototype('scalar')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $node;
    }
}