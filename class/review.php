<?php

include_once "../consts.php";
include_once TOP_DIR . "/class/db_handler.php";
include_once TOP_DIR . "/enum/review_status.php";
include_once TOP_DIR . "/helper.php";

/**
 * Класс для обработки запросов, связанных с сущностью review из БД или с 
 * любыми другими сущностями, которые логически соединены с review (например,
 * review_status и review_n_service).
 */
class Review extends DBHandler
{
    private $_standard_status_obj;

    public function __construct() {
        $this->_standard_status_obj = $this->getObject(
            "review_status",
            "name",
            ReviewStatus::NotChecked->value
        );
    }

    /**
     * Получить объект review из БД по его ИД.
     */
    public function get(string $review_id): stdClass
    {
        $obj = $this->getObject("review", "id", $review_id);
        return $obj;
    }

    /**
     * Создать объект review на основе информации, переданной пользователем 
     * через веб-интерфейс.
     */
    public function create(
        string $user_id,
        string $room_number,
        string $living_start_date,
        string $living_stop_date,
        string $text
    ): void {
        $query = "INSERT INTO review (
                      user_id,
                      room_number,
                      living_start_date,
                      living_stop_date,
                      date_of_writing,
                      status_id,
                      text
                  ) VALUES (
                      :user_id,
                      :room_number,
                      :living_start_date,
                      :living_stop_date,
                      :date_of_writing,
                      :status_id,
                      :text
                  )";
        $pdo_conn = $this->getPDOConn();
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([
            "user_id" => $user_id,
            "room_number" => $room_number,
            "living_start_date" => $living_start_date,
            "living_stop_date" => $living_stop_date,
            "date_of_writing" => get_current_date(),
            "status_id" => $this->_standard_status_obj->id, 
            "text" => $text
        ]);
    }

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
}
