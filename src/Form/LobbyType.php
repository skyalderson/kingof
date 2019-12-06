<?php

namespace App\Form;

use App\Entity\Monster;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LobbyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $game = $options['data']['game'];

        $builder->add('monster', EntityType::class, [
            'class' => Monster::class,
            'choices' => $game->getMonstersAuthorized(),
            'choice_label' => 'name',
            'placeholder' => 'Choisissez votre monstre',
        ])->add('ready', ChoiceType::class, [
            'choices' => ['Prêt' => 1],
            'expanded' => true,
            'multiple' => true,
        ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
