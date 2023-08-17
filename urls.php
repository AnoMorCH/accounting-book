<?php

// Секции для построения URL-ссылок.
$subfolder = "au";
$controller_folder = "controller";
$unauthorized_user_folder = "common";
$customer_folder = "customer";
$admin_folder = "admin";
$review_folder = "review";

// URL-ссылки.
$urls = [
    // Страницы неавторизованного пользователя.
    "guest_homepage" => "/$subfolder/$controller_folder/$unauthorized_user_folder/login.php",
    "login_page" => "/$subfolder/$controller_folder/$unauthorized_user_folder/login.php",
    "signup_page" => "/$subfolder/$controller_folder/$unauthorized_user_folder/signup.php",
    
    // Страницы клиента.
    "customer_homepage" => "/$subfolder/$controller_folder/$customer_folder/$review_folder/mine_list.php",
    "customer_this_review_page" => "/$subfolder/$controller_folder/$customer_folder/$review_folder/index.php",
    "my_reviews_page" => "/$subfolder/$controller_folder/$customer_folder/$review_folder/mine_list.php",
    "create_review_page" => "/$subfolder/$controller_folder/$customer_folder/$review_folder/create.php",
    "edit_review_page" => "/$subfolder/$controller_folder/$customer_folder/$review_folder/edit.php",
    "delete_review_page" => "/$subfolder/$controller_folder/$customer_folder/$review_folder/delete.php",
    "public_reviews_page" => "/$subfolder/$controller_folder/$customer_folder/$review_folder/public_list.php",
    
    // Страницы администратора.
    "admin_homepage" => "/$subfolder/$controller_folder/$admin_folder/$review_folder/check_list.php",
    "check_list_page" => "/$subfolder/$controller_folder/$admin_folder/$review_folder/check_list.php",
    "admin_this_review_page" => "/$subfolder/$controller_folder/$admin_folder/$review_folder/index.php",
    "check_review_page" => "/$subfolder/$controller_folder/$admin_folder/$review_folder/check.php",
    "all_reviews_page" => "/$subfolder/$controller_folder/$admin_folder/$review_folder/all_list.php",
    
    // Общие страницы авторизованных пользователей.
    "logout_page" => "/$subfolder/$controller_folder/$unauthorized_user_folder/logout.php",
];

define("URLS", $urls);
