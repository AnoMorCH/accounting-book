<?php
include_once "../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/user_handler.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона common/login.html.
 */
function login(): string
{
    $access = new Access(UserPosition::Guest->value);
    $access->redirectUserToHisHomepageIfNeeded();
    // TODO. Put "template_path" everywhere at the bottom of the func.
    $template_path = TOP_DIR . "/view/common/login.html";
    $context = new Context();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            (new UserHandler())->login($_POST["email"], $_POST["password"]);
            $access->redirectUserToHisHomepageIfNeeded();
        } catch (Exception $exception) {
            $context->append("login_failed", $exception->getMessage());
        }
    }

    return get_html($template_path, $context->value);
}

echo login();
