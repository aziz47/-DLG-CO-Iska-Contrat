<?php

namespace App\Command\Stats;

use App\Service\Stats\UJuridiquePerfContrat;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UJuridiquePerfContratCommand extends Command
{
    protected static $defaultName = 'stats:perf:contrat';
    /**
     * @var UJuridiquePerfContrat
     */
    private $juridiquePerfContratSrv;

    public function __construct(UJuridiquePerfContrat $juridiquePerfContratSrv)
    {
        parent::__construct();
        $this->juridiquePerfContratSrv = $juridiquePerfContratSrv;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //try{
            $output->writeln("[STATS COMMAND] COMPUTING USER JURIDIQUE STATS CONTRATS" . PHP_EOL);
            ($this->juridiquePerfContratSrv)();
            $output->writeln(PHP_EOL . "[STATS COMMAND] COMPUTING USER JURIDIQUE STATS DONE");
            return Command::SUCCESS;
        /*}catch(\Exception $e){
            $output->writeln("[STATS COMMAND] COMPUTING USER JURIDIQUE ERROR !");
        }
        return Command::INVALID;*/
    }
}