<?php
include_once "../../consts.php";
include_once TOP_DIR . "/class/user_handler.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/urls.php";

function logout(): void
{
    (new UserHandler())->logout();
    $guest_homepage_url = URLS["guest_homepage"];
    header("Location: {$guest_homepage_url}");
    exit;
}

echo logout();
