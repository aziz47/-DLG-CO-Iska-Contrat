<?php

namespace App\Command\Stats;

use App\Service\Stats\UJuridiquePerfAvisConseils;
use App\Service\Stats\UJuridiquePerfContrat;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UJuridiquePerfAvisConseilsCommand extends Command
{
    protected static $defaultName = 'stats:perf:avis';
    /**
     * @var UJuridiquePerfAvisConseils
     */
    private $juridiquePerfAvisConseilsSrv;

    public function __construct(UJuridiquePerfAvisConseils $juridiquePerfAvisConseilsSrv)
    {
        parent::__construct();
        $this->juridiquePerfAvisConseilsSrv = $juridiquePerfAvisConseilsSrv;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /*try{*/
            $output->writeln("[STATS COMMAND] COMPUTING USER JURIDIQUE STATS AVIS" . PHP_EOL);
            ($this->juridiquePerfAvisConseilsSrv)();
            $output->writeln(PHP_EOL . "[STATS COMMAND] COMPUTING USER JURIDIQUE STATS DONE");
            return Command::SUCCESS;
        /*}catch(\Exception $e){
            $output->writeln($e->getTraceAsString());
            $output->writeln("[STATS COMMAND] COMPUTING USER JURIDIQUE ERROR !");
        }
        return Command::INVALID;*/
    }
}