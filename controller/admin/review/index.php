<?php
include "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона 
 * admin/review/index.html.
 */
function index(): string
{
    $access = new Access(UserPosition::Admin->value);
    $access->redirectUserToHisHomepageIfNeeded();
    $context = new Context();

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $review_id = $_GET["id"];
        $review = new Review();
        $review_obj = $review->get($review_id);
        $context->append("review", $review_obj);
        $context->append("review_author", $review->getAuthor($review_id));
        $context->append("review_status", $review->getStatus($review_obj->status_id));
        $context->append("review_services", $review->getServices($review_id));
    }

    $template_path = TOP_DIR . "/view/admin/review/index.html";
    return get_html($template_path, $context->value);
}

echo index();
