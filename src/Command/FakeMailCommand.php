<?php

namespace App\Command;

use App\Entity\User;
use App\Service\Mail\DefaultMailService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FakeMailCommand extends Command
{
    protected static $defaultName = 'tests:mail';
    /**
     * @var DefaultMailService
     */
    private $defaultMailService;

    public function __construct(string $name = null, DefaultMailService $defaultMailService)
    {
        parent::__construct($name);
        $this->defaultMailService = $defaultMailService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ($this->defaultMailService)(
            (new User())->setEmail("azizkamadou17@gmail.com"),
            'Hello',
            'Hello'
        );
        return Command::SUCCESS;
        // return Command::FAILURE;
        // return Command::INVALID
    }
}