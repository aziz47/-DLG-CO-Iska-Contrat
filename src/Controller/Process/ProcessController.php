<?php

namespace App\Controller\Process;

use App\Entity\{Abstracts\ProcessObj,
    Autorisation\Autorisation,
    AvisConseils\Avis,
    Contrat\Contrat,
    Contrat\DocumentContrat,
    User};
use App\Form\{GestionAutorisation\DemandeType, GestionAvis\AvisType, GestionContrat\ContratNewType};
use App\Repository\UserJuridiqueRepository;
use App\Service\{AttributionJuridiqueProcess,
    CreatedMailProcess,
    EditProcessObj,
    ManagerProcess,
    SaveProcessObj,
    TraitementJuridiqueProcess,
    ValidationManagerProcess};
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\{HttpFoundation\Request,
    HttpFoundation\Response,
    HttpFoundation\Session\SessionInterface,
    Routing\Annotation\Route,
    Workflow\WorkflowInterface};
//TO-DO : Corriger bug impossible d'envoyer mail et valider demande
/**
 * @Route("/apps/process")
 */
class ProcessController extends AbstractController
{
    private const START_WORKFLOW_STATE = 'en_attente_manager';

    private const PROCESS_CLASSES = [
        'avis' => [
            'class' => Avis::class,
            'slug' => 'avis',
            'obj' => 'App\Entity\AvisConseils\Avis',
            'new' => AvisType::class,
            'show' => 4,
            'edit' => 3,
            'upload_folder' => 'avis_doc_directory'
        ],
        'contrat' => [
            'class' => Contrat::class,
            'obj' => 'App\Entity\Contrat\Contrat',
            'new' => ContratNewType::class,
            'upload_folder' => 'contrat_doc_directory'
        ],
        'autorisation' => [
            'class' => Autorisation::class,
            'obj' => 'App\Entity\Autorisation\Autorisation',
            'new' => DemandeType::class,
            'upload_folder' => 'contrat_doc_directory'
        ]
    ];

    /**
     * @var WorkflowInterface
     */
    private $gestionProcessStateMachine;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(WorkflowInterface $gestionProcessStateMachine, SessionInterface $session)
    {
        $this->gestionProcessStateMachine = $gestionProcessStateMachine;
        $this->session = $session;
    }

    /**
     * Page d'accueil pour tout les process
     * @Route("/{processObj}", name="apps_process_home", methods={"GET"})
     */
    public function home($processObj){
        $perms = $this->setPermsSession($this->session);
        $this->checkProcessObjStr($processObj);

        $_els = [
            'page_title' => $processObj . '_title'
        ];

        return $this->render('apps/gestion_process/index.html.twig', array_merge($_els, [
            'processObj' => $processObj,
            'perms' => $perms,
            'status' => $perms
        ]));
    }

    /**
     * Page de création des process obj
     * @Route("/{processObj}/new", name="apps_process_new", methods={"GET", "POST"})
     */
    public function new($processObj, Request $request, SaveProcessObj $saveProcessObj){
        $this->checkProcessObjStr($processObj);
        $className = self::PROCESS_CLASSES[$processObj]['obj'];
        $classe = new $className;

        $form = $this->createForm(self::PROCESS_CLASSES[$processObj]['new'], $classe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /* @var User $user */
            $user = $this->getUser();

            $saveProcessObj->run($classe, $user, [
                'documents' => $form->get('documents')->getData(),
                'upload_folder' => $this->getParameter(self::PROCESS_CLASSES[$processObj]['upload_folder'])
            ]);

            $this->addFlash(
                'success',
                'Votre demande a bien été enregistrée.'
            );

            return $this->redirectToRoute('apps_process_home', ['processObj' => $processObj]);
        }

        return $this->render('apps/gestion_process/new.html.twig', [
            'form' => $form->createView(),
            'processObj' => $processObj
        ]);
    }

    /**
     * Page de validation par le manager
     * @IsGranted("ROLE_USER_MANAGER")
     * @Route("/{processObj}/manager/{id}", name="apps_process_validation_manager", methods={"GET", "POST"})
     */
    public function validationManager(string $processObj, int $id, ManagerRegistry $doctrine, Request $request, ManagerProcess $managerProcessSrv){
        $this->checkProcessObjStr($processObj);

        $classe = self::PROCESS_CLASSES[$processObj]['class'];
        $repo = $doctrine->getRepository($classe);
        //On récupére l'objet correspondant à partir de son ID
        /* @var $e ProcessObj */
        $e = $repo->findOneBy(['id' => $id]);
        if($e === null){
            throw new \Exception("L'objet demandé n'existe pas !");
        }
        //On vérifie si la demande provient bien du département du manager qui la consulte
        /* @var User $user */
        $user = $this->getUser();
        if ($e->getDepartementInitiateur() !== $user->getDepartement()){
            throw new \Exception("Vous n'êtes pas autorisé à effectuer cette action.");
        }
        //Si la demande n'est pas dans le bonne état, on lève une erreur
        if($e->getCurrentState() != 'en_attente_manager'){
            throw new \Exception("Action non autorisée.");
        }

        //On récupére le choix effecuté par le manager
        $choice = $request->request->get('choice');
        if($choice != null){
            $transition = $choice == 'yes' ? 'valider_manager' : 'refuser_manager';
            $managerProcessSrv->run($e, $transition);
            //TODO : Mettre en place des messages de succès personnalisés
            $this->addFlash(
                'success',
                'La demande a bien été ' . ($choice == 'yes' ? 'validée' : 'refusée') . '.'
            );

            return $this->redirectToRoute('apps_process_home', ['processObj' => $processObj]);
        }

        return $this->render('apps/gestion_process/manager.html.twig', [
            //Obj pour initialisation du formulaire
            'obj' => $e,
            //Slug pour afficher le form non-modifiable
            'processObj' => $processObj
        ]);
    }

    /**
     * Page d'attribution des process obj
     * @IsGranted("ROLE_USER_BOSS_JURIDIQUE")
     * @Route("/{processObj}/attrib/{id}", name="apps_process_attribution", methods={"GET", "POST"})
     */
    public function attributon(string $processObj, int $id, Request $request,ManagerRegistry $doctrine, UserJuridiqueRepository $uJuridiqueeRepo, AttributionJuridiqueProcess $attributionJuridiqueSrv){
        //On vérifie l'objet process dans l'url
        $this->checkProcessObjStr($processObj);

        $classe = self::PROCESS_CLASSES[$processObj]['class'];
        $repo = $doctrine->getRepository($classe);
        //On récupére l'objet correspondant à partir de son ID
        /* @var ProcessObj $e */
        $e = $repo->findOneBy(['id' => $id]);
        if($e === null){
            throw new \Exception("L'objet demandé n'existe pas !");
        }elseif($e->getCurrentState() != 'demande_non_attribuee'){
            throw new \Exception("Action impossible");
        }

        //ID de l'user juridique envoyé par le client
        $idUJurdique = $request->request->get('uJuridique');
        //Si l'id n'est pas nul on peut attribuer à l'agent
        if($idUJurdique){
            $res = $attributionJuridiqueSrv->run(
                $e,
                $uJuridiqueeRepo->findOneBy([
                    'id' => $idUJurdique
                ])
            );
            if(!$res){
                throw new \Exception('Une erreur a eu lieu lors de l\'attribution.');
            }
            $this->addFlash(
                'success',
                'La demande a bien été attribuée à l\'agent'
            );
            return $this->redirectToRoute('apps_process_home', ['processObj' => $processObj]);
        }


        return $this->render('apps/gestion_process/attrib.html.twig', [
            //Obj pour initialisation du formulaire
            'obj' => $e,
            //Slug pour afficher le form non-modifiable
            'processObj' => $processObj,
            //Liste des users juridiques
            'uJuridique' => $uJuridiqueeRepo->findAll()
        ]);
    }

    /**
     * Page de traitement des process
     * @IsGranted("ROLE_JURIDIQUE")
     * @Route("/{processObj}/traitement/{id}", name="apps_process_traitement", methods={"GET", "POST"})
     * @throws \Exception
     */
    public function traitement(string $processObj, int $id, ManagerRegistry $doctrine, Request $request, TraitementJuridiqueProcess $traitementJuridiqueSrv){
        //On vérifie l'objet process dans l'url
        $this->checkProcessObjStr($processObj);

        $classe = self::PROCESS_CLASSES[$processObj]['class'];
        $repo = $doctrine->getRepository($classe);
        //On récupére l'objet correspondant à partir de son ID
        /* @var ProcessObj $e */
        $e = $repo->findOneBy(['id' => $id]);
        if($e === null){
            throw new \Exception("L'objet demandé n'existe pas !");
        }
        else if($e->getCurrentState() != 'demande_attribuee'){
            throw new \Exception("La demande ne peut pas être traitée.");
        }
        else{
            //On vérifie si la demande a bien été attrbuée à l'agent qui essaie de la traiter
            /* @var User $user */
            $user = $this->getUser();
            if($e->getUserAgentJuridique()->getUser()->getId() != $user->getId()){
                throw new \Exception("L'utilisateur n'est pas habilité à traiter la demande.");
            }
        }

        //On récupére le choix effecuté par l'agent juridique
        $choice = $request->request->get('choice');
        $raisons = $request->request->get('txt_raisons2');
        if($choice != null){
            if($e instanceof Avis){
                $e->setReponse($raisons);
            }

            $transition = $choice == 'yes' ? 'valider_demande' : 'refuser_demande';
            $traitementJuridiqueSrv->run($e, $transition);
            $this->addFlash(
                'success',
                'La demande d\'avis a bien été ' . ($choice == 'yes' ? 'validée' : 'refusée') . '.'
            );
            return $this->redirectToRoute('apps_process_home', ['processObj' => $processObj]);
        }

        return $this->render('apps/gestion_process/traitement.html.twig', [
            //Obj pour initialisation du formulaire
            'obj' => $e,
            //Slug pour afficher le form non-modifiable
            'processObj' => $processObj
        ]);
    }

    /**
     * @Route("/{processObj}/no-modif/{id}", name="apps_process_no_modif", methods={"GET"})
     */
    public function noModif(string $processObj, int $id, ManagerRegistry $doctrine)
    {
        //On vérifie l'objet process dans l'url
        $this->checkProcessObjStr($processObj);

        $classe = self::PROCESS_CLASSES[$processObj]['class'];
        $repo = $doctrine->getRepository($classe);
        //On récupére l'objet correspondant à partir de son ID
        /* @var ProcessObj $e */
        $e = $repo->findOneBy(['id' => $id]);

        return $this->render('apps/gestion_process/no_modif.html.twig', [
            //Obj pour initialisation du formulaire
            'obj' => $e,
            //Slug pour afficher le form non-modifiable
            'processObj' => $processObj
        ]);
    }

    /**
     * @Route("/{processStr}/modif/{slug}", name="apps_process_modif", methods={"GET", "POST"})
     */
    public function modif(ProcessObj $processObj, string $processStr, ManagerRegistry $doctrine, Request $request, EditProcessObj $editProcessObjSrv)
    {
        $classe = self::PROCESS_CLASSES[$processStr]['class'];
        $repo = $doctrine->getRepository($classe);
        //On récupére l'objet correspondant à partir de son ID
        /* @var $e ProcessObj */
        $e = $repo->findOneBy(['id' => $processObj->getId()]);
        $dep = $e->getDepartementInitiateur();

        $form = $this->createForm(self::PROCESS_CLASSES[$processStr]['new'], $processObj);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $editProcessObjSrv->run(
                $processObj, $e, [
                    'departement' => $dep,
                    'documents' => $form->get('documents')->getData(),
                    'upload_folder' => $this->getParameter(self::PROCESS_CLASSES[$processStr]['upload_folder'])
                ]
            );
            $this->addFlash(
                'success',
                'Modification effectuée'
            );
            return $this->redirectToRoute('apps_process_home', ['processObj' => $processStr]);
        }

        return $this->render('apps/gestion_process/modif.html.twig', [
            'form' => $form->createView(),
            'processObj' => $processStr
        ]);
    }

    /**
     * Page d'accueil des process mais avec options
     * @Route("/{processObj}/{perms}", name="apps_process_home_perms", methods={"GET"})
     */
    public function home_perms($processObj, $perms){
        $this->checkProcessObjStr($processObj);
        $_els = [
            'page_title' => $processObj . '_title'
        ];
        return $this->render('apps/gestion_process/index.html.twig', array_merge($_els, [
            'processObj' => $processObj,
            'perms' => $this->setPermsSession($this->session),
            'status' => $perms
        ]));
    }

    /**
     * @Route("/link/{process}/{slug}", name="apps_link_process", methods={"GET"})
     */
    public function linkProcess(ProcessObj $processObj, $process)
    {
        $perms = $this->setPermsSession($this->session);
        $link = 'no_modif';
        switch ($perms){
            case 'all_user':
                if($processObj->isFinished()){
                    if($this->isGranted('ROLE_JURIDIQUE') && !($processObj instanceof Avis)){
                        $link = 'no_modif';
                    }
                    break;
                }elseif($processObj->getCurrentState() !== 'en_attente_manager'){
                    $link = 'modif';
                }
                break;
            case 'all_manager':
                $link = ($processObj->getCurrentState() === 'en_attente_manager') ? 'validation_manager': $link;
                break;
            case 'all_user_juridique':
                $link = ($processObj->getCurrentState() === 'demande_attribuee') ? 'traitement': $link;
                break;
            case 'all':
                $link = ($processObj->getCurrentState() === 'demande_non_attribuee') ? 'attribution': $link;
                $link = ($processObj->getCurrentState() === 'demande_validee') ? 'modif': $link;
                break;
        }

        if($link == 'modif'){
            return $this->redirectToRoute('apps_process_'.$link, [
                'processStr' => $process,
                'slug' => $processObj->getSlug()
            ]);
        }

        return $this->redirectToRoute('apps_process_'.$link, [
            'processObj' => $process,
            'id' => $processObj->getId()
        ]);
    }

    /**
     * @Route("/dep_manager/{id}", name="apps_process_dep_manager")
     */
    public function dep_manager(ProcessObj $processObj, ValidationManagerProcess $validationManagerProcess){
        /* @var User $user */
        $user = $this->getUser();

        $processObj = $validationManagerProcess->run(
            $user, $processObj, true
        );

        var_dump($processObj);

        return new Response($processObj->getObjet());
    }

    private function setPermsSession(SessionInterface $session){
        if($session->get('perms') == null){
            $perms = 'all_user';
            $perms = $this->isGranted('ROLE_USER_MANAGER') ? 'all_manager' : $perms;
            $perms = $this->isGranted('ROLE_JURIDIQUE') ? 'all_user_juridique' : $perms;
            $perms = $this->isGranted('ROLE_USER_BOSS_JURIDIQUE') ? 'all' : $perms;

            $session->set('perms', $perms);
        }
        return $session->get('perms');
    }

    private function checkProcessObjStr(string $processObj){
        if(!in_array($processObj, array_keys(self::PROCESS_CLASSES))){
            throw new \Exception("La chaine de caractère fournie n'est pas valide. Elle ne correspond à aucune classe.");
        }
    }
}