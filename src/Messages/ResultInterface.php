<?php

namespace Sholokhov\BitrixExchange\Messages;

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
     * Добавление ошибки
     *
     * @param ErrorInterface $error
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function addError(ErrorInterface $error): self;

    /**
     * Добавление ошибок
     *
     * @param ErrorInterface[] $errors
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function addErrors(array $errors): self;

    /**
     * Указание нового списка ошибок (старые будут удалены)
     *
     * @param ErrorInterface[] $errors
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setErrors(array $errors): self;

    /**
     * Получение всех ошибок
     *
     * @return ErrorInterface[]
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
     * @return ErrorInterface|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getErrorByCode(string $code): ?ErrorInterface;
}