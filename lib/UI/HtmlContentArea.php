<?php

namespace Sholokhov\Exchange\UI;

use Bitrix\Main\Engine\Response\ContentArea\ContentAreaInterface;

/**
 * @internal
 * @since 1.2.0
 * @version 1.2.0
 */
readonly class HtmlContentArea implements ContentAreaInterface
{
    /**
     * @param string $html
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function __construct(private string $html)
    {
    }

    /**
     * Получение врестки
     *
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getHtml(): string
    {
        return $this->html;
    }
}