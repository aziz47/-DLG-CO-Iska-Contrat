<?php

namespace App\Form\GestionContrat;

use App\Entity\Contrat\Contrat;
use App\Repository\Contrat\ModeFacturationRepository;
use App\Repository\Contrat\ModeReglementRepository;
use App\Repository\Contrat\ModeRenouvellementRepository;
use App\Repository\Contrat\TypeDemandeContratRepository;
use App\Repository\DepartementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;

class ContratNewType extends AbstractType
{
    /**
     * @var TypeDemandeContratRepository
     */
    private $typeDemandeContratRepository;
    /**
     * @var DepartementRepository
     */
    private $departementRepository;
    /**
     * @var ModeFacturationRepository
     */
    private $modeFacturationRepository;
    /**
     * @var ModeReglementRepository
     */
    private $modeReglementRepository;
    /**
     * @var ModeRenouvellementRepository
     */
    private $modeRenouvellementRepository;
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security, ModeRenouvellementRepository $modeRenouvellementRepository, ModeReglementRepository $modeReglementRepository, ModeFacturationRepository $modeFacturationRepository, DepartementRepository $departementRepository, TypeDemandeContratRepository $typeDemandeContratRepository)
    {
        $this->typeDemandeContratRepository = $typeDemandeContratRepository;
        $this->departementRepository = $departementRepository;
        $this->modeFacturationRepository = $modeFacturationRepository;
        $this->modeReglementRepository = $modeReglementRepository;
        $this->modeRenouvellementRepository = $modeRenouvellementRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mois = [];
        for($i = 0; $i < 12; $i++){
            $mois[] = ($i + 1).' mois';
        }

        $builder
            ->add('objet', TextType::class, [
                'label' => 'Objet du contrat',
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('identiteCocontractant', TextType::class, [
                'label' => 'Identité du co-contractant',
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('clausesParticulieres', TextareaType::class, [
                'label' => 'Clauses particulières',
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'id' => 'summernote',
                    'class' => 'form-control',
                    'rows' => 10,
                    'style' => "resize: none;"
                ]
            ])
            /*->add('dureeContrat', ChoiceType::class, [
                'label' => 'Durée du contrat',
                'choices' => $mois,
                'choice_label'=> function($choice, $key, $value){
                    return $value;
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])*/
            ->add('delaiDenonciation', ChoiceType::class, [
                'label' => 'Délai de dénonciation',
                'choices' => $mois,
                'choice_label'=> function($choice, $key, $value){
                    return $value;
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('periodicitePaiement', ChoiceType::class, [
                'label' => 'Périodicité de paiement',
                'choices' => $mois,
                'choice_label'=> function($choice, $key, $value){
                    return $value;
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('typeDemande', ChoiceType::class, [
                'label' => "Type de demande",
                'choices' => $this->typeDemandeContratRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('modeFacturation', ChoiceType::class, [
                'label' => "Mode de facturation",
                'choices' => $this->modeFacturationRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('modeReglement', ChoiceType::class, [
                'label' => "Mode de paiement",
                'choices' => $this->modeReglementRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('modeRenouvellement', ChoiceType::class, [
                'label' => "Mode de renouvellement",
                'choices' => $this->modeRenouvellementRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('departementInitiateur', ChoiceType::class, [
                'label' => "Département initiateur",
                'choices' => [$this->security->getUser()->getDepartement()],
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control',
                    'disabled' => true
                ]
            ])
            ->add('entreeVigueurAt', DateType::class, [
                'label' => "Date d'entrée en vigueur",
                'attr' => [
                    'class' => 'form-control datetimepicker-input',
                ]
            ])
            ->add('finContratAt', DateType::class, [
                'label' => "Date de fin du contrat",
                'attr' => [
                    'class' => 'form-control datetimepicker-input',
                ]
            ])
            ->add('objetConditionModification', TextType::class, [
                'label' => "Objet des conditions de modifications",
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'required' => false
                ]
            ])
            ->add('libConditionModification', TextareaType::class, [
                'label' => "Détails des conditions de modifications",
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'required' => false
                ]
            ])
            ->add('documents', FileType::class, [
                'label' => 'Joindre des fichiers à la demande',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
