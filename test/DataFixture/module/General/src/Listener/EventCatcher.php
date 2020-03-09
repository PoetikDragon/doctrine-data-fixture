<?php

namespace General\Listener;

use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;

/**
 * Class EventCatcher
 *
 * @package DbMongo\Listener
 */
class EventCatcher
{
    const EVENT_IDENTIFIER = 'Laminas\ApiTools\Doctrine\DoctrineResource';

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $caughtEvents = [];

    /**
     * Attach shared event manager interface
     *
     * @param SharedEventManagerInterface $events
     *
     * @return void
     */
    public function attachShared(SharedEventManagerInterface $events): void
    {
        $this->listeners[] = $events->attach(
            self::EVENT_IDENTIFIER,
            '*',
            [$this, 'listen']
        );
    }

    /**
     * Detach shared event manager interface
     *
     * @param SharedEventManagerInterface $events
     *
     * @return void
     */
    public function detachShared(SharedEventManagerInterface $events): void
    {
        foreach ($this->listeners as $listener) {
            $events->detach(self::EVENT_IDENTIFIER, $listener);
        }
        $this->listeners = [];
    }

    /**
     * Listen to an event
     *
     * @param Event $event
     *
     * @return void
     */
    public function listen(Event $event): void
    {
        $this->caughtEvents[$event->getName()] = $event->getName();
    }

    /**
     * Get the caught events
     *
     * @return array
     */
    public function getCaughtEvents(): array
    {
        return $this->caughtEvents;
    }
}
