<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values;

trait ValueStatusTrait
{
    /**
     * @var int
     */
    private $status;

    public function getStatus(): int
    {
        return $this->status;
    }

    public function isDraft(): bool
    {
        return $this->status === Value::STATUS_DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === Value::STATUS_PUBLISHED;
    }

    public function isArchived(): bool
    {
        return $this->status === Value::STATUS_ARCHIVED;
    }
}
