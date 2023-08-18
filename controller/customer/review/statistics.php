<?php
include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/enum/user_position.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона
 * customer/review/statistics.php.
 */
function statistics(): string 
{
    (new Access(UserPosition::Customer->value))->redirectUserToHisHomepageIfNeeded();
    $template_path = TOP_DIR . "/view/customer/review/statistics.html";
    return get_html($template_path, (new Context())->value);
}

echo statistics();
