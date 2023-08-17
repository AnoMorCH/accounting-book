<?php
include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона
 * admin/review/all-list.html.
 */
function all_list(): string
{
    (new Access(UserPosition::Admin->value))->redirectUserToHisHomepageIfNeeded();
    $context = new Context();
    $context->append("reviews_list", (new Review())->getAll());
    $template_path = TOP_DIR . "/view/admin/review/all-list.html";
    return get_html($template_path, $context->value);
}

echo all_list();
