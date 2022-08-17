<?php

namespace App\Controller\Admin;

use App\Repository\BaseDoc\ModeleContratRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/admin/base_doc")
 */
class BaseDocController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_base_doc")
     */
    public function index(ModeleContratRepository $repository): Response
    {
        return $this->render('admin/base_doc.html.twig', [
            'docs' => $repository->findAll()
        ]);
    }
}
