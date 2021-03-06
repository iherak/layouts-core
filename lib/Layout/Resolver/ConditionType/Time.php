<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Netgen\BlockManager\Validator\Constraint\ConditionType\Time as TimeConstraint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class Time implements ConditionTypeInterface
{
    public static function getType(): string
    {
        return 'time';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotBlank(),
            new TimeConstraint(),
        ];
    }

    public function matches(Request $request, $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (empty($value['from']) && empty($value['to'])) {
            return true;
        }

        $visibleFrom = isset($value['from']) && is_array($value['from']) ?
            DateTimeUtils::createFromArray($value['from']) :
            null;

        $visibleTo = isset($value['to']) && is_array($value['to']) ?
            DateTimeUtils::createFromArray($value['to']) :
            null;

        return DateTimeUtils::isBetweenDates(null, $visibleFrom, $visibleTo);
    }
}
