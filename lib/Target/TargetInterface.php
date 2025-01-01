<?php

namespace Sholokhov\Exchange\Target;

use Bitrix\Main\Result;

interface TargetInterface
{
    public function has(mixed $item): bool;
    public function add(mixed $item): Result;
    public function update(mixed $item): Result;
}