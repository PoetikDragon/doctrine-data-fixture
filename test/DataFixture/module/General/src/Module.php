<?php

declare(strict_types=1);

namespace General;

use General\Listener\EventCatcher;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ApiTools\Provider\ApiToolsProviderInterface;

class Module implements ApiToolsProviderInterface, BootstrapListenerInterface
{
    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return require __DIR__ . '/../config/module.config.php';
    }

    /**
     * @inheritdoc
     */
    public function getAutoloaderConfig(): array
    {
        return [
            'Laminas\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function onBootstrap(EventInterface $event): void
    {
        $application        = $event->getApplication();
        $serviceManager     = $application->getServiceManager();
        $eventManager       = $application->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        $eventCatcher = $serviceManager->get(EventCatcher::class);
        $eventCatcher->attachShared($sharedEventManager);
    }
}
