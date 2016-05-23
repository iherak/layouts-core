<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetBuilder;

use Netgen\BlockManager\Layout\Resolver\Target;
use Netgen\BlockManager\Layout\Resolver\TargetBuilder\RoutePrefix;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class RoutePrefixTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\RoutePrefix
     */
    protected $targetBuilder;

    /**
     * Sets up the route target builder tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'my_cool_route');

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetBuilder = new RoutePrefix();
        $this->targetBuilder->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\RoutePrefix::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(
            new Target(array('identifier' => 'route_prefix', 'values' => array('my_cool_route'))),
            $this->targetBuilder->buildTarget()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\RoutePrefix::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetBuilder->buildTarget());
    }
}
