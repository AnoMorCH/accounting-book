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