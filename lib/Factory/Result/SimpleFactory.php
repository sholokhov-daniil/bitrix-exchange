<?php

namespace Sholokhov\BitrixExchange\Factory\Result;

use Sholokhov\BitrixExchange\Repository\Result\SimpleResultRepository;

class SimpleFactory
{
    public function __invoke()
    {
        return new SimpleResultRepository;
    }
}