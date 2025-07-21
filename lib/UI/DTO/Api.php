<?php

namespace Sholokhov\Exchange\UI\DTO;

use Sholokhov\Exchange\Repository\Types\Memory;

class Api implements ApiInterface
{
    /**
     * Хранилище данных поля
     *
     * @var Memory
     * @since 1.2.0
     * @version 1.2.0
     */
    private readonly Memory $repository;

    /**
     * Контроллер на который идет запрос
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getAction(): string
    {
        return $this->getRepository()->get('action');
    }

    /**
     * Указание контроллера на который идет запрос
     *
     * @param string $action
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setAction(string $action): static
    {
        $this->getRepository()->set('action', $action);
        return $this;
    }

    /**
     * Параметры запроса
     *
     * @return array
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getParameters(): array
    {
        return $this->getRepository()->get('parameters', []);
    }

    /**
     * Установка параметров запроса
     *
     * @param array $data
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setParameters(array $data): static
    {
        $this->getRepository()->set('parameters', $data);
        return $this;
    }

    /**
     * JS обработчик результата API ответа
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getCallback(): string
    {
        return $this->getRepository()->get('callback', '');
    }

    /**
     * Установка JS обработчика API ответа
     *
     * @param string $callback
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setCallback(string $callback): static
    {
        $this->getRepository()->set('callback', $callback);
        return $this;
    }

    /**
     * Получение хранилища данных поля
     *
     * @return Memory
     * @since 1.2.0
     * @version 1.2.0
     */
    final protected function getRepository(): Memory
    {
        return $this->repository ??= new Memory;
    }
}