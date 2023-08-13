<?php
include_once "../../consts.php";
include_Once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/user_handler.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона login.html.
 */
function login(): string
{
    $access = new Access(UserPosition::Guest->value);
    $access->redirectUserToHisHomepageIfNeeded();
    $template_path = TOP_DIR . "/view/common/login.html";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            (new UserHandler())->login($_POST["email"], $_POST["password"]);
            $access->redirectUserToHisHomepageIfNeeded();
        } catch(Exception $exception) {
            $context = ["login_failed" =>$exception->getMessage()];
            return get_html($template_path, $context);
        }
    }

    return get_html($template_path);
}

echo login();