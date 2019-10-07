<?php

namespace SomethingDigital\InvalidateAdminPasswords\Console;

use SomethingDigital\InvalidateAdminPasswords\Model\Invalidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InvalidateCommand extends Command
{
    private $invalidator;

    public function __construct(Invalidator $invalidator)
    {
        parent::__construct(null);

        $this->invalidator = $invalidator;
    }

    protected function configure()
    {
        $this->setName('sd:invalidate-admin-passwords:invalidate');
        $this->setDescription('Invalidate admin passwords');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo $this->invalidator->invalidate();
    }
}
