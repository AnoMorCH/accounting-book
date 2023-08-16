<?php
include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/db_handler";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона
 * customer/review/create.html.
 */
function create(): string
{
    $access = new Access(UserPosition::Customer->value);
    $access->redirectUserToHisHomepageIfNeeded();
    $context = new Context();
    $db_handler = new DBHandler();
    $context->append("rooms", $db_handler->getObjects("room"));
    $context->append("available_services", $db_handler->getObjects("service"));

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            (new Review())->create(
                $_COOKIE[COOKIE_NAME_OF_USER_ID],
                $_POST["room-number"],
                $_POST["coming-date"],
                $_POST["leaving-date"],
                $_POST["review-text"]
            );
            $context->append(
                "review_created_successfully",
                "Отзыв успешно оставлен"
            );
        } catch (Exception $exception) {
            $context->append(
                "review_hasnt_been_created",
                $exception->getMessage()
            );
        }
    }

    $template_path = TOP_DIR . "/view/customer/review/create.html";
    return get_html($template_path, $context->value);
}

echo create();