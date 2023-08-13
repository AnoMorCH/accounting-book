<?php
include_once "../consts.php";
include_once TOP_DIR . "/class/db_handler.php";

/**
 * Класс, который используется для реализации любых манипуляций, связанных с 
 * пользователем.
 */
class UserHandler extends DBHandler
{
    const STANDARD_POSITION = UserPosition::Customer->value;

    /**
     * Создать пользователя, основываясь на информации, переданной клиентом на
     * сервер через веб-интерфейс HTML.
     */
    public function signup(
        string $email,
        string $password,
        string $first_name,
        string $last_name
    ): void {
        if (!$this->_canBeCreated($email)) {
            $error_message = "К сожалению, данный адрес эл. почты уже занят.";
            throw new Exception($error_message);
        }
        $this->_create($email, $password, $first_name, $last_name);
    }

    /**
     * Вернуть текущую позицию пользователя.
     */
    public function getPosition(): string
    {
        if ($this->_isAuthenticated()) {
            $position_id = $this->_getPositionId();
            $position_obj = $this->getObject("user_position", "name", $position_id);
            $position = $position_obj->name;
            return $position;
        } else {
            return UserPosition::Guest->value;
        }
    }

    /**
     * Получить ИД стандартной позиции при регистрации нового пользователя.
     */
    private function _getStandardPositionId(): int
    {
        $standard_position_obj = $this->getObject(
            "user_position",
            "name",
            self::STANDARD_POSITION
        );
        return $standard_position_obj->id;
    }

    /**
     * Вернуть текущий ИД позиции пользователя.
     */
    private function _getPositionId(): int
    {
        $user_id = $this->_getCurrentId();
        $user_obj = $this->getObject("user", "role_id", $user_id);
        $user_position_id = $user_obj->position_id;
        return $user_position_id;
    }

    /**
     * Вернуть ИД текущего пользователя.
     */
    private function _getCurrentId(): int
    {
        return $_COOKIE[COOKIE_NAME_OF_USER_ID];
    }

    /**
     * Создать нового пользователя.
     */
    private function _create(
        string $email,
        string $password,
        string $first_name,
        string $last_name
    ): void {
        $query = "INSERT INTO user (email, password, first_name, last_name, position_id)
                  VALUES (:email, :password, :first_name, :last_name, :position_id);";
        $pdo_conn = $this->getPDOConn();
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([
            "email" => $email,
            "password" => $password,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "position_id" => $this->_getStandardPositionId()
        ]);
    }

    /**
     * Проверить, авторизовался ли пользователь.
     */
    public function _isAuthenticated(): bool
    {
        return isset($_COOKIE[COOKIE_NAME_OF_USER_ID]);
    }

    /**
     * Проверить, является ли переданные данными корректными, чтобы создать
     * нового пользователя. 
     */
    private function _canBeCreated(string $email): bool
    {
        return $this->_isEmailUnique($email);
    }

    /**
     * Проверить, есть ли уже пользователи с запрашиваемой эл. почтой. 
     */
    private function _isEmailUnique(string $email): bool
    {
        $query = "SELECT * FROM user WHERE email = :email";
        $pdo_conn = $this->getPDOConn();
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute(["email" => $email]);
        $users_with_the_same_email_amount = $stmt->rowCount();
        $has_email_already_been_used = $users_with_the_same_email_amount > 0;
        $is_email_unique = !$has_email_already_been_used;
        return $is_email_unique;
    }
}
