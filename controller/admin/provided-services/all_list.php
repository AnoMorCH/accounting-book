<?php

include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/user.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер для демонстрации содержимого шаблона
 * admin/provided-services/all_list.html.
 */
function all_list(): string
{
    (new Access(UserPosition::Admin->value))->redirectUserToHisHomepageIfNeeded();
    $context = new Context();
    $user = new User();
    $customer_position_id = $user->getCustomerPositionId();
    $context->append("users_list", $user->getAll($customer_position_id));
    $template_path = TOP_DIR . "/view/admin/provided-services/all-list.html";
    return get_html($template_path, $context->value);
}

echo all_list();
