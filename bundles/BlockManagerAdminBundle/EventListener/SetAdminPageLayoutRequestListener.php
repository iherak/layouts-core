<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener;

use Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\BlockManagerAdminBundle\Event\BlockManagerAdminEvents;
use Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SetAdminPageLayoutRequestListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

    /**
     * @var string
     */
    protected $defaultTemplate;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable $globalVariable
     * @param string $defaultTemplate
     */
    public function __construct(GlobalVariable $globalVariable, $defaultTemplate)
    {
        $this->globalVariable = $globalVariable;
        $this->defaultTemplate = $defaultTemplate;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(BlockManagerAdminEvents::ADMIN_MATCH => array('onAdminMatch', -65535));
    }

    /**
     * Sets the pagelayout template for admin interface.
     *
     * @param \Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent $event
     */
    public function onAdminMatch(AdminMatchEvent $event)
    {
        $pageLayoutTemplate = $event->getPageLayoutTemplate();
        if ($pageLayoutTemplate === null) {
            $pageLayoutTemplate = $this->defaultTemplate;
        }

        $this->globalVariable->setPageLayoutTemplate($pageLayoutTemplate);
    }
}