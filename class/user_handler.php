<?php
include_once "../consts.php";
include_once TOP_DIR . "/class/db_handler.php";
include_once TOP_DIR . "/enum/user_position.php";

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
        if ($this->_canBeCreated($email)) {
            $this->_create($email, $password, $first_name, $last_name);
        } else {
            $error_message = "К сожалению, данный адрес эл. почты уже занят.";
            throw new Exception($error_message);
        }
    }

    /**
     * Авторизовать пользователя, основываясь на информации, переданной 
     * клиентом на сервер через веб-интерфейс HTML.
     */
    public function login(string $email, string $password): void
    {
        $user = $this->getObject("user", "email", $email);
        if ($this->_isPasswordCorrect($user->password, $password)) {
            $this->_loginUsingCookie($user->id);
        } else {
            $error_message = "Не удалось войти. Переданы неверные данные";
            throw new Exception($error_message);
        }
    }

    /**
     * Выйти из текущей учетной записи путем удаления из куки ИД текущего 
     * пользователя (проверка, авторизован ли пользователь, задается при 
     * помощи задачи его ИД).
     */
    public function logout(): void
    {
        print_r("I am here");
        if (isset($_COOKIE[COOKIE_NAME_OF_USER_ID])) {
            unset($_COOKIE[COOKIE_NAME_OF_USER_ID]);
        }
        setcookie(COOKIE_NAME_OF_USER_ID, "", time() - 1, "/");
    }

    /**
     * Вернуть ИД текущего пользователя.
     */
    public function getCurrentId(): int
    {
        return $_COOKIE[COOKIE_NAME_OF_USER_ID];
    }

    /**
     * Вернуть url домашней страницы пользователя.
     */
    public function getHomepageUrl(string $user_position): string 
    {
        $basic_homepages_urls = $this->_getBasicHomepage();
        $homepage_url = $basic_homepages_urls[$user_position];
        return $homepage_url;
    }

    /**
     * Получить словарь типа "роль пользователя" => "ссылка на домашнюю стр.".
     */
    protected function _getBasicHomepage(): array
    {
        $basic_users_homepage = [
            UserPosition::Guest->value => URLS["guest_homepage"],
            UserPosition::Customer->value => URLS["customer_homepage"],
            UserPosition::Admin->value => URLS["admin_homepage"],
        ];
        return $basic_users_homepage;
    }

    /**
     * Авторизовать пользователя в систему через механизм COOKIE.
     */
    private function _loginUsingCookie(string $user_id): void
    {
        $cookie_lifespan = 3600;
        $cookie_expiration_time = time() + $cookie_lifespan;
        setcookie(COOKIE_NAME_OF_USER_ID, $user_id, $cookie_expiration_time, "/");
    }

    /**
     * Проверить, является ли пароль, переданный клиентом через
     * веб-интерфейс HTML, правильным на основании информации, которая 
     * хранится в БД.
     */
    private function _isPasswordCorrect(
        string $actual_password,
        string $inputted_password
    ): bool {
        return $actual_password == $inputted_password;
    }

    /**
     * Вернуть текущую позицию пользователя.
     */
    public function getPosition(): string
    {
        if ($this->_isAuthenticated()) {
            $position_id = $this->_getPositionId();
            $position_obj = $this->getObject("user_position", "id", $position_id);
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
        $user_id = $this->getCurrentId();
        $user_obj = $this->getObject("user", "id", $user_id);
        $user_position_id = $user_obj->position_id;
        return $user_position_id;
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
