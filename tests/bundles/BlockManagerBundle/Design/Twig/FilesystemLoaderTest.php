<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Design\Twig;

use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader;
use PHPUnit\Framework\TestCase;
use Twig\Loader\FilesystemLoader as BaseFilesystemLoader;
use Twig\Source;

final class FilesystemLoaderTest extends TestCase
{
    /**
     * @var \Twig\Loader\FilesystemLoader&\PHPUnit\Framework\MockObject\MockObject
     */
    private $innerLoaderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $configurationMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader
     */
    private $loader;

    public function setUp(): void
    {
        // We mock \Twig\Loader\FilesystemLoader instead of the loader interface
        // because it has all needed methods in both versions of Twig (1.x and 2.x)
        $this->innerLoaderMock = $this->createMock(BaseFilesystemLoader::class);
        $this->configurationMock = $this->createMock(ConfigurationInterface::class);

        $this->configurationMock
            ->expects(self::any())
            ->method('getParameter')
            ->with(self::identicalTo('design'))
            ->will(self::returnValue('test'));

        $this->loader = new FilesystemLoader(
            $this->innerLoaderMock,
            $this->configurationMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getRealName
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getSource
     */
    public function testGetSource(): void
    {
        $this->innerLoaderMock = $this->createMock(LegacyFilesystemLoader::class);
        $this->loader = new FilesystemLoader($this->innerLoaderMock, $this->configurationMock);

        $this->innerLoaderMock
            ->expects(self::once())
            ->method('getSourceContext')
            ->with(self::identicalTo('@ngbm_test/template.html.twig'))
            ->will(self::returnValue(new Source('source code', '@ngbm_test/template.html.twig')));

        $source = $this->loader->getSource('@ngbm/template.html.twig');

        self::assertSame('source code', $source);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getRealName
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getSource
     */
    public function testGetSourceWithNonLayoutsTwigFile(): void
    {
        $this->innerLoaderMock = $this->createMock(LegacyFilesystemLoader::class);
        $this->loader = new FilesystemLoader($this->innerLoaderMock, $this->configurationMock);

        $this->innerLoaderMock
            ->expects(self::once())
            ->method('getSourceContext')
            ->with(self::identicalTo('@other/template.html.twig'))
            ->will(self::returnValue(new Source('source code', '@other/template.html.twig')));

        $source = $this->loader->getSource('@other/template.html.twig');

        self::assertSame('source code', $source);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getRealName
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getSourceContext
     */
    public function testGetSourceContext(): void
    {
        $source = new Source('', '@ngbm_test/template.html.twig');

        $this->innerLoaderMock
            ->expects(self::once())
            ->method('getSourceContext')
            ->with(self::identicalTo('@ngbm_test/template.html.twig'))
            ->will(self::returnValue($source));

        $sourceContext = $this->loader->getSourceContext('@ngbm/template.html.twig');

        self::assertSame($source, $sourceContext);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getRealName
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getSourceContext
     */
    public function testGetSourceContextWithNonLayoutsTwigFile(): void
    {
        $source = new Source('', '@other/template.html.twig');

        $this->innerLoaderMock
            ->expects(self::once())
            ->method('getSourceContext')
            ->with(self::identicalTo('@other/template.html.twig'))
            ->will(self::returnValue($source));

        $sourceContext = $this->loader->getSourceContext('@other/template.html.twig');

        self::assertSame($source, $sourceContext);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getCacheKey
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getRealName
     */
    public function testGetCacheKey(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('getCacheKey')
            ->with(self::identicalTo('@ngbm_test/template.html.twig'))
            ->will(self::returnValue('cache_key'));

        $cacheKey = $this->loader->getCacheKey('@ngbm/template.html.twig');

        self::assertSame('cache_key', $cacheKey);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getCacheKey
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getRealName
     */
    public function testGetCacheKeyWithNonLayoutsTwigFile(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('getCacheKey')
            ->with(self::identicalTo('@other/template.html.twig'))
            ->will(self::returnValue('cache_key'));

        $cacheKey = $this->loader->getCacheKey('@other/template.html.twig');

        self::assertSame('cache_key', $cacheKey);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getRealName
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::isFresh
     */
    public function testIsFresh(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('isFresh')
            ->with(self::identicalTo('@ngbm_test/template.html.twig'), self::identicalTo(42))
            ->will(self::returnValue(true));

        self::assertTrue($this->loader->isFresh('@ngbm/template.html.twig', 42));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getRealName
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::isFresh
     */
    public function testIsFreshWithNonLayoutsTwigFile(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('isFresh')
            ->with(self::identicalTo('@other/template.html.twig'), self::identicalTo(42))
            ->will(self::returnValue(true));

        self::assertTrue($this->loader->isFresh('@other/template.html.twig', 42));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::exists
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getRealName
     */
    public function testExists(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('exists')
            ->with(self::identicalTo('@ngbm_test/template.html.twig'))
            ->will(self::returnValue(true));

        self::assertTrue($this->loader->exists('@ngbm/template.html.twig'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::exists
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getRealName
     */
    public function testExistsWithNonLayoutsTwigFile(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('exists')
            ->with(self::identicalTo('@other/template.html.twig'))
            ->will(self::returnValue(true));

        self::assertTrue($this->loader->exists('@other/template.html.twig'));
    }
}
