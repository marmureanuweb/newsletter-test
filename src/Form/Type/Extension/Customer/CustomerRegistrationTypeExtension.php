<?php

declare(strict_types=1);

namespace App\Form\Type\Extension\Customer;

use App\Entity\Newsletter;
use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerRegistrationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerRegistrationTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add('subscribedToNewsletter', EntityType::class, [
                'class' => Newsletter::class,
                'expanded' => true,
//                'multiple' => true,
                'required' => false,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'query_builder' => function (EntityRepository $newsletter) {
                    return $newsletter->createQueryBuilder('s')
                        ->orderBy('s.id', 'ASC')
                        ->andWhere('s.status = :status')
                        ->setParameter('status', 1);
                },
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [
            CustomerRegistrationType::class,
        ];
    }
}
