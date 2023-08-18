<?php

include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/db_handler.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/class/user_handler.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона
 * customer/review/edit.html.
 */
function edit(): string
{
    (new Access(UserPosition::Customer->value))->redirectUserToHisHomepageIfNeeded();

    $review_id = $_GET["id"];
    $context = new Context();
    $db_handler = new DBHandler();
    $review = new Review();

    $context->append("rooms", $db_handler->getObjects("room"));
    $context->append("available_services", $db_handler->getObjects("service"));
    $context->append("review", $review->get($review_id));
    $context->append("selected_services_ids", $review->getSelectedServicesIds($review_id));

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $review->edit(
                $review_id,
                (new UserHandler())->getCurrentId(),
                $_POST["room-number"],
                $_POST["coming-date"],
                $_POST["leaving-date"],
                $_POST["review-text"]
            );
            $context->append(
                "review_edited_successfully",
                "Отзыв успешно изменен. Пожалуйста, обновите страницу, чтобы
                увидеть изменения"
            );
        } catch (Exception $exception) {
            $context->append(
                "review_hasnt_been_edited",
                $exception->getMessage()
            );
        }
    }

    $template_path = TOP_DIR . "/view/customer/review/edit.html";
    return get_html($template_path, $context->value);
}

echo edit();
