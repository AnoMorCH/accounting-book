<?php

include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/user_handler.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона 
 * admin/provided-services/index.html.
 */
function index(): string
{
    (new Access(UserPosition::Admin->value))->redirectUserToHisHomepageIfNeeded();
    $context = new Context();

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $user_id = $_GET["id"];
        $user_handler = new UserHandler();
        $context->append("user", $user_handler->get($user_id));
        $context->append("provided_services", $user_handler->getProvidedServices($user_id));
    }

    $template_path = "view/admin/provided-services/index.html";
    return get_html($template_path, $context->value);
}

echo index();
