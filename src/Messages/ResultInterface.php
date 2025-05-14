<?php

namespace Sholokhov\BitrixExchange\Messages;

use Sholokhov\BitrixExchange\Messages\Type\Error;

/**
 * @since 1.0.0
 * @version 1.0.0
 */
interface ResultInterface
{
    /**
     * Успешный результат (отсутствие ошибок)
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isSuccess(): bool;

    /**
     * Указание результата работы
     *
     * @param mixed $value
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setData(mixed $value): self;

    /**
     * Получение результата работы
     *
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getData(): mixed;

    /**
     * Добавление ошибки
     *
     * @param Error $error
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function addError(Error $error): self;

    /**
     * Добавление ошибок
     *
     * @param array $errors
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function addErrors(array $errors): self;

    /**
     * Указание нового списка ошибок (старые будут удалены)
     *
     * @param Error[] $errors
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setErrors(array $errors): self;

    /**
     * Получение всех ошибок
     *
     * @return Error[]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getErrors(): array;

    /**
     * Получение ошибочных сообщений
     *
     * @return string[]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getErrorMessages(): array;

    /**
     * Получение ошибки по коду
     *
     * @param string $code
     * @return Error|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getErrorByCode(string $code): ?Error;
}