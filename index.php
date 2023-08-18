<?php

include_once "./consts.php";
include_once TOP_DIR . "/helper.php";
include_once TOP_DIR . "/urls.php";

/**
 * Страница для перенаправления пользователя на его домашнюю страницу в 
 * зависимости от роли (админа - на домашнюю страницу администратора и т.д.).
 */
function index(): void
{
    redirect(URLS["guest_homepage"]);
}

index();
