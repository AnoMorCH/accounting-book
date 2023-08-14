<?php
include_once "../consts.php";
include_once TOP_DIR . "/urls.php";

/**
 * Класс для работы с контекстом - переменной, которая передается из PHP
 * логики в HTML верстки для трансфера данных из серверной части кода ПО в 
 * верстку.
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
            "top_dir" => TOP_DIR
        ];
        return $basic_value;
    }
}