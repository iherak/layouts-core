<?php

namespace Netgen\BlockManager\Collection\Form;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Form\TranslatableType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class EditType extends TranslatableType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('collection');
        $resolver->setAllowedTypes('collection', Collection::class);
        $resolver->setAllowedTypes('data', CollectionUpdateStruct::class);

        $resolver->setDefault('translation_domain', 'ngbm_forms');
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['collection'] = $options['collection'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'offset',
            IntegerType::class,
            array(
                'label' => 'collection.offset',
                'property_path' => 'offset',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'int')),
                    new Constraints\GreaterThanOrEqual(array('value' => 0)),
                ),
            )
        );

        $builder->add(
            'limit',
            IntegerType::class,
            array(
                'label' => 'collection.limit',
                'property_path' => 'limit',
                'constraints' => array(
                    new Constraints\Type(array('type' => 'int')),
                    new Constraints\GreaterThanOrEqual(array('value' => 0)),
                ),
            )
        );

        $builder->setDataMapper(new CollectionDataMapper());
    }
}
