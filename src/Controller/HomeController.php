<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/apps", name="apps_home")
     */
    public function index(): Response
    {
        if($this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_admin_management');
        }
        return $this->redirectToRoute('apps_process_home', [
            'processObj' => 'contrat'
        ]);
    }
}
