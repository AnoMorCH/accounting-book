<?php
include "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/db_handler.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона customer/review/index.html.
 */
function index(): string 
{
    $access = new Access(UserPosition::Customer->value);
    $access->redirectUserToHisHomepageIfNeeded();
    $template_path = TOP_DIR . "/view/customer/review/index.html";
    $context = new Context();

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $review = new Review($_GET["id"]);
        $context->append("review", $review->value);
        $context->append("review_author", $review->getAuthor());
        $context->append("review_status", $review->getStatus());
        $context->append("review_services", $review->getServices());
    }

    return get_html($template_path, $context->value);
}

echo index();
