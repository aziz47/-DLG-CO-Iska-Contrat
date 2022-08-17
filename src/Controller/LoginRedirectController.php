<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginRedirectController extends AbstractController
{
    /**
     * @Route("/login-redirect", name="app_login_redirect")
     */
    public function index(): Response
    {
        if($this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_admin_index');
        }
        return $this->redirectToRoute('apps_process_home', [
            'processObj' => 'contrat'
        ]);
    }
}
