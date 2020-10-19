<?php

namespace App\Form\Type;

use App\Entity\Phone;
use App\Entity\SharedPhone;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class SharedPhoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('to_user', EntityType::class, [
                'class' => User::class,
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('phone', EntityType::class, [
                'class' => Phone::class,
                'constraints' => [
                    new NotNull(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SharedPhone::class,
        ]);
    }
}