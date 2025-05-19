<?php

namespace Sholokhov\BitrixExchange\Messages\Type;

use Sholokhov\BitrixExchange\Messages\ErrorInterface;
use Sholokhov\BitrixExchange\Messages\ResultInterface;

class Result implements ResultInterface
{
    /**
     * Ошибки при выполнении действия
     *
     * @var ErrorInterface[]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected array $errors = [];

    /**
     * Работа завершилась успехом
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isSuccess(): bool
    {
        return count($this->errors) === 0;
    }

    /**
     * Добавить ошибку
     *
     * @param ErrorInterface $error
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function addError(ErrorInterface $error): static
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * Добавить ошибки
     *
     * @param ErrorInterface[] $errors
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function addErrors(array $errors): static
    {
        array_walk($errors, [$this, 'addError']);
        return $this;
    }

    /**
     * Установить ошибки
     *
     * @param ErrorInterface[] $errors
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setErrors(array $errors): static
    {
        $this->errors = [];
        return $this->addErrors($errors);
    }

    /**
     * Получение ошибок
     *
     * @return ErrorInterface[]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Получение ошибочных сообщений
     *
     * @return array
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getErrorMessages(): array
    {
        return array_map(fn(ErrorInterface $error) => $error->getMessage(), $this->errors);
    }

    /**
     * Получить ошибку по коду
     *
     * @param string $code
     * @return ErrorInterface|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getErrorByCode(string $code): ?ErrorInterface
    {
        foreach ($this->errors as $error) {
            if ($error->getCode() === $code) {
                return $error;
            }
        }

        return null;
    }
}