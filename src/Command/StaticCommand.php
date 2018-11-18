<?php

namespace PiedWeb\StaticBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GreetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('static:generate')
            ->setDescription('Generate statif folder for PiedWeb CMS')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $static->dump();
        $output->writeln('statif folder generation succeeded');
    }
}
