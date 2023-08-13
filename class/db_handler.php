<?php

/**
 * Класс для работы с базой данных (используется PDO).
 */
class DBHandler
{
    /**
     * Возвращает переменную для работы с PDO подключением.
     */
    public function getPDOConn(): PDO
    {
        $host_name = "localhost";
        $username = "root";
        $password = "";
        $db_name = "sanatorium";

        // Используй кодировку UTF8, чтобы разрешить загрузку кириллических
        // символов в БД.
        $dsn = "mysql:host={$host_name};dbname={$db_name};charset=UTF8";

        $pdo = new PDO($dsn, $username, $password);

        // Установить способ выгрузки PDO-объектов как основной метод 
        // получения информации из БД.
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        return $pdo;
    }

    /**
     * Получить объект из БД по заданным параметрам.
     */
    public function getObject(
        string $table_name,
        string $attr_name,
        string $attr_value
    ): stdClass {
        $query = "SELECT *
                  FROM {$table_name}
                  WHERE {$attr_name} = :{$attr_name}";
        $pdo_conn = $this->getPDOConn();
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute(["{$attr_name}" => $attr_value]);
        $is_there_obj = $stmt->rowCount() > 0;
        if ($is_there_obj) {
            $obj = $stmt->fetch();
            return $obj;
        } else {
            $error_message = "Объект с переданными параметрами не был найден";
            throw new Exception($error_message);
        }
    }
}
