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
        return $this->redirectToRoute('apps_process_home', [
            'processObj' => 'contrat'
        ]);
    }
}
