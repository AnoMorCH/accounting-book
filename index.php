<?php
include_once "./consts.php";
include_once TOP_DIR . "/helper.php";
include_once TOP_DIR . "/urls.php";

function index(): void
{
    redirect(URLS["guest_homepage"]);
}

index();
