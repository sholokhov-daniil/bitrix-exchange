<?php

namespace Sholokhov\Exchange\Messages;

use Sholokhov\Exchange\Messages\Errors\ErrorInterface;

/**
 * Результат выполненных действий
 */
class Result implements ResultInterface
{
    /**
     * Результат действия
     *
     * @var mixed|null
     */
    protected mixed $data = null;

    /**
     * Ошибки при выполнении действия
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Работа завершилась успехом
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return count($this->errors) === 0;
    }

    /**
     * Установка данных результата работы
     *
     * @param mixed $value
     * @return ResultInterface
     */
    public function setData(mixed $value): ResultInterface
    {
        $this->data = $value;
        return $this;
    }

    /**
     * Получить данные результата работы
     *
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Добавить ошибку
     *
     * @param ErrorInterface $error
     * @return ResultInterface
     */
    public function addError(ErrorInterface $error): ResultInterface
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * Добавить ошибки
     *
     * @param array $errors
     * @return ResultInterface
     */
    public function addErrors(array $errors): ResultInterface
    {
        array_walk($errors, [$this, 'addError']);
        return $this;
    }

    /**
     * Установить ошибки
     *
     * @param array $errors
     * @return ResultInterface
     */
    public function setErrors(array $errors): ResultInterface
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Получение ошибок
     *
     * @return array|ErrorInterface[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Получить ошибку по коду
     *
     * @param string $code
     * @return ErrorInterface|null
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