<?php

include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/db_handler";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/class/user.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона
 * customer/review/create.html.
 */
function create(): string
{
    (new Access(UserPosition::Customer->value))->redirectUserToHisHomepageIfNeeded();
    $context = new Context();
    $db_handler = new DBHandler();
    $current_user_id = (new User())->getCurrentId();
    $context->append("rooms", $db_handler->getObjects("room"));
    $context->append("provided_services", (new User())->getProvidedServices($current_user_id));

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            (new Review())->create(
                $current_user_id,
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
