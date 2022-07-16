<?php

namespace App\MessageHandler;

use App\Message\CreatePdf;
use Carbon\Carbon;
use Knp\Snappy\Pdf;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CreatePdfHandler implements MessageHandlerInterface
{
    /**
     * @var Pdf
     */
    private $knpSnappyPdf;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(MailerInterface $mailer, ContainerInterface $container, Environment $twig, UrlGeneratorInterface $router, Pdf $knpSnappyPdf, Filesystem $filesystem, LoggerInterface $logger)
    {
        $this->knpSnappyPdf = $knpSnappyPdf;
        $this->router = $router;
        $this->twig = $twig;
        $this->container = $container;
        $this->mailer = $mailer;
        $this->filesystem = $filesystem;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
e\PhpSpreadsheet\     */
    public function __invoke(CreatePdf $pdf): string
    {
        $fileName = $pdf->getName() . '/' . (Carbon::now())->format('d_m_Y_H_i_v') . '.pdf';
        $fileLocation = $this->container->getParameter('generated_pdf_dir') . $fileName;
        //Vérification de l'existence du fichier
        if(!$this->filesystem->exists($fileLocation))
        {
            //Si le fichier n'existe pas, on le génère
            $this->knpSnappyPdf->generateFromHtml(
                $pdf->getHtml(),
                $fileLocation,
                array('orientation' => 'Landscape')
            );
        }

        //Petit temps d'attente pour la génération du fichier
        sleep(15);

        //Envoi du mail avec le rapport
        $email = (new TemplatedEmail())
            ->attachFromPath($fileLocation, 'Votre rapport')

            ->from('iska-contrat@dlgco.ci')
            ->to(new Address(
                $pdf->getUser()->getEmail()
            ))
            ->subject("Votre reporting")

            ->htmlTemplate('apps/mails/base.html.twig')

            ->context([
                'title' => "Reporting généré",
                'text' => "Bonjour, vous pouvez trouver ci-joint votre rapport généré."
            ]);

        $this->mailer->send($email);

        return $fileLocation;
    }
}