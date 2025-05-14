<?php

namespace Sholokhov\BitrixExchange\Helper;

use Throwable;

/**
 * @since 1.0.0
 * @version 1.0.0
 */
class LoggerHelper
{
    /**
     * Преобразование Exception в строку.
     *
     * @param Throwable $throwable
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function exceptionToString(Throwable $throwable): string
    {
        $trace = [];

        foreach ($throwable->getTrace() as $value) {
            if (!is_array($value['args'])) {
                $value['args'] = [];
            }

            foreach ($value['args'] as &$argument) {
                if (is_object($argument)) {
                    $argument = sprintf('Object(%s)', $argument::class);
                }
            }

            $trace[] = $value;
        }

        return json_encode(
            [
                'message' => $throwable->getMessage(),
                'code' => $throwable->getCode(),
                'trace' => $trace
            ],
            JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES
        );
    }

}