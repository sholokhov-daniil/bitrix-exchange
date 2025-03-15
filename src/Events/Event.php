<?php

declare(strict_types=1);

namespace Sholokhov\Exchange\Events;

use Sholokhov\Exchange\Repository\RepositoryInterface;
use Sholokhov\Exchange\Repository\Types\Memory;

class Event
{
    private RepositoryInterface $container;

    /**
     * Вызов обработчиков перед запуском обмена
     *
     * @param array $parameters
     * @return void
     */
    public function invokeBeforeRun(array $parameters = []): void
    {
        $this->invoke('before_run', $parameters);
    }

    /**
     * Добавить вызов обработчика в начале обмена
     *
     * @param callable $callback
     * @return static
     */
    public function subscribeBeforeRun(callable $callback): self
    {
        return $this->subscribe('before_run', $callback);
    }

    /**
     * Вызов обработчиков после обмена
     *
     * @param array $parameters
     * @return void
     */
    public function invokeAfterRun(array $parameters = []): void
    {
        $this->invoke('after_run', $parameters);
    }

    /**
     * Добавить вызов обработчика по окончанию обмена
     *
     * @param callable $callback
     * @return static
     */
    public function subscribeAfterRun(callable $callback): self
    {
        return $this->subscribe('after_run', $callback);
    }

    /**
     * Вызов событий после добавления элемента
     *
     * @param array $parameters
     * @return void
     */
    public function invokeAfterAdd(array $parameters = []): void
    {
        $this->invoke('after_add', $parameters);
    }

    /**
     * Добавить вызов обработчика после добавления
     *
     * @param callable $callback
     * @return static
     */
    public function subscribeAfterAdd(callable $callback): self
    {
        return $this->subscribe('after_add', $callback);
    }

    /**
     * Событие после обновления элемента
     *
     * @param array $parameters
     * @return void
     */
    public function invokeAfterUpdate(array $parameters = []): void
    {
        $this->invoke('after_update', $parameters);
    }

    /**
     * Добавить вызов обработчика после обновления
     *
     * @param callable $callback
     * @return static
     */
    public function subscribeAfterUpdate(callable $callback): self
    {
        return $this->subscribe('after_update', $callback);
    }

    /**
     * Вызов обработчиков перед обменом элемента
     *
     * @param array $parameters
     * @return void
     */
    public function invokeBeforeActionItem(array $parameters = []): void
    {
        $this->invoke('before_action_item', $parameters);
    }

    /**
     * Подписка перед обменом элемента
     *
     * @param callable $callback
     * @return void
     */
    public function subscribeBeforeActionItem(callable $callback): void
    {
        $this->subscribe('before_action_item', $callback);
    }

    /**
     * Вызов обработчиков перед обменом элемента
     *
     * @param array $parameters
     * @return void
     */
    public function invokeAfterActionItem(array $parameters = []): void
    {
        $this->invoke('after_action_item', $parameters);
    }

    /**
     * Подписка перед обменом элемента
     *
     * @param callable $callback
     * @return void
     */
    public function subscribeAfterActionItem(callable $callback): void
    {
        $this->subscribe('after_action_item', $callback);
    }

    /**
     * Добавление обработчика
     *
     * @param string $id
     * @param callable $callback
     * @return $this
     */
    public function subscribe(string $id, callable $callback): self
    {
        $container = $this->getContainer();

        $handlers = $container->get($id, []);
        $handlers[] = $callback;
        $container->set('before_run', $handlers);

        return $this;
    }

    /**
     * Вызов обработчиков
     *
     * @param string $id
     * @param array $parameters
     * @return void
     */
    public function invoke(string $id, array $parameters = []): void
    {
        $callbacks = $this->getContainer()->get($id, []);
        array_walk($callbacks, fn(callable $callback) => call_user_func_array($callback, $parameters));
    }

    /**
     * Получение хранилища данных
     *
     * @return RepositoryInterface
     */
    protected function getContainer(): RepositoryInterface
    {
        return $this->container ??= new Memory;
    }
}