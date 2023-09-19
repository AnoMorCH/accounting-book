<?php

include_once "../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/user.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона common/login.html.
 */
function login(): string
{
    (new Access(UserPosition::Guest->value))->redirectUserToHisHomepageIfNeeded();
    $context = new Context();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $user = new User();
            $user->login($_POST["email"], $_POST["password"]);
            $user_position = $user->getPosition();
            $user_homepage_url = $user->getHomepageUrl($user_position);
            redirect($user_homepage_url);
        } catch (Exception $exception) {
            $context->append("login_failed", $exception->getMessage());
        }
    }

    $template_path = TOP_DIR . "/view/common/login.html";
    return get_html($template_path, $context->value);
}

echo login();
