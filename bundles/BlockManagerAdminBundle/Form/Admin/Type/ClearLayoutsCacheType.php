<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type;

use Netgen\BlockManager\API\Values\Layout\LayoutList;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ClearLayoutsCacheType extends AbstractType
{
    use ChoicesAsValuesTrait;

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(['layouts']);
        $resolver->setAllowedTypes('layouts', LayoutList::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'layouts',
            ChoiceType::class,
            [
                'choices' => $options['layouts'],
                'choice_value' => 'id',
                'choice_name' => 'id',
                'choice_label' => 'name',
                'translation_domain' => false,
                'choice_translation_domain' => false,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'constraints' => [new NotBlank()],
            ] + $this->getChoicesAsValuesOption()
        );

        $builder->get('layouts')->addModelTransformer(new LayoutListTransformer());
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        foreach ($options['layouts'] as $layout) {
            /* @var \Netgen\BlockManager\API\Values\Layout\Layout $layout */
            $view['layouts'][$layout->getId()]->vars['layout'] = $layout;
        }
    }
}
