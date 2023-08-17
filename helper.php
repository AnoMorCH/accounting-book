<?php

/**
 * Передать HTML-шаблон в качестве строки для реализации взаимосвязи HTML-PHP.
 */
function get_html(string $template_path, array $context = null): string
{
    if (!is_null($context)) {
        foreach ($context as $key => $value) {
            $$key = $value;
        }
    }

    ob_start();
    require($template_path);
    $html = ob_get_clean();

    return $html;
}

/**
 * Вернуть текущую дату в формате "ДД-ММ-ГГГГ".
 */
function get_current_date(): string
{
    return date("Y-m-d");
}

/**
 * Перевести пользователя на указанную страницу.
 */
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}
