<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class ViewRenderer implements ViewRendererInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(EventDispatcherInterface $eventDispatcher, Environment $twig)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->twig = $twig;
    }

    public function renderView(ViewInterface $view): string
    {
        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch(BlockManagerEvents::RENDER_VIEW, $event);
        $view->addParameters($event->getParameters());

        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch(sprintf('%s.%s', BlockManagerEvents::RENDER_VIEW, $view::getIdentifier()), $event);
        $view->addParameters($event->getParameters());

        $viewTemplate = $view->getTemplate();
        if ($viewTemplate === null) {
            return '';
        }

        return $this->twig->render($viewTemplate, $view->getParameters());
    }
}
