<?php

include_once "../../../consts.php";
include_once TOP_DIR . "/class/access.php";
include_once TOP_DIR . "/class/context.php";
include_once TOP_DIR . "/class/db_handler.php";
include_once TOP_DIR . "/class/user_handler.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/helper.php";

/**
 * Обработать контроллер по демонстрации содержимого шаблона
 * admin/provided-services/create.html.
 */
function create(): string
{
    (new Access(UserPosition::Admin->value))->redirectUserToHisHomepageIfNeeded();
    $context = new Context();
    $user_handler = new UserHandler();
    $customer_position_id = $user_handler->getCustomerPositionId();
    $context->append("users", $user_handler->getAll($customer_position_id));
    $context->append("available_services", (new DBHandler())->getObjects("service"));

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $user_handler->addProvidedServices($_POST["user-id"]);
            $context->append(
                "provided_services_updated_successfully",
                "Список оказанных услуг успешно обновлен"
            );
        } catch (Exception $exception) {
            $context->append(
                "provided_services_hasnt_been_updated",
                $exception->getMessage()
            );
        }
    }

    $template_path = TOP_DIR . "/view/admin/provided-services/create.html";
    return get_html($template_path, $context->value);
}

echo create();
