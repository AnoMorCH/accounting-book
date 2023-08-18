<?php

include_once "../consts.php";
include_once TOP_DIR . "/urls.php";

/**
 * Класс для взаимодействия с контекстом - объектом, который служит для 
 * передачи данных из серверной части приложения, PHP, в HTML-шаблоны. 
 */
class Context
{
    public $value;

    public function __construct()
    {
        $this->value = $this->_getBasicValue();
    }

    /**
     * Добавить новую переменную в контекст.
     */
    public function append(string $var_name, $var_value): void
    {
        $this->value += [$var_name => $var_value];
    }

    /**
     * Инициализировать контекст.
     */
    private function _getBasicValue(): array
    {
        $basic_value = [
            "urls" => URLS,
            "top_dir" => TOP_DIR,
            "service_obj_prefix" => SERVICE_OBJ_PREFIX,
            "review_status_prefix" => REVIEW_STATUS_PREFIX,
        ];
        return $basic_value;
    }
}
