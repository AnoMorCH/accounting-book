<?php
include_once "../../consts.php";
include_once TOP_DIR . "/class/user_handler.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/urls.php";
include_once TOP_DIR . "/helper.php";

function logout(): void
{
    (new UserHandler())->logout();
    redirect(URLS["guest_homepage"]);
}

echo logout();
