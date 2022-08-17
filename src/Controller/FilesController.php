<?php

namespace App\Controller;

use App\Entity\Abstracts\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FilesController extends AbstractController
{
    /**
     * @Route("/file/{slug}", name="files_get", methods={"GET"})
     */
    public function getFile(Document $document){
        return $this->file('../../public/uploads'.$document->getPath());
    }


}