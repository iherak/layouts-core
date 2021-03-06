<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Type;

use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

final class TimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'from',
            DateTimeType::class,
            [
                'required' => false,
                'use_datetime' => false,
                'label' => 'condition_type.time.from',
            ]
        );

        $builder->add(
            'to',
            DateTimeType::class,
            [
                'required' => false,
                'use_datetime' => false,
                'label' => 'condition_type.time.to',
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'ngbm_condition_type_time';
    }
}
