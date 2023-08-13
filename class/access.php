<?php
include_once "../consts.php";
include_once TOP_DIR . "/enum/user_position.php";
include_once TOP_DIR . "/class/user_handler.php";

/**
 * Класс для контроля доступа пользователя к тем или иным участкам проекта.
 */
class Access extends UserHandler
{
    private $_authorized_user_position;
    private $_actual_user_position;
    private $_users_homepage_by_position;

    public function __construct(string $authorized_user_position) {
        $this->_authorized_user_position = $authorized_user_position;
        $this->_actual_user_position = $this->getPosition();
        $this->_users_homepage_by_position = $this->_getBasicUsersHomepage();
    }

    /**
     * Перевести пользователя на его домашнюю страницу, если он открыл часть
     * проекта, которая не предназначена для него.
     */
    public function redirectUserToHisHomepageIfNeeded(): void
    {
        if (!$this->_canUserGoToThePage()) {
            $this->_redirectUserToHisHomepage();
        }
    }

    /**
     * Перенаправить пользователя на его домашнюю страницу.
     */
    private function _redirectUserToHisHomepage(): void
    {
        $users_homepage_addr = $this->_getUsersHomepageRelativeAddr();
        header("Location: {$users_homepage_addr}");
        exit;
    }

    /**
     * Получить словарь типа "роль пользователя" => "ссылка на домашнюю стр.".
     */
    private function _getBasicUsersHomepage(): array 
    {
        $basic_users_homepage = [
            UserPosition::Guest->value => GUESTS_HOMEPAGE_ADDR,
            UserPosition::Customer->value => CUSTOMER_HOMEPAGE_ADDR,
            UserPosition::Admin->value => ADMIN_HOMEPAGE_ADDR,
        ];
        return $basic_users_homepage;
    }

    /**
     * Получить адрес домашней страницы
     */
    private function _getUsersHomepageRelativeAddr(): string 
    {
        $users_homepage_addr = $this->_users_homepage_by_position[
            $this->_actual_user_position
        ];

        if (is_null($users_homepage_addr)) {
            $error_message = "Дана неизвестная роль для пользователя!";
            throw new Exception($error_message);
        } else {
            return $users_homepage_addr;
        }
    }

    /** 
     * Проверить, авторизован ли пользователь для посещения данной части
     * проекта.
     */
    private function _canUserGoToThePage(): bool
    {
        return $this->_actual_user_position == $this->_authorized_user_position;
    }
}
