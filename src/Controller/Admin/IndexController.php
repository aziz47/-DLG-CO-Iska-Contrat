<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/admin")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_index")
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_admin_acc_mgmt_management');
    }
}
