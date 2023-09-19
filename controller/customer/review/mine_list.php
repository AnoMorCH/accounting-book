<?php

include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/class/user.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона 
 * customer/review/mine-list.php.
 */
function mine_list(): string
{
    (new Access(UserPosition::Customer->value))->redirectUserToHisHomepageIfNeeded();
    $user_id = (new User())->getCurrentId();
    $reviews_written_by_user = (new Review())->getAllWrittenByUser($user_id);
    $context = new Context();
    $context->append("reviews_list", $reviews_written_by_user);
    $template_path = TOP_DIR . "/view/customer/review/mine-list.html";
    return get_html($template_path, $context->value);
}

echo mine_list();
