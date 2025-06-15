<?php

namespace Sholokhov\Exchange\UI\View;

use Sholokhov\Exchange\Cache\CacheInterface;
use Sholokhov\Exchange\Cache\CacheAwareInterface;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Адаптер позволяющий работать с шаблонизатором twig.
 * @link https://twig.symfony.com/doc/3.x/
 *
 * @version 3.21 - Версия шаблонизатора под которую был реализован адаптер.
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class View implements CacheAwareInterface
{
    /**
     * Шаблонизатор twig
     *
     * @var Environment
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected Environment $view;

    /**
     * Расширение шаблона по умолчанию.
     *
     * @var string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected string $expansion = ".twig";

    /**
     * @param string $viewPath Корневая директория шаблонов
     * @param array $options Конфигурация twig
     */
    public function __construct(string $viewPath, array $options = [])
    {
        $loader = new FilesystemLoader($viewPath);
        $this->view = new Environment($loader, $options);

        if (!empty($options['debug'])) {
            $this->view->addExtension(new DebugExtension);
        }
    }

    /**
     * Отрисовка шаблона
     *
     * В пути не указывается расширение {@see static::$expansion}.
     * Данное расширение подставляется автоматически.<br>
     * Если нам не нужна автоподстановка, в параметр $autoExpansion нужно передать отрицание.
     *
     * @param string $view
     * @param array $data
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function render(string $view, array $data): string
    {
        return $this->view->render($this->prepareView($view), $data);
    }

    /**
     * Установка кеша представления
     *
     * @param CacheInterface $cache
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setCache(CacheInterface $cache): void
    {
        $this->view->setCache(new Cache($cache));
    }

    /**
     * Преобразование названия визуальной части.
     *
     * @param string $view
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected function prepareView(string $view): string
    {
        return $view .= $this->expansion;
    }
}