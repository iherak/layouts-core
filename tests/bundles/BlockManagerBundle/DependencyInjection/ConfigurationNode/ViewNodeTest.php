<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ViewNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettings(): void
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'template' => 'block.html.twig',
                                'match' => [
                                    'block_identifier' => 42,
                                ],
                                'parameters' => [
                                    'param' => 'value',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                            'match' => [
                                'block_identifier' => 42,
                            ],
                            'parameters' => [
                                'param' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithMatchWithArrayValues(): void
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'match' => [24, 42],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'match' => [24, 42],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view.*.*.*.match'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithEmptyMatch(): void
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'match' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'match' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view.*.*.*.match'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoParameters(): void
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'parameters' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view.*.*.*.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoMatch(): void
    {
        $config = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoTemplate(): void
    {
        $config = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'match' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoBlocks(): void
    {
        $config = [
            'view' => [
                'block_view' => [
                    'some_context' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoContext(): void
    {
        $config = [
            'view' => [
                'block_view' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoViews(): void
    {
        $config = [
            'view' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
