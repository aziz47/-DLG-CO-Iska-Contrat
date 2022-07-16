<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Entity\Stats\UserJuridiqueStats;
use App\Entity\User;
use App\Entity\UserJuridique;
use App\Entity\UserJuridiqueData;
use App\Repository\DepartementRepository;
use App\Repository\UserRepository;
use Carbon\CarbonInterval;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_management")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/list", name="app_admin_list")
     */
    public function list(Request $request,UserRepository $repository)
    {
        $json = json_decode(
            $request->getContent()
        );
        if(isset($json->departement)){
            $users = $repository->findBy([
                'departement' => $json->departement
            ]);
        }else{
            $users = $repository->findAll();
        }

        $data = [];
        foreach ($users as $user){
            $data[] = [
                'id' => $user->getId(),
                'fname' => $user->getFirstName(),
                'lname' => $user->getLastName(),
                'email' => $user->getEmail(),
                'dep' => $user->getDepartement()->getLib(),
                'roles' => $user->getRoles()[0]
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/list/departement", name="app_admin_list_departement")
     */
    public function list_departement(DepartementRepository $repository): JsonResponse
    {
        $deps = $repository->findAll();
        $data = [];
        foreach ($deps as $d){
            $data[] = [
                'id' => $d->getId(),
                'lib' => $d->getLib()
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * @Route("/update_user", name="app_admin_update_user")
     */
    public function update_user(EntityManagerInterface $manager, Request $request, UserRepository $userRepository, DepartementRepository $repository): JsonResponse
    {
        $json = json_decode(
            $request->getContent()
        );

        $user = $userRepository->find($json->id);
        $dep = $repository->findOneBy([
            'lib' => $json->dep
        ]);
        $user
            ->setFirstName($json->fname)
            ->setLastName($json->lname)
            ->setRoles(
                [$json->roles]
            )
            ->setEmail($json->email)
            ->setDepartement($dep);

        $manager->persist($user);
        $manager->flush();

        return new JsonResponse(['mail' => $user->getEmail()]);
    }

    /**
     * @Route("/create_user", name="app_admin_create_user")
     */
    public function create_user(EntityManagerInterface $manager, Request $request, UserRepository $userRepository, DepartementRepository $repository, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $city = ["Abidjan", "Paris", "Anyama", "Abobo", "Cocody", "Marcory", "Bingerville", "Bouake"];
        $json = json_decode(
            $request->getContent()
        );

        $dep = $repository->findOneBy([
            'lib' => $json->dep
        ]);

        $user = (new User())
            ->setFirstName($json->fname)
            ->setLastName($json->lname)
            ->setRoles(
                [$json->roles]
            )
            ->setEmail($json->email)
            ->setDepartement($dep);
        $pass = $city[array_rand($city)] . '' . mt_rand(1000, 9999);
        $user->setPassword(
          $hasher->hashPassword(
              $user, $pass
          )
        );

        $manager->persist($user);

        if($json->roles === "ROLE_JURIDIQUE"){
            $ujD = (new UserJuridique())
                ->setUser($user);
            $manager->persist($ujD);

            $ujData = (new UserJuridiqueData())
                ->setUserJuridique($ujD)
                ->setNbrJourImpartiContrat(
                    CarbonInterval::days(5)
                );
            $manager->persist($ujData);

            $ujS = (new UserJuridiqueStats())
                ->setUJuridique($ujD)
                ->setPerfContrat(0)
                ->setPerfAvisConseils(0);
            $manager->persist($ujS);
        }

        $manager->flush();

        return new JsonResponse(['pass' => $pass]);
    }
}
