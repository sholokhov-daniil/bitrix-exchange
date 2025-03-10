<?php

namespace Sholokhov\Exchange\Target;

use Iterator;
use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Source\SourceAwareInterface;

interface Target extends Exchange
{
    public function execute(Iterator $source): Result;
}