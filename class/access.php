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

    public function __construct(string $authorized_user_position) {
        $this->_authorized_user_position = $authorized_user_position;
        $this->_actual_user_position = (new UserHandler())->getPosition();
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
     * Получить адрес домашней страницы
     */
    private function _getUsersHomepageRelativeAddr(): string 
    {
        $users_homepage_addr = null;

        if ($this->_actual_user_position == UserPosition::Guest->value) {
            $users_homepage_addr = GUESTS_HOMEPAGE_ADDR;
        } elseif ($this->_actual_user_position == UserPosition::Customer->value) {
            $users_homepage_addr = CUSTOMER_HOMEPAGE_ADDR;
        } elseif ($this->_actual_user_position == UserPosition::Admin->value) {
            $users_homepage_addr = ADMIN_HOMEPAGE_ADDR;
        }

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
