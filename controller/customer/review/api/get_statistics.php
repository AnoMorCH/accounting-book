<?php

include_once "../../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/review.php";
include_once TOP_DIR . "/class/user.php";
include_once TOP_DIR . "/enum/user_position.php";

/**
 * Вернуть статистику, в которой отображено, сколько у пользователя одобренных
 * и заблокированных отзывов.
 */
function get_statistics(): void
{
    (new Access(UserPosition::Customer->value))->redirectUserToHisHomepageIfNeeded();
    $user_id = (new User())->getCurrentId();
    $statistics = (new Review())->getStatistics($user_id);
    $json_encoded_statistics = json_encode($statistics);
    echo $json_encoded_statistics;
}

get_statistics();
