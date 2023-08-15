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
    /**
     * Получить объект сущности user, где пользователь является автором
     * какого-либо отзыва.
     */
    public function getAuthor(string $review_id): stdClass
    {
        $review = $this->getObject("review", "id", $review_id);
        $author = $this->getObject("user", "id", $review->user_id);
        return $author;
    }

    /**
     * Получить объект сущности review_status, где показан статус какого-либо
     * отзыва в качестве объекта stdClass.
     */
    public function getStatus(string $review_id): stdClass
    {
        $status = $this->getObject("review_status", "id", $review_id);
        return $status;
    }

    /**
     * Получить список оказанных в указанном отзыве услуг в качестве списка 
     * строк.
     */
    public function getServices(string $review_id): array
    {
        $review_services_ids = $this->getObjects("review_n_service", "review_id", $review_id);
        $review_services = [];
        foreach ($review_services_ids as $review_service_id) {
            $service = $this->getObject("service", "id", $review_service_id->service_id);
            array_push($review_services, $service->name);
        }
        return $review_services;
    }

    /**
     * Получить все отзывы, написанные пользователем с определенным ИД.
     */
    public function getAllWrittenByUser(string $user_id): array
    {
        $reviews_list = $this->getObjects("review", "user_id", $user_id);
        return $reviews_list;
    }

    /**
     * Получить объект review из БД по его ИД.
     */
    public function get(string $review_id): stdClass
    {
        $obj = $this->getObject("review", "id", $review_id);
        return $obj;
    }
}
