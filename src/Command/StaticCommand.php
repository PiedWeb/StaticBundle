<?php

namespace PiedWeb\StaticBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PiedWeb\StaticBundle\Service\StaticService;

class StaticCommand extends Command
{
    private $static;

    public function __construct(StaticService $static)
    {
        parent::__construct();
        $this->static = $static;
    }

    protected function configure()
    {
        $this
            ->setName('static:generate')
            ->setDescription('Generate statif folder for PiedWeb CMS')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->static->dump();
        $output->writeln('statif folder generation succeeded');
    }
}
