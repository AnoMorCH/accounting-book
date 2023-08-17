<?php

include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/class/user_handler.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/urls.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по удалению одного или нескольких отзывов (шаблоны
 * отсутствуют).
 */
function delete(): void 
{
    (new Access(UserPosition::Customer->value))->redirectUserToHisHomepageIfNeeded();
    $review = new Review();
    $review_id = $_GET["id"];
    if (is_null($review_id)) {
        $review->delete(user_id: (new UserHandler())->getCurrentId());
    } else {
        $review->delete(review_id: $review_id);
    }
    redirect(URLS["customer_homepage"]);
}

delete();