<?php

namespace SomethingDigital\InvalidateAdminPasswords\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InvalidateCommand extends Command
{
    protected function configure()
    {
        $this->setName('sd:invalidate-admin-passwords:invalidate');
        $this->setDescription('Invalidate admin passwords');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'HELLO' . PHP_EOL;
    }
}
