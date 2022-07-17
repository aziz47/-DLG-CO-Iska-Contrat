<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(): Response
    {
        if($this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_admin_index');
        }
        return $this->redirectToRoute('apps_home');
    }
}
