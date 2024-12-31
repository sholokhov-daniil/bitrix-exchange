<?php

namespace Sholokhov\Exchange;

use Bitrix\Main\Error;
use Iterator;

class Exchange extends AbstractApplication
{
    private ?Iterator $source = null;

    public function getSiteID(): string
    {
        // TODO: Implement getSiteID() method.
    }

    /**
     * Указание источника данных участвующих в обмене данных
     *
     * @param Iterator $source
     * @return $this
     */
    public function setSource(Iterator $source): self
    {
        $this->source = $source;
        return $this;
    }

    final protected function logic(): void
    {
        if (!$this->source) {
            $this->getResult()->addError(new Error('Source is not set', 404));
            return;
        }

            foreach ($this->source as $item) {
                $this->import($item);
            }

        $this->deactivate();
    }

    private function import(array $item): void
    {
        // Событие перед импортом
    }

    private function deactivate(): void
    {
        // Деактивация значений. Добавить флаг (если источника нет, или он пустой, то не деактивировать)
    }
}