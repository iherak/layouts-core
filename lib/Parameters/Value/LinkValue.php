<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Value;

use Netgen\BlockManager\Value;

final class LinkValue extends Value
{
    public const LINK_TYPE_URL = 'url';

    public const LINK_TYPE_EMAIL = 'email';

    public const LINK_TYPE_PHONE = 'phone';

    public const LINK_TYPE_INTERNAL = 'internal';

    /**
     * @var string
     */
    protected $linkType;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var string
     */
    protected $linkSuffix;

    /**
     * @var bool
     */
    protected $newWindow = false;

    /**
     * Returns the link type.
     */
    public function getLinkType(): ?string
    {
        return $this->linkType;
    }

    /**
     * Returns the link value.
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * Returns the link suffix.
     */
    public function getLinkSuffix(): ?string
    {
        return $this->linkSuffix;
    }

    /**
     * Returns if the link should be opened in new window.
     */
    public function getNewWindow(): bool
    {
        return $this->newWindow;
    }
}
