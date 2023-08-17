<?php
include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона
 * admin/review/check-list.php.
 */
function check_list(): string 
{
    $access = new Access(UserPosition::Admin->value);
    $access->redirectUserToHisHomepageIfNeeded();
    $context = new Context();
    $context->append("unchecked_reviews_list", (new Review)->getUncheckedAndSuspended());
    $template_path = TOP_DIR . "/view/admin/review/check-list.html";
    return get_html($template_path, $context->value);
}

echo check_list();