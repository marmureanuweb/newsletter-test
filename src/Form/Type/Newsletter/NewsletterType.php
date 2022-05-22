<?php

declare(strict_types=1);

namespace App\Form\Type\Newsletter;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class NewsletterType extends AbstractResourceType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        parent::buildForm($builder, $options);

        $builder
            ->add('content', TextType::class, [
                'required' => false,
                'label' => 'ASDADASD ADASD'
            ])
        ;
    }

//    public function getBlockPrefix(): string
//    {
//        return 'newsletter';
//    }
    public static function getExtendedTypes(): iterable
    {
        return [
            AbstractResourceType::class
        ];
    }
}
