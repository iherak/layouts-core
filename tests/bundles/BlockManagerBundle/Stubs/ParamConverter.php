<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Stubs;

use Netgen\BlockManager\API\Values\Value as APIValue;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter as BaseParamConverter;

final class ParamConverter extends BaseParamConverter
{
    public function getSourceAttributeNames(): array
    {
        return ['id'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'value';
    }

    public function getSupportedClass(): string
    {
        return Value::class;
    }

    public function loadValue(array $values): APIValue
    {
        $status = APIValue::STATUS_DRAFT;
        if ($values['status'] === 'published') {
            $status = APIValue::STATUS_PUBLISHED;
        } elseif ($values['status'] === 'archived') {
            $status = APIValue::STATUS_ARCHIVED;
        }

        unset($values['status']);

        return Value::fromArray($values + ['status' => $status]);
    }
}
