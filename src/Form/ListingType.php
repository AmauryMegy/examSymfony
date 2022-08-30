<?php

namespace App\Form;

use App\Entity\Listing;
use App\Entity\Model;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('producedYear', NumberType::class, [
                'label' => 'Année de production',
                'attr' => [
                    'placeholder' => 'Année de production',
                    'min' => 0
                ],
            ])
            ->add('mileage', NumberType::class, [
                'label' => 'Kilométrage',
                'attr' => [
                    'placeholder' => 'Kilométrage',
                    'min' => 0
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'attr' => [
                    'placeholder' => 'Prix',
                    'min' => 0
                ],
            ])
            ->add('createdAt', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'attr' => [
                    'max' => date('Y-m-d')
                ]
            ])
            ->add('model', EntityType::class, [
                'required' => false,
                'class' => Model::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('b')
                        ->orderBy('b.name', 'ASC')
                        ;
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Listing::class,
        ]);
    }
}
