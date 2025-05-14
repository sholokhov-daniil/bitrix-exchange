<?php

namespace Sholokhov\BitrixExchange\Messages\Type;

use Sholokhov\BitrixExchange\Messages\ResultInterface;

/**
 * Результат выполненных действий
 *
 * @deprecated Будет переделываться на {@see \Bitrix\Main\Result}
 * @since 1.0.0
 * @version 1.0.0
 */
class DataResult implements ResultInterface
{
    /**
     * Результат действия
     *
     * @var mixed|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected mixed $data = null;

    /**
     * Ошибки при выполнении действия
     *
     * @var Error[]
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
     * Установка данных результата работы
     *
     * @param mixed $value
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setData(mixed $value): static
    {
        $this->data = $value;
        return $this;
    }

    /**
     * Получить данные результата работы
     *
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Добавить ошибку
     *
     * @param Error $error
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function addError(Error $error): static
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * Добавить ошибки
     *
     * @param array $errors
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
     * @param array $errors
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setErrors(array $errors): static
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Получение ошибок
     *
     * @return Error[]
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
        return array_map(fn(Error $error) => $error->getMessage(), $this->errors);
    }

    /**
     * Получить ошибку по коду
     *
     * @param string $code
     * @return Error|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getErrorByCode(string $code): ?Error
    {
        foreach ($this->errors as $error) {
            if ($error->getCode() === $code) {
                return $error;
            }
        }

        return null;
    }
}