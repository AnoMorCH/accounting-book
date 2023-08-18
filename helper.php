<?php

include_once "./consts.php";

// Подключение PHP Mailer, установленного через composer.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require TOP_DIR . "/vendor/autoload.php";

/**
 * Передать HTML-шаблон в качестве строки для реализации взаимосвязи HTML-PHP.
 */
function get_html(string $template_path, array $context = null): string
{
    if (!is_null($context)) {
        foreach ($context as $key => $value) {
            $$key = $value;
        }
    }

    ob_start();
    require($template_path);
    $html = ob_get_clean();

    return $html;
}

/**
 * Вернуть текущую дату в формате "ДД-ММ-ГГГГ".
 */
function get_current_date(): string
{
    return date("Y-m-d");
}

/**
 * Перевести пользователя на указанную страницу.
 */
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

/**
 * Отправить эл. письмо на почту через PHPMailer (переменная $subject 
 * содержит свой заголовок на английском языке, потому что PHPMailer не
 * поддерживает отправку заголовков с кириллицей).
 */
function send_email(
    string $address,
    string $review_date_of_writing,
    string $body
): void {
    $subject = "You got a notification about your review created
                $review_date_of_writing";
    
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "tokisakar@gmail.com";
    $mail->Password = "dzotmexcvmbkmieu";
    $mail->SMTPSecure = "ssl";
    $mail->Port = 465;

    $mail->setFrom("tokisakar@gmail.com");
    $mail->addAddress($address);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->send();
}
