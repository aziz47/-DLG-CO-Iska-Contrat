<?php

namespace App\Controller\Process;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/apps/process/_partials/forms")
 */
class PartialsFormController extends AbstractController
{
    private const REPOS = [
        'avis' => [
            'readOnly' => 0
        ]
    ];
}