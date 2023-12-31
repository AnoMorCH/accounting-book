<?php

include_once "../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/user.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона signup.html.
 */
function signup(): string
{
    (new Access(UserPosition::Guest->value))->redirectUserToHisHomepageIfNeeded();
    $context = new Context();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            (new User())->signup(
                $_POST["email"],
                $_POST["password"],
                $_POST["first-name"],
                $_POST["last-name"]
            );
            $context->append(
                "user_created_successfully",
                "Вы успешно зарегистрировались"
            );
        } catch (Exception $exception) {
            $context->append(
                "user_hasnt_been_created",
                $exception->getMessage()
            );
        }
    }

    $template_path = TOP_DIR . "/view/common/signup.html";
    return get_html($template_path, $context->value);
}

echo signup();
