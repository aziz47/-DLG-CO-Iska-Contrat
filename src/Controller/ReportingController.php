<?php

namespace App\Controller;

use App\Entity\Abstracts\ProcessObj;
use App\Entity\Autorisation\Autorisation;
use App\Entity\AvisConseils\Avis;
use App\Entity\Contrat\Contrat;
use App\Entity\GestionContentieux\Litige;
use App\Entity\Obligation\Obligation;
use App\Entity\Stats\UserJuridiqueStats;
use App\Entity\User;
use App\Message\CreateExcel;
use App\Message\CreatePdf;
use App\Repository\Contrat\ContratRepository;
use App\Repository\Contrat\ModeFacturationRepository;
use App\Repository\Contrat\TypeDemandeContratRepository;
use App\Repository\DepartementRepository;
use App\Repository\GestionContentieux\LitigeRepository;
use App\Repository\Obligation\ImportanceObligationRepository;
use App\Repository\Obligation\SourceListeRepository;
use App\Repository\Obligation\StatutObligationRepository;
use App\Repository\Stats\UserJuridiqueStatsRepository;
use App\Repository\UserJuridiqueRepository;
use App\Service\ProcessObjCurrentStateToStringService;
use Carbon\CarbonInterval;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/apps/autres")
 */
class ReportingController extends AbstractController
{
    /**
     * @var DepartementRepository
     */
    private $departementRepository;
    /**
     * @var TypeDemandeContratRepository
     */
    private $typeDemandeContratRepository;
    /**
     * @var ModeFacturationRepository
     */
    private $modeFacturationRepository;
    /**
     * @var ImportanceObligationRepository
     */
    private $importanceObligationRepository;
    /**
     * @var StatutObligationRepository
     */
    private $statutObligationRepository;
    /**
     * @var SourceListeRepository
     */
    private $sourceListeRepository;
    /**
     * @var UserJuridiqueRepository
     */
    private $userJuridiqueRepository;
    /**
     * @var ContratRepository
     */
    private $contratRepository;
    /**
     * @var LitigeRepository
     */
    private $litigeRepository;
    /**
     * @var UserJuridiqueStatsRepository
     */
    private $userJuridiqueStatsRepository;

    public function __construct(UserJuridiqueStatsRepository $userJuridiqueStatsRepository, LitigeRepository $litigeRepository, ContratRepository $contratRepository, UserJuridiqueRepository $userJuridiqueRepository, DepartementRepository $departementRepository, TypeDemandeContratRepository $typeDemandeContratRepository, ModeFacturationRepository $modeFacturationRepository, ImportanceObligationRepository $importanceObligationRepository, StatutObligationRepository $statutObligationRepository, SourceListeRepository $sourceListeRepository)
    {
        $this->departementRepository = $departementRepository;
        $this->typeDemandeContratRepository = $typeDemandeContratRepository;
        $this->modeFacturationRepository = $modeFacturationRepository;
        $this->importanceObligationRepository = $importanceObligationRepository;
        $this->statutObligationRepository = $statutObligationRepository;
        $this->sourceListeRepository = $sourceListeRepository;
        $this->userJuridiqueRepository = $userJuridiqueRepository;
        $this->contratRepository = $contratRepository;
        $this->litigeRepository = $litigeRepository;
        $this->userJuridiqueStatsRepository = $userJuridiqueStatsRepository;
    }

    /**
     * Page d'accueil du reporting
     * @Route("/", name="app_reporting_home", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('apps/reporting/index.html.twig', [
            'controller_name' => 'ReportingController',
        ]);
    }

    /**
     * Récupérer le tableau lié aux données recherchées
     * @Route("/table/{processStr}")
     */
    public function table(string $processStr){
        return $this->render('apps/reporting/table/' . $processStr . '.html.twig');
    }

    /**
     * Filtres des données recherchées
     * @Route("/filter/{processStr}")
     */
    public function filter(string $processStr){
        //On initialise les options de recherches à un tableau vide
        $opts = [];
        //Par défaut, on peut faire des recherches sur tout les objets en fonction du département
        $opts['deps'] = $this->departementRepository->findAll();
        //Les champs de tris sont personnalisés, on récupère automatiquement les valeurs pour remplir les champs et qui seront transmises au champ twig
        if($processStr === 'demande_contrat' || $processStr === 'contrat'){
            $opts['typeDemandes'] = $this->typeDemandeContratRepository->findAll();
            $opts['modeFacturations'] = $this->modeFacturationRepository->findAll();
        }elseif($processStr === 'obligations'){
            $opts['statut'] = $this->statutObligationRepository->findAll();
            $opts['importanceObligation'] = $this->importanceObligationRepository->findAll();
            $opts['sourceList'] = $this->sourceListeRepository->findAll();
        }else if($processStr === 'collaborateurs_juridique'){
            $opts['agents'] = $this->userJuridiqueRepository->findAll();
        }

        return $this->render('apps/reporting/filter/' . $processStr . '.html.twig', $opts);
    }

    /**
     * Filtrer les données recherchées
     * @Route("/print", methods={"POST"})
     */
    public function print(Request $request, ManagerRegistry $doctrine, ProcessObjCurrentStateToStringService $currentStateToStringService)
    {
        //Récupération de toutes les valeurs transmises par les filtres
        $json = json_decode(
            $request->getContent()
        );
        //Objet recherché
        $processStr = $json->processStr;
        //En-tête du tableau en fonction de l'objet recherché
        $headers = [
          'demande_contrat' => ['Identifiant', 'Objet', 'Créé par', 'Statut', 'Date', 'Délai de dénonciation', 'Type'],
          'avis'  => ['Identifiant', 'Objet / Question', 'Créé par', 'Renseignement', 'Niveau d\'exécution', 'Statut'],
          'autorisation'  => ['Identifiant', 'Objet', 'Créé par', 'Statut', 'Créé le'],
          'contrat' => ['Identifiant', 'Objet', 'Créé par', 'Statut', 'Date', 'Type'],
          'litige'  => ['Identifiant', 'Statut', 'Nature', 'Date du cas', 'Nom des parties', 'Juridiction', 'Avocat'],
          'obligations' => ['Identifiant', 'Statut', 'Sources', 'Références', 'Responsables'],
          'collaborateurs_juridique' => ['Identifiant', 'Nom', 'Temps moyen de traitement - Contrat', 'Temps moyen de traitement - Demande d\'avis'],
        ];
        //Valeurs de remplissage du tableau
        $res = [];
        //Valeurs transmises par les champs de tris
        $searchParams = [];

        if($processStr === 'avis'){
            $repo = $doctrine->getRepository(Avis::class);

            isset($json->currentState) ? ($searchParams['currentState'] = $json->currentState) : null ;
            isset($json->departementInitiateur) ? ($searchParams['departementInitiateur'] = intval($json->departementInitiateur)) : null ;

            /* @var $e Avis[] */
            $e = $repo->findBy($searchParams);
            foreach ($e as $elt){
                $res[] = [
                    'id' => $elt->getId(),
                    'objet' => $elt->getObjet(),
                    'createdBy' => $elt->getCreatedBy()->displayName(),
                    'renseignement' => $elt->getRenseignement(),
                    'niveauExec' => $elt->getNiveauExecution(),
                    'statut' => $this->currentStateSimply($elt->getCurrentState())
                ];
            }
        }
        elseif($processStr === 'autorisation'){
            $repo = $doctrine->getRepository(Autorisation::class);
            isset($json->currentState) ? ($searchParams['currentState'] = $json->currentState) : null ;
            isset($json->departementInitiateur) ? ($searchParams['departementInitiateur'] = intval($json->departementInitiateur)) : null ;

            /* @var $e Avis[] */
            $e = $repo->findBy($searchParams);
            foreach ($e as $elt){
                $res[] = [
                    'id' => $elt->getId(),
                    'objet' => $elt->getObjet(),
                    'createdBy' => $elt->getCreatedBy()->displayName(),
                    //Transformation des valeurs currentState en champs compréhensibles par les humains
                    'currentState' => $currentStateToStringService($elt->getCurrentState()),
                    'createdAt' => $elt->getCreatedAt()->format('d/m/Y'),
                ];
            }
        }
        elseif($processStr === 'contrat'){
            isset($json->currentState) ? ($searchParams['currentState'] = $json->currentState) : null ;
            isset($json->typeDemande) ? ($searchParams['typeDemande'] = intval($json->typeDemande)) : null ;
            isset($json->modeFacturation) ? ($searchParams['typeDemande'] = intval($json->modeFacturation)) : null ;
            isset($json->departementInitiateur) ? ($searchParams['departementInitiateur'] = intval($json->departementInitiateur)) : null ;

            /* @var $e Contrat[] */
            $e = $this->contratRepository->findBy($searchParams);

            foreach ($e as $elt){
                $res[] = [
                    'id' => $elt->getId(),
                    'objet' => $elt->getObjet(),
                    'createdBy' => $elt->getCreatedBy()->displayName(),
                    //Transformation des valeurs currentState en champs compréhensibles par les humains
                    'currentState' => $currentStateToStringService($elt->getCurrentState()),
                    'createdAt' => $elt->getCreatedAt()->format('d/m/Y'),
                    'Type' => $elt->getTypeDemande()->getLib()
                ];
            }
        }
        elseif($processStr === 'litige'){
            isset($json->isClosed) ? ($searchParams['isClosed'] = ($json->isClosed === 'false') ? false : true ) : null ;

            $res = [];
            /* @var $e Litige[] */
            $e = $this->litigeRepository->findBy($searchParams);

            foreach ($e as $elt){
                $res[] = [
                    'id' => $elt->getId(),
                    'isClosed' => $elt->getIsClosed() ? 'Fermé' : 'En cours',
                    'nature' => $elt->getNature(),
                    'dateCas' => $elt->getDateCas()->format('d/m/Y'),
                    'parties' => $elt->getPartieDemandeur() . ' ' . $elt->getPartieDefendeur(),
                    'juridiction' => $elt->getJuridiction(),
                    'avocat' => $elt->getAvocat()
                ];
            }
        }
        elseif($processStr === 'obligations'){
            isset($json->statut) ? ($searchParams['statut'] = $json->statut) : null ;
            isset($json->sourceList) ? ($searchParams['sourceList'] = $json->sourceList) : null ;
            isset($json->importanceObligation) ? ($searchParams['importanceObligation'] = $json->importanceObligation) : null ;

            $res = [];
            /* @var $e Obligation[] */
            $e = $this->litigeRepository->findBy($searchParams);

            foreach ($e as $elt){
                $res[] = [
                    'id' => $elt->getId(),
                    'statut' => $elt->getStatut()->getLib(),
                    'sourceDisposition' => $elt->getSourceDisposition(),
                    'reference' => $elt->getReference(),
                    'pointsAbordes' => $elt->getPointsAbordes(),
                    'obligation' => $elt->getObligation(),
                    'sanctions' => $elt->getSanctions(),
                    'prevues' => $elt->getPrevues(),
                    'responsable' => $elt->getResponsable()->getUser()->displayName(),
                ];
            }
        }
        elseif($processStr === 'collaborateurs_juridique'){
            isset($json->agent) ? ($searchParams['uJuridique'] = $json->agent) : null ;

            $res = [];
            $e = $this->userJuridiqueStatsRepository->findBy($searchParams);
            foreach ($e as $elt){
                $tempsContrat = CarbonInterval::seconds(
                    $elt->getPerfContrat()
                )->cascade()->forHumans();

                $tempsAvis = CarbonInterval::seconds(
                    $elt->getPerfAvisConseils()
                )->cascade()->forHumans();

                $res[] = [
                    'id' => $elt->getId(),
                    'nom' => $elt->getUJuridique()->getUser()->displayName(),
                    'contrat' => $tempsContrat === '1 second' ? 'Néant' : $tempsContrat,
                    'avis' => $tempsAvis === '1 second' ? 'Néant' : $tempsAvis,
                ];
            }
        }

        return new Response(
            //Encodage au format JSON
            json_encode(
                [
                    //On retourne les entêtes
                    'headers' => $headers[$processStr],
                    //On retourne les valeurs
                    'values' => $res
                ]
            )
        );
    }

    /**
     * Filtrer les données recherchées
     * @Route("/report-to-mail", methods={"POST"})
     */
    public function reportToMail(Request $request, ManagerRegistry $doctrine, ProcessObjCurrentStateToStringService $currentStateToStringService)
    {
        //Récupération de toutes les valeurs transmises par les filtres
        $json = json_decode(
            $request->getContent()
        );
        //Format demandé
        $format = $json->format;
        //Objet recherché
        $processStr = $json->processStr;
        //En-tête du tableau en fonction de l'objet recherché
        $headers = [
          'demande_contrat' => ['Identifiant', 'Objet', 'Créé par', 'Statut', 'Date', 'Délai de dénonciation', 'Type'],
          'avis'  => ['Identifiant', 'Objet / Question', 'Créé par', 'Renseignement', 'Niveau d\'exécution', 'Statut'],
          'autorisation'  => ['Identifiant', 'Objet', 'Créé par', 'Statut', 'Créé le'],
          'contrat' => ['Identifiant', 'Objet', 'Créé par', 'Statut', 'Date', 'Type'],
          'litige'  => ['Identifiant', 'Statut', 'Nature', 'Date du cas', 'Nom des parties', 'Juridiction', 'Avocat'],
          'obligations' => ['Identifiant', 'Statut', 'Sources', 'Références', 'Responsables'],
          'collaborateurs_juridique' => ['Identifiant', 'Nom', 'Temps moyen de traitement - Contrat', 'Temps moyen de traitement - Demande d\'avis'],
        ];
        //Valeurs de remplissage du tableau
        $res = [];
        //Valeurs transmises par les champs de tris
        $searchParams = [];
        $e = null;
        if($processStr === 'avis'){
            $repo = $doctrine->getRepository(Avis::class);

            isset($json->currentState) ? ($searchParams['currentState'] = $json->currentState) : null ;
            isset($json->departementInitiateur) ? ($searchParams['departementInitiateur'] = intval($json->departementInitiateur)) : null ;

            /* @var $e Avis[] */
            $e = $repo->findBy($searchParams);
        }
        elseif($processStr === 'autorisation'){
            $repo = $doctrine->getRepository(Autorisation::class);
            isset($json->currentState) ? ($searchParams['currentState'] = $json->currentState) : null ;
            isset($json->departementInitiateur) ? ($searchParams['departementInitiateur'] = intval($json->departementInitiateur)) : null ;

            /* @var $e Avis[] */
            $e = $repo->findBy($searchParams);
        }
        elseif($processStr === 'contrat'){
            isset($json->currentState) ? ($searchParams['currentState'] = $json->currentState) : null ;
            isset($json->typeDemande) ? ($searchParams['typeDemande'] = intval($json->typeDemande)) : null ;
            isset($json->modeFacturation) ? ($searchParams['typeDemande'] = intval($json->modeFacturation)) : null ;
            isset($json->departementInitiateur) ? ($searchParams['departementInitiateur'] = intval($json->departementInitiateur)) : null ;

            /* @var $e Contrat[] */
            $e = $this->contratRepository->findBy($searchParams);
        }
        elseif($processStr === 'litige'){
            isset($json->isClosed) ? ($searchParams['isClosed'] = ($json->isClosed === 'false') ? false : true ) : null ;

            /* @var $e Litige[] */
            $e = $this->litigeRepository->findBy($searchParams);
        }
        elseif($processStr === 'obligations'){
            isset($json->statut) ? ($searchParams['statut'] = $json->statut) : null ;
            isset($json->sourceList) ? ($searchParams['sourceList'] = $json->sourceList) : null ;
            isset($json->importanceObligation) ? ($searchParams['importanceObligation'] = $json->importanceObligation) : null ;

            /* @var $e Obligation[] */
            $e = $this->litigeRepository->findBy($searchParams);
        }
        elseif($processStr === 'collaborateurs_juridique'){
            isset($json->agent) ? ($searchParams['uJuridique'] = $json->agent) : null ;
            $e = $this->userJuridiqueStatsRepository->findBy($searchParams);

            foreach ($e as $elt){
                $tempsContrat = CarbonInterval::seconds(
                    $elt->getPerfContrat()
                )->cascade()->forHumans();

                $tempsAvis = CarbonInterval::seconds(
                    $elt->getPerfAvisConseils()
                )->cascade()->forHumans();

                $res[] = [
                    'id' => $elt->getId(),
                    'nom' => $elt->getUJuridique()->getUser()->displayName(),
                    'contrat' => $tempsContrat === '1 second' ? 'Néant' : $tempsContrat,
                    'avis' => $tempsAvis === '1 second' ? 'Néant' : $tempsAvis,
                ];
            }

            $e = $res;
        }

        /* @var User $user */
        $user = $this->getUser();

        $class = 'App\\Message\\'.($format === 'PDF' ? 'CreatePdf' : 'CreateExcel');
        $file = new $class($processStr,
            $this->renderView(
                'apps/reporting/mail/' . $format . '/' . $processStr .'.html.twig',
                [
                    'filtersData' => $e,
                    'headers' => $headers[$processStr],
                ]
            ),
            $user
        );

        $this->dispatchMessage(
            $file
        );

        return new Response(
            //Encodage au format JSON
            json_encode(
                [
                    'result' => true
                ]
            )
        );
    }

    public function currentStateSimply($value)
    {
        $statut = [
            "en_attente_manager" => "En attente de validation par le chef de département",
            "demande_rejetee_manager" => "Demande rejétée par le manager",
            "demande_acceptee_manager" => "Demande acceptée par le manager",
            "demande_non_attribuee" => "Demande au niveau du service juridique en attente d'attribution",
            "demande_attribuee" => "Demande attribuée",
            "demande_validee" => "Demande validée",
            "demande_rejetee" => "Demande rejétée",
        ];

        return $statut[$value];
    }
}
