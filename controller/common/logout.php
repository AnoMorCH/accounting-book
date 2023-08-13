<?php
include_once "../../consts.php";
include_once TOP_DIR . "/class/user_handler.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/enum/user_position.php";

function logout(): void
{
    (new UserHandler())->logout();
    (new Access(UserPosition::Guest->value))->redirectUserToHisHomepageIfNeeded();
}

echo logout();
