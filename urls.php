<?php

const URLS = [
    // Страницы неавторизованного пользователя.
    "guest_homepage" => "/au/controller/common/login.php",
    "login_page" => "/au/controller/common/login.php",
    "signup_page" => "/au/controller/common/signup.php",

    // Страницы клиента.
    "customer_homepage" => "/au/controller/customer/review/mine-list.php",
    "this_review_page" => "/au/controller/customer/review/index.php",
    "my_reviews_page" => "/au/controller/customer/review/mine-list.php",
    "public_reviews_page" => "/au/controller/customer/review/public-list.php",

    // Страницы администратора.
    "admin_homepage" => "",

    // Общие страницы авторизованных пользователей.
    "logout_page" => "/au/controller/common/logout.php",
];
