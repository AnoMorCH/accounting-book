<?php
include "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона 
 * controller/admin/review/check.php.
 */
function check(): string
{
    $access = new Access(UserPosition::Admin->value);
    $access->redirectUserToHisHomepageIfNeeded();
    $context = new Context();

    $review_id = $_GET["id"];
    $review = new Review();
    $context->append("review", $review->get($review_id));
    $context->append("review_author", $review->getAuthor($review_id));
    $context->append("review_statuses_list", $review->getAvailableStatuses());

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $review->check($_POST["review-status"], $review_id);
        redirect(URLS["admin_homepage"]);
    }

    $template_path = TOP_DIR . "/view/admin/review/check.html";
    return get_html($template_path, $context->value);
}

echo check();
