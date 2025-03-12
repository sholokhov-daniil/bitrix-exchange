<?php

namespace Sholokhov\Exchange\Event;

use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\SystemException;
use Bitrix\Main\EventManager as BXEventManager;

/**
 * Менеджер событий
 */
class EventManager
{
    /**
     * Модуль, которому принадлежат события
     *
     * @var string
     */
    private string $module = 'sholokhov.exchange';

    private static self $instance;

    /**
     * Зарегистрированные события.
     *
     * @var array[]
     */
    protected array $events = [];

    private function __construct()
    {
    }

    /**
     * Получение менеджера события.
     *
     * @return self
     * @author Daniil S. GlobalArts
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self;
    }

    /**
     * Вызов события
     *
     * @param string $id
     * @param array $parameters
     * @return EventResult
     * @throws ArgumentTypeException
     * @throws SystemException
     */
    public function call(string $id, array &$parameters = []): EventResult
    {
        $eventResult = new EventResult(EventResult::SUCCESS, $parameters, $this->module);

        $data = $this->get($id);
        $data['event']->setParameters($parameters);
        $data['event']->send();

        if (is_callable($data['callback'])) {
            foreach ($data['event']->getResults() as $result) {
                $eventResult = call_user_func($data['callback'], $result);
            }
        }

        return $eventResult;
    }

    /**
     * Подписка на событие
     *
     * @param string $id
     * @param callable $callback
     * @param int $sort
     * @return void
     */
    public function subscribe(string $id, callable $callback, int $sort = 500): void
    {
        BXEventManager::getInstance()->addEventHandler($this->module, $id, $callback, false, $sort);
    }

    /**
     * Регистрация события
     *
     * @param string $id
     * @param callable|null $callback
     * @return $this
     */
    public function registration(string $id, callable $callback = null): self
    {
        $event = new Event($this->module, $id);
        $this->events[$id] = [
            'event' => $event,
            'callback' => $callback
        ];

        return $this;
    }

    /**
     * Получения события
     *
     * @param string $id
     * @return array{event: Event, callback: callable}
     * @throws SystemException
     */
    private function get(string $id): array
    {
        $this->check($id);
        return $this->events[$id];
    }

    /**
     * Проверка наличия события
     *
     * @param string $id
     * @return bool
     */
    private function has(string $id): bool
    {
        return array_key_exists($id, $this->events);
    }

    /**
     * Проверка наличия события
     *
     * @param string $id
     * @return void
     * @throws SystemException
     */
    private function check(string $id): void
    {
        if (!$this->has($id)) {
            throw new SystemException('Event not registered');
        }
    }
}