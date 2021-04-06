<?php

declare(strict_types=1);

namespace ApiSkeletons\Doctrine\DataFixture\Commands;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiSkeletons\Doctrine\DataFixture\Loader;

class ImportCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $groupName = sprintf('\<%s\>', self::ARGUMENT_GROUP);
        $this->setName('data-fixture:import')
             ->setDescription('Import the data fixtures for the provided group name')
             ->addArgument(
                 self::ARGUMENT_GROUP,
                 InputOption::VALUE_REQUIRED,
                 'The data-fixture group to import.'
             )
             ->addOption(
                 'purge-with-truncate',
                 null,
                 InputOption::VALUE_NONE,
                 'If specified, will purge the object manager\'s tables using truncate before running fixtures.'
             )
             ->addOption(
                 'do-not-append',
                 null,
                 InputOption::VALUE_NONE,
                 'Specifying this option ensures that the object manager\'s tables will be emptied. The default option '
                 . 'is to append values to the tables. NOTE: If you are re-running fixtures, this option is not '
                 . 'necessary.'
             )
             ->addUsage('%command.name% \<group_name\> [--purge-with-truncate] [--do-not-append]')
             ->setHelp(<<<EOT
The <info>%command.name% {$groupName}</info> command imports the data fixtures
specified by <info>{$groupName}</info> found in the config key:
<info>doctrine.fixture.{$groupName}</info>.

The <info>{$groupName}</info> is a required argument.

The default was to not append and this was consistently overriden with
<info>--append</info>. Append is now the default option and is inversed with the
new <info>--do-not-append</info> option.

Options:

<info>%command.name% --purge-with-truncate</info>
If specified, will purge the object manager's tables before running fixtures.

<info>%command.name% --do-not-append</info>
Specifying this option ensures that the object manager's tables will be emptied.
NOTE: If you are re-running fixtures, this option is not necessary.
EOT
             );
    }

    /**
     * @inheritdoc
     */
    protected function executeCommand(InputInterface $input, OutputInterface $output): void
    {
        $groupName = (string)$input->getArgument(self::ARGUMENT_GROUP);
        if (! $groupName) {
            $interface = new SymfonyStyle($input, $output);
            $interface->error('No fixture group given.');
            return;
        }

        $manager      = $this->getDataFixtureManager($groupName);
        $loader       = new Loader($manager);
        $purgerClass  = (isset($manager->getOptions()['purger'])) ? $manager->getOptions()['purger']: ORMPurger::class;
        $purger = new $purgerClass();

        foreach ($manager->getAll() as $fixture) {
            $loader->addFixture($fixture);
        }

        if ($input->getOption('purge-with-truncate')) {
            $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        }

        $objectManager = $manager->getObjectManager();
        if (! $objectManager instanceof ObjectManager) {
            throw new \RuntimeException(sprintf(
                'Invalid object manager given, %s must implement %s.',
                get_class($objectManager),
                ObjectManager::class
            ));
        }

        $executorClass  = (isset($manager->getOptions()['executor'])) ?
            $manager->getOptions()['executor']:
            ORMExecutor::class;
        $executor = new $executorClass($objectManager, $purger);

        $executor->execute($loader->getFixtures(), ! $input->getOption('do-not-append'));
        $interface = new SymfonyStyle($input, $output);
        $interface->success('Fixtures loaded!');
    }
}
