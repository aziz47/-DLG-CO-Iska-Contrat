<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/apps/mail")
 */
class MailController extends AbstractController
{
    /**
     * @Route("/", name="app_mail")
     */
    public function index(): Response
    {
        return $this->render('apps/mails/base.html.twig', [
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MailController.php',
        ]);
    }
}
