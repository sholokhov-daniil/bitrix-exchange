<?php

namespace Sholokhov\Exchange\Target;

use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Source\SourceAwareInterface;

interface TargetInterface extends SourceAwareInterface
{
    public function execute(): ResultInterface;
}