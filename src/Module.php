<?php

declare(strict_types=1);

namespace ApiSkeletons\Doctrine\DataFixture;

use Symfony\Component\Console\Application;
use Laminas\EventManager\EventInterface;
use Laminas\Loader\StandardAutoloader;
use Laminas\ModuleManager\Feature\AutoloaderProviderInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\InitProviderInterface;
use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\ServiceManager\ServiceManager;
use ApiSkeletons\Doctrine\DataFixture\Commands\ImportCommand;
use ApiSkeletons\Doctrine\DataFixture\Commands\ListCommand;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    InitProviderInterface
{

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return ['service_manager' => (new ConfigProvider)->getDependencies(),];
    }

    /**
     * @inheritdoc
     */
    public function getAutoloaderConfig(): array
    {
        return [
            StandardAutoloader::class => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init(ModuleManagerInterface $manager): void
    {
        $eventManager = $manager->getEventManager();
        if (! $eventManager) {
            throw new \RuntimeException('Unable to retrieve event manager.');
        }

        $sharedEventManager = $eventManager->getSharedManager();
        if (! $sharedEventManager) {
            throw new \RuntimeException('Unable to retrieve shared event manager.');
        }

        $sharedEventManager->attach(
            'doctrine',
            'loadCli.post',
            function (EventInterface $event) {
                $application = $event->getTarget();
                if (! $application instanceof Application) {
                    throw new \RuntimeException('Unable to retrieve application.');
                }

                $container = $event->getParam('ServiceManager');
                if (! $container instanceof ServiceManager) {
                    throw new \RuntimeException('Unable to retrieve service manager.');
                }

                $application->addCommands([
                    $container->get(ImportCommand::class),
                    $container->get(ListCommand::class),
                ]);
            }
        );
    }
}
