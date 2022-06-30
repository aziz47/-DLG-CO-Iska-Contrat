<?php

namespace App\Command;

use App\Entity\User;
use App\Service\Mail\DefaultMailService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Ldap\Ldap;

class LdapCommand extends Command
{
    protected static $defaultName = 'tests:ldap';
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
        $ldap = Ldap::create('ext_ldap',['host'=>'AD-Serv']);
	$ldap->bind("CN=Pédjoulalé OUATTARA,OU=Staff DSI,OU=Directions,OU=_MoovCI,OU=MOOV-CI,DC=etisalat-africa,DC=net", "Nana.Lyam@2026");	
	$query = $ldap->query('dc=etisalat-africa,dc=net', '(&(objectClass=user)(objectCategory=person)(!(userAccountControl:1.2.840.11355.6.1.4.803:=2)))');
	$results = $query->execute()->toArray();
	print_r($results);

	return Command::SUCCESS;

	}

}
