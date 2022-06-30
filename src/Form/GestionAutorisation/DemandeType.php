<?php

namespace App\Form\GestionAutorisation;

use App\Entity\Autorisation\Autorisation;
use App\Entity\AutorisationOffre\Demande;
use App\Entity\User;
use App\Repository\DepartementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class DemandeType extends AbstractType
{

    /**
     * @var DepartementRepository
     */
    private $departementRepository;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(DepartementRepository $departementRepository, TokenStorageInterface $tokenStorage)
    {
        $this->departementRepository = $departementRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet', TextType::class, [
                'label' => 'Objet',
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('corps', TextareaType::class, [
                'label' => "Complément d'informations",
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'id' => 'summernote',
                    'class' => 'form-control',
                    'rows' => 10,
                    'style' => "resize: none;"
                ]
            ])
            ->add('documents', FileType::class, [
                'label' => 'Joindre des fichiers à la demande',
                'data_class'=>null,
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new All(
                        new File([
                            'maxSize' => '200M'
                        ])
                    )
                ],
            ]);

        //Un agent juridique vient directement il crée la demande pour quelqu'un d'autre
        /* @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        if(in_array('ROLE_JURIDIQUE', $user->getRoles()) || in_array('ROLE_USER_BOSS_JURIDIQUE', $user->getRoles())){
            $users = [];
            //Récupération de tous les membres des départements non-juridiques
            $depts = $this->departementRepository->notMembersJuridique();
            foreach ($depts as $d){
                $departementName = $d->getLib();
                $members = $d->getUsers()->getValues();
                //Ajout en sous-groupes
                $users['Département ' . $departementName] = $members;
            }

            $builder->add('createdBy', ChoiceType::class, [
                'label' => "Agent demandeur",
                'empty_data' => '',
                'choices' => $users,
                'choice_label'=> function($choice, $key, $value){
                //Affichage du non d'utilisateur
                    return $choice->displayName();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ;
        }
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Autorisation::class,
        ]);
    }
}
