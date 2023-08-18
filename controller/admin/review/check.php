<?php

include_once "../../../consts.php";
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
    (new Access(UserPosition::Admin->value))->redirectUserToHisHomepageIfNeeded();

    $review_id = $_GET["id"];
    $review = new Review();
    $review_obj = $review->get($review_id);
    $review_author = $review->getAuthor($review_id);
    $context = new Context();
    $context->append("review", $review_obj);
    $context->append("review_author", $review_author);
    $context->append("review_statuses_list", $review->getAvailableStatuses());

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $review->check($_POST["review-status"], $review_id);
        send_email($review_author->email, $review_obj->date_of_writing, $_POST["comment"]);
        redirect(URLS["check_list_page"]);
    }

    $template_path = TOP_DIR . "/view/admin/review/check.html";
    return get_html($template_path, $context->value);
}

echo check();
