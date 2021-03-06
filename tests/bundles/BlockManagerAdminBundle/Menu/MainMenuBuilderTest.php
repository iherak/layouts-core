<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\Menu;

use Knp\Menu\Integration\Symfony\RoutingExtension;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class MainMenuBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $authorizationCheckerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder
     */
    private $builder;

    public function setUp(): void
    {
        $urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);
        $urlGeneratorMock
            ->expects(self::any())
            ->method('generate')
            ->will(
                self::returnCallback(
                    function (string $route): string {
                        return $route;
                    }
                )
            );

        $menuFactory = new MenuFactory();
        $menuFactory->addExtension(new RoutingExtension($urlGeneratorMock));

        $this->authorizationCheckerMock = $this->createMock(AuthorizationCheckerInterface::class);

        $this->builder = new MainMenuBuilder($menuFactory, $this->authorizationCheckerMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder::createMenu
     */
    public function testCreateMenu(): void
    {
        $this->authorizationCheckerMock
            ->expects(self::any())
            ->method('isGranted')
            ->with(self::identicalTo('ROLE_NGBM_ADMIN'))
            ->will(self::returnValue(true));

        $menu = $this->builder->createMenu();

        self::assertCount(3, $menu);

        self::assertInstanceOf(ItemInterface::class, $menu->getChild('layout_resolver'));
        self::assertSame(
            'ngbm_admin_layout_resolver_index',
            $menu->getChild('layout_resolver')->getUri()
        );

        self::assertInstanceOf(ItemInterface::class, $menu->getChild('layouts'));
        self::assertSame(
            'ngbm_admin_layouts_index',
            $menu->getChild('layouts')->getUri()
        );

        self::assertInstanceOf(ItemInterface::class, $menu->getChild('shared_layouts'));
        self::assertSame(
            'ngbm_admin_shared_layouts_index',
            $menu->getChild('shared_layouts')->getUri()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder::createMenu
     */
    public function testCreateMenuWithNoAccess(): void
    {
        $this->authorizationCheckerMock
            ->expects(self::any())
            ->method('isGranted')
            ->with(self::identicalTo('ROLE_NGBM_ADMIN'))
            ->will(self::returnValue(false));

        $menu = $this->builder->createMenu();

        self::assertCount(0, $menu);
    }
}
