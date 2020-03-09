<?php

declare(strict_types=1);

namespace Laminas\Doctrine\DataFixture\Commands;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListCommand extends AbstractCommand
{

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $groupName = sprintf('[\<%s\>]', self::ARGUMENT_GROUP);
        $this->setName('data-fixture:list')
             ->setDescription('List all fixture groups, or if specified, list all fixtures for a given group.')
             ->addArgument(
                 self::ARGUMENT_GROUP,
                 InputOption::VALUE_REQUIRED,
                 'The data-fixture group to import.'
             )
             ->addUsage('%command.name% [\<group_name\>]')
             ->setHelp(<<<EOT
The <info>%command.name% {$groupName}</info> command lists the data fixtures
specified by <info>{$groupName}</info> if given, otherwise lists all the
available fixture groups if left blank.
EOT
             );
    }

    /**
     * {@inheritdoc}
     */
    protected function executeCommand(InputInterface $input, OutputInterface $output): void
    {
        $interface = new SymfonyStyle($input, $output);
        if (! $input->hasArgument(self::ARGUMENT_GROUP) || ! $input->getArgument(self::ARGUMENT_GROUP)) {
            $this->listAllGroups($interface);
            return;
        }

        $this->listGroup($input, $interface);
    }

    /**
     * List all groups
     *
     * @param SymfonyStyle $interface
     *
     * @return void
     */
    protected function listAllGroups(SymfonyStyle $interface): void
    {
        $interface->title('<fg=red>All Fixture Groups</>');
        $interface->listing(array_map(function (string $group) {
            return sprintf(
                '<fg=cyan>%s</>',
                $group
            );
        }, array_keys((array)$this->container->get('config')['doctrine']['fixture'])));
    }

    /**
     * List the specified group
     *
     * @param InputInterface $input
     * @param SymfonyStyle   $interface
     *
     * @return void
     */
    protected function listGroup(InputInterface $input, SymfonyStyle $interface): void
    {
        $group = (array)$input->getArgument(self::ARGUMENT_GROUP);
        if (count($group) !== 1) {
            throw new \RuntimeException('Only one data fixture group can be provided.');
        }

        $manager = $this->getDataFixtureManager($group[0]);
        $interface->title(sprintf(
            '<comment>Group:</comment> <info>%s</info>',
            $input->getArgument(self::ARGUMENT_GROUP)
        ));

        $interface->text(sprintf(
            '<comment>Object Manager:</comment> <info>%s</info>',
            $manager->getObjectManagerAlias()
        ));

        $fixtures = array_map(function (FixtureInterface $fixture) {
            return sprintf(
                '<fg=cyan>%s</>',
                get_class($fixture)
            );
        }, $manager->getAll());
        $interface->listing($fixtures);
    }
}
