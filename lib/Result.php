<?php

namespace Sholokhov\Exchange;

use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;

class Result implements ResultInterface
{
    protected readonly ErrorCollection $errorCollection;
    protected mixed $data = null;

    public function __construct()
    {
        $this->errorCollection = new ErrorCollection;
    }

    /**
     * Успешный результат (отсутствие ошибок)
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->errorCollection->isEmpty();
    }

    /**
     * Указание результата работы
     *
     * @param mixed $value
     * @return $this
     */
    public function setData(mixed $value): self
    {
        $this->data = $value;
        return $this;
    }

    /**
     * Получение результата работы
     *
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Добавление ошибки
     *
     * @param Error $error
     * @return $this
     */
    public function addError(Error $error): self
    {
        $this->errorCollection->setError($error);
        return $this;
    }

    /**
     * Добавление ошибок
     *
     * @param array $errors
     * @return $this
     */
    public function addErrors(array $errors): self
    {
        $this->errorCollection->add($errors);
        return $this;
    }

    /**
     * Указание нового списка ошибок (старые будут удалены)
     *
     * @param Error[] $errors
     * @return $this
     */
    public function setErrors(array $errors): self
    {
        $this->errorCollection->setValues($errors);
        return $this;
    }

    /**
     * Получение всех ошибок
     *
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errorCollection->getValues();
    }

    /**
     * Получение ошибки по коду
     *
     * @param string $code
     * @return Error|null
     */
    public function getErrorByCode(string $code): ?Error
    {
        return $this->errorCollection->getErrorByCode($code);
    }
}