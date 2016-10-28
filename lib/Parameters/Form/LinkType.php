<?php

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\BlockManager\Parameters\Form\DataMapper\ItemLinkDataMapper;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserDynamicType;
use Netgen\BlockManager\Parameters\Parameter\Link;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('value_types'));
        $resolver->setAllowedTypes('value_types', 'array');
        $resolver->setDefault('value_types', array());
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'link_type',
            ChoiceType::class,
            array(
                'label' => 'forms.uri.link_type',
                'choices' => array(
                        'forms.uri.link_type.url' => Link::LINK_TYPE_URL,
                        'forms.uri.link_type.email' => Link::LINK_TYPE_EMAIL,
                        'forms.uri.link_type.phone' => Link::LINK_TYPE_PHONE,
                        'forms.uri.link_type.internal' => Link::LINK_TYPE_INTERNAL,
                    ),
                'choices_as_values' => true,
                'placeholder' => $options['required'] ? false : 'forms.uri.link_type.none',
                'required' => true,
            )
        );

        $builder->add(
            Link::LINK_TYPE_URL,
            UrlType::class,
            array(
                'label' => 'forms.uri.link_type.url',
            )
        );

        $builder->add(
            Link::LINK_TYPE_EMAIL,
            EmailType::class,
            array(
                'label' => 'forms.uri.link_type.email',
            )
        );

        $builder->add(
            Link::LINK_TYPE_PHONE,
            TextType::class,
            array(
                'label' => 'forms.uri.link_type.phone',
            )
        );

        $internalLinkForm = $builder->create(
            Link::LINK_TYPE_INTERNAL,
            ContentBrowserDynamicType::class,
            array(
                'label' => 'forms.uri.link_type.internal',
                'item_types' => $options['value_types'],
            )
        );

        $internalLinkForm->setDataMapper(new ItemLinkDataMapper());
        $builder->add($internalLinkForm);

        // We use the hidden field to collect the validation errors and
        // to show them in the right place using a template (in one of url,
        // email, internal forms) since we can't use error_mapping option
        // to do it automatically based on submitted data
        $builder->add(
            'link',
            HiddenType::class,
            array(
                'mapped' => false,
                'error_bubbling' => false,
            )
        );

        $builder->add(
            'link_suffix',
            TextType::class,
            array(
                'label' => 'forms.uri.link_suffix',
            )
        );

        $builder->add(
            'new_window',
            CheckboxType::class,
            array(
                'label' => 'forms.uri.new_window',
                'required' => true,
            )
        );
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefixes default to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'ngbm_link';
    }
}
