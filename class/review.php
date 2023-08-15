<?php

include_once "../consts.php";
include_once TOP_DIR . "/class/db_handler.php";

/**
 * Класс для обработки запросов, связанных с сущностью review из БД или с 
 * любыми другими сущностями, которые логически соединены с review (например,
 * review_status и review_n_service).
 */
class Review extends DBHandler
{
    public $value;

    public function __construct(string $id)
    {
        $this->value = $this->_getObject($id);
    }

    /**
     * Получить объект сущности user, где пользователь является автором
     * какого-либо отзыва.
     */
    public function getAuthor(): stdClass
    {
        $author = $this->getObject("user", "id", $this->value->user_id);
        return $author;
    }

    /**
     * Получить объект сущности review_status, где показан статус какого-либо
     * отзыва в качестве объекта stdClass.
     */
    public function getStatus(): stdClass
    {
        $status = $this->getObject(
            "review_status", 
            "id", 
            $this->value->status_id
        );
        return $status;
    }

    /**
     * Получить список оказанных в указанном отзыве услуг в качестве списка 
     * строк.
     */
    public function getServices(): array
    {
        $review_services_ids = $this->getObjects("review_n_service", "review_id", $this->value->id);
        $review_services = [];
        foreach ($review_services_ids as $review_service_id) {
            $service = $this->getObject("service", "id", $review_service_id->service_id);
            array_push($review_services, $service->name);
        }
        return $review_services;
    }

    /**
     * Получить объект review из БД по его ИД.
     */
    private function _getObject(string $id): stdClass
    {
        $obj = $this->getObject("review", "id", $id);
        return $obj;
    }
}
