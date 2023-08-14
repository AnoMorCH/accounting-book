<?php
include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/db_handler.php";
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
    $template_path = TOP_DIR . "/view/customer/review/mine-list.html";
    $context = new Context();
    $reviews_list = (new DBHandler())->getObjects(
        "review",
        "user_id",
        $_COOKIE[COOKIE_NAME_OF_USER_ID]
    );
    $context->append("reviews_list", $reviews_list);
    return get_html($template_path, $context->value);
}

echo mine_list();