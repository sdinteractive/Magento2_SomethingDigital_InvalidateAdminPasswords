<?php

namespace SomethingDigital\InvalidateAdminPasswords\Console;

use Magento\Framework\App\State;
use SomethingDigital\InvalidateAdminPasswords\Model\Invalidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InvalidateCommand extends Command
{
    private $invalidator;

    private $state;

    public function __construct(
        Invalidator $invalidator,
        State $state
    ) {
        parent::__construct(null);

        $this->invalidator = $invalidator;
        $this->state = $state;
    }

    protected function configure()
    {
        $this->setName('sd:invalidate-admin-passwords:invalidate');
        $this->setDescription('Invalidate admin passwords');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

        $result = $this->invalidator->invalidate();

        if ($result) {
            $output->writeln('Passwords invalidated');
        }
    }
}
