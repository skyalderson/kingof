<?php

namespace App\Form;

use App\Entity\Board;
use App\Entity\Game;
use App\Entity\Mode;
use App\Entity\Monster;
use App\Entity\Rule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    private function getChoicesMonstersSelect()
    {
        $choices = Game::SELECT_MONSTERS_TYPE;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $k;
        }

        return $output;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [])
            ->add('maxplayers', IntegerType::class, ['attr' => [
                            'min' => 2,
                            'max' => 6,
                        ],
                    ])
            ->add('monstersSelect', ChoiceType::class, [
                    'label' => 'Sélection Monstres',
                    'choices' => [$this->getChoicesMonstersSelect()],
                    ])
            ->add('board', EntityType::class, [
                'class' => Board::class,
                'choice_label' => 'name',
                'label' => 'Jeu',
                'choice_attr' => function ($board) {
                    $_return['class'] = 'text-yellow ';

                    $_return['checked'] = (1 == $board->getId()) ? true : false;

                    if (1 == $board->getAvailable()) {
                        $_return['disabled'] = false;
                    } else {
                        $_return['disabled'] = true;
                    }

                    return $_return;
                },
                ])

            ->add('rules', EntityType::class, [
                    'class' => Rule::class,
                    'choice_label' => 'name',
                    'expanded' => true,
                    'multiple' => true,
                    'label' => 'Règles additionnelles',

                    'choice_attr' => function ($rule) {
                        $_return = [];
                        $_classes = [];

                        $_return['disabled'] = (1 == $rule->getAvailable()) ? false : true;
                        if (0 == $rule->getAvailable()) {
                            $_classes[] = 'rule_notav';
                        }
                        $_boards = $rule->getApplicableToBoard();

                        foreach ($_boards as $b) {
                            if (1 == $b->getId()) {
                                $_classes[] = 'KoT';
                            }
                            if (2 == $b->getId()) {
                                $_classes[] = 'KoN';
                            }
                        }
                        $classes = implode(' ', $_classes);
                        $_return['class'] = $classes;

                        return $_return;
                    },
                    'group_by' => function ($rule) {
                        return $rule->getBox()->getShortName();
                    },
                    ])
            ->add('mode', EntityType::class, [
                'class' => Mode::class,
                'choice_label' => 'name',
                'choice_attr' => function ($mode) {
                    if (1 == $mode->getAvailable()) {
                        return ['disabled' => false];
                    } else {
                        return ['disabled' => true];
                    }
                },
                ])
            ->add('monstersAuthorized', EntityType::class, [
                'class' => Monster::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'label' => 'Monstres autorisés',
                'choice_attr' => function ($monster) {
                    $_return = [];
                    $_return['checked'] = true;
                    $_return['disabled'] = (1 == $monster->getAvailable()) ? false : true;
                    if (false == $monster->getAvailable()) {
                        $_return["class"] = 'monster_notav';
                        $_return['disabled'] = true;
                    }
                    else {
                        $_return['disabled'] = false;
                        $_return["class"] = '';
                    }


                    return $_return;


                },
                'group_by' => function ($monster) {
                    return $monster->getBox()->getBoxType()->getName();
                },
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
