<?php

namespace SomethingDigital\InvalidateAdminPasswords\Console;

use Magento\Framework\App\State;
use SomethingDigital\InvalidateAdminPasswords\Model\Invalidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'This will invalidate all admin passwords. Are you sure you want to do this?[y/N]',
            false
        );
        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

        $result = $this->invalidator->invalidate();

        if ($result) {
            $output->writeln('Passwords invalidated');
        }
    }
}
