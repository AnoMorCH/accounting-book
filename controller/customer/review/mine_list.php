<?php
include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/class/user_handler.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона 
 * customer/review/mine-list.php.
 */
function mine_list(): string
{
    $access = new Access(UserPosition::Customer->value);
    $access->redirectUserToHisHomepageIfNeeded();
    $context = new Context();
    $user_id = (new UserHandler())->getCurrentId();
    $reviews_written_by_user = (new Review())->getAllWrittenByUser($user_id);
    $context->append("reviews_list", $reviews_written_by_user);
    $template_path = TOP_DIR . "/view/customer/review/mine-list.html";
    return get_html($template_path, $context->value);
}

echo mine_list();