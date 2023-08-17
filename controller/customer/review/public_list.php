<?php
include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона
 * customer/review/public-list.php.
 */
function public_list(): string
{
    $access = new Access(UserPosition::Customer->value);
    $access->redirectUserToHisHomepageIfNeeded();
    $context = new Context();
    $context->append("reviews_list", (new Review())->getAllPublished());
    $template_path = TOP_DIR . "/view/customer/review/public-list.html";
    return get_html($template_path, $context->value);
}

echo public_list();