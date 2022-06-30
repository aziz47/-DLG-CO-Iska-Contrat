<?php

namespace App\MessageHandler;

use App\Message\CreateExcel;
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

class CreateExcelHandler implements MessageHandlerInterface
{

    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, ContainerInterface $container, Environment $twig, UrlGeneratorInterface $router, Filesystem $filesystem)
    {
        $this->mailer = $mailer;
        $this->container = $container;
        $this->twig = $twig;
        $this->router = $router;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function __invoke(CreateExcel $createExcel): string
    {
        $fileName = $createExcel->getName() . '/' . (Carbon::now())->format('d_m_Y_H_i_v') . '.xls';
        $fileLocation = $this->container->getParameter('generated_pdf_dir') . $fileName;
        //Vérification de l'existence du fichier
        if(!$this->filesystem->exists($fileLocation))
        {
            //Si le fichier n'existe pas, on le génère
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $spreadsheet = $reader->loadFromString((string) $createExcel->getHtml());
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save($fileLocation);
        }

        //Envoi du mail avec le rapport
        $email = (new TemplatedEmail())
            ->attachFromPath($fileLocation, '')

            ->from('iska-contrat@dlgco.ci')
            ->to(new Address(
                $createExcel->getUser()->getEmail()
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