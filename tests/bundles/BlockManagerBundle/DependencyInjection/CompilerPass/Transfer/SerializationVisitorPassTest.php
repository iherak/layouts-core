<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Transfer;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Transfer\SerializationVisitorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class SerializationVisitorPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Transfer\SerializationVisitorPass::process
     */
    public function testProcess(): void
    {
        $aggregateVisitor = new Definition();
        $aggregateVisitor->addArgument([]);
        $this->setDefinition('netgen_block_manager.transfer.serializer.visitor.aggregate', $aggregateVisitor);

        $visitor = new Definition();
        $visitor->addTag('netgen_block_manager.transfer.serializer.visitor');
        $this->setDefinition('netgen_block_manager.transfer.serializer.visitor.test', $visitor);

        $visitor2 = new Definition();
        $visitor2->addTag('netgen_block_manager.transfer.serializer.visitor');
        $this->setDefinition('netgen_block_manager.transfer.serializer.visitor.test2', $visitor2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.transfer.serializer.visitor.aggregate',
            0,
            [
                new Reference('netgen_block_manager.transfer.serializer.visitor.test'),
                new Reference('netgen_block_manager.transfer.serializer.visitor.test2'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Transfer\SerializationVisitorPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SerializationVisitorPass());
    }
}
