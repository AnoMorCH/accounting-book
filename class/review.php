<?php

include_once "../consts.php";
include_once TOP_DIR . "/class/db_handler.php";
include_once TOP_DIR . "/enum/review_status.php";
include_once TOP_DIR . "/enum/limit_id.php";
include_once TOP_DIR . "/helper.php";

/**
 * Класс для обработки запросов, связанных с сущностью review из БД или с 
 * любыми другими сущностями, которые логически соединены с review (например,
 * review_status и review_n_service).
 */
class Review extends DBHandler
{
    private $_standard_status_obj;

    public function __construct()
    {
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
     * Получить все экземпляры сущности review из БД.
     */
    public function getAll(): array
    {
        $objs = $this->getObjects("review");
        return $objs;
    }

    /**
     * Получить все экземпляры сущности review из БД, где отзыв имеет статус
     * опубликованного.
     */
    public function getAllPublished(): array
    {
        $review_published_status = $this->getObject("review_status", "name", ReviewStatus::Published->value);
        $objs = $this->getObjects("review", "status_id", $review_published_status->id);
        return $objs;
    }

    /**
     * Добавить информацию о review и оказанных услугах на основании 
     * информации, переданной пользователем через веб-интерфейс,
     */
    public function create(
        string $user_id,
        string $room_number,
        string $living_start_date,
        string $living_stop_date,
        string $text
    ): void {
        if (!$this->_isDateCreationCorrect($living_stop_date, $living_start_date)) {
            throw new Exception("Ошибка! Дата выселения поставлена раньше чем дата заселения.");
        }
        $this->_create(
            $user_id,
            $room_number,
            $living_start_date,
            $living_stop_date,
            $text
        );
        $just_created_review_id = $this->getObjectByLimitId(
            LimitId::Max->value,
            "review"
        );
        $this->_createServices($just_created_review_id->id);
    }

    /**
     * Изменить информацию о существующем review и оказанных услугах на 
     * основании информации, переданной пользователем через веб-интерфейс.
     */
    public function edit(
        string $review_id,
        string $user_id,
        string $living_start_date,
        string $living_stop_date,
        string $text
    ): void {
        $this->_edit(
            $review_id,
            $user_id,
            $living_start_date,
            $living_stop_date,
            $text
        );
        $this->_editSelectedServices($review_id);
    }

    /**
     * Удалить отзыв(ы). Если передан атрибут review_id, то удалить отзыв с 
     * указанным ИДом. Иначе если передан только ИД пользователя, то удалить
     * все отзывы, которые ему(ей) принадлежат.
     */
    public function delete(
        string $review_id = null,
        string $user_id = null
    ): void {
        if (!is_null($review_id)) {
            $this->deleteObjects("review", "id", $review_id);
        } elseif (!is_null($user_id)) {
            $this->deleteObjects("review", "user_id", $user_id);
        }
    }

    /**
     * Изменяет существующий отзыв на основании проверки, выполненной
     * администратором через веб-интерфейс.
     */
    // ! Реализация хранимых процедур.
    // * Обычная версия функции.
    // public function check(string $dirty_review_status, string $review_id): void
    // {
    //     $query = "UPDATE review
    //               SET status_id = :status_id
    //               WHERE id = :id";
    //     $pdo_conn = $this->getPDOConn();
    //     $stmt = $pdo_conn->prepare($query);
    //     $stmt->execute([
    //         "status_id" => $this->_getCleanReviewStatus($dirty_review_status),
    //         "id" => $review_id
    //     ]);
    // }
    // * Версия функции с хранимыми процедурами.
    public function check(string $dirty_review_status, string $review_id): void
    {
        $clean_review_status = $this->_getCleanReviewStatus($dirty_review_status);
        $procedure_name = 'Update Review';
        $mysqli = $this->getMySQLi();
        $mysqli->query("DROP PROCEDURE IF EXISTS `{$procedure_name}`");
        $mysqli->query("CREATE PROCEDURE `{$procedure_name}`()
                        BEGIN
                            UPDATE review
                            SET status_id = {$clean_review_status}
                            WHERE id = {$review_id};
                        END");
        $mysqli->query("CALL `{$procedure_name}`();");
    }

    /**
     * Почистить статус отзыва, переданный через веб-интерфейс, от лишней
     * информации.
     */
    private function _getCleanReviewStatus(string $dirty_review_status): string
    {
        $clean_review_status = str_replace(REVIEW_STATUS_PREFIX, "", $dirty_review_status);
        return $clean_review_status;
    }

    /**
     * Проверить, поставлена ли дата выселения позже чем дата заселения.
     */
    private function _isDateCreationCorrect(string $living_stop_date, string $living_start_date): bool
    {
        $start_date = new DateTime($living_start_date);
        $stop_date = new DateTime($living_stop_date);
        return $start_date < $stop_date;
    }

    /**
     * Получить непроверенные отзывы.
     */
    public function getUncheckedAndSuspended(): array
    {
        $unchecked_review_status = $this->getObject("review_status", "name", ReviewStatus::NotChecked->value);
        $unchecked_reviews = $this->getObjects("review", "status_id", $unchecked_review_status->id);
        $suspended_review_status = $this->getObject("review_status", "name", ReviewStatus::Suspended->value);
        $suspended_reviews = $this->getObjects("review", "status_id", $suspended_review_status->id);
        return array_merge($unchecked_reviews, $suspended_reviews);
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

    /**
     * Получить ИДы всех сервисов, которые пользователь выбрал при создании
     * отзыва.
     */
    public function getSelectedServicesIds(string $review_id): array
    {
        $review_services = $this->getObjects("review_n_service", "review_id", $review_id);
        $selected_services_ids = [];
        foreach ($review_services as $review_service) {
            $selected_service_id = $review_service->service_id;
            array_push($selected_services_ids, $selected_service_id);
        }
        return $selected_services_ids;
    }

    /**
     * Получить все доступные для использования статусы отзыва как список
     * объектов stdClass.
     */
    public function getAvailableStatuses(): array
    {
        $available_statuses = $this->getObjects("review_status");
        return $available_statuses;
    }

    /**
     * Вернуть статистику, в которой отображено, сколько у пользователя 
     * одобренных и заблокированных отзывов.
     */
    public function getStatistics(string $user_id): array
    {
        $accepted_reviews_amount = $this->_getReviewsAmount(
            ReviewStatus::Published->value,
            $user_id
        );
        $declined_reviews_amount = $this->_getReviewsAmount(
            ReviewStatus::Suspended->value,
            $user_id
        );
        $statistics = [
            "accepted_reviews_amount" => $accepted_reviews_amount,
            "declined_reviews_amount" => $declined_reviews_amount
        ];
        return $statistics;
    }

    /**
     * Вернуть кол-во отзывов определенного типа у пользователя с конкретным 
     * ИД (например, количество одобренных отзывов и т.д.)
     */
    private function _getReviewsAmount(
        string $type_of_review,
        string $user_id
    ): int {
        $review_status = $this->getObject(
            "review_status",
            "name",
            $type_of_review
        );
        $query = "SELECT *
                  FROM review
                  WHERE user_id = :user_id
                        AND status_id = :status_id";
        $pdo_conn = $this->getPDOConn();
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([
            "user_id" => $user_id,
            "status_id" => $review_status->id
        ]);
        $reviews_amount = $stmt->rowCount();
        return $reviews_amount;
    }

    /**
     * Изменить информацию о существующем review.
     */
    private function _edit(
        string $review_id,
        string $user_id,
        string $living_start_date,
        string $living_stop_date,
        string $text
    ): void {
        $query = "UPDATE review
                  SET user_id = :user_id,
                      living_start_date = :living_start_date,
                      living_stop_date = :living_stop_date,
                      text = :text
                  WHERE id = :id";
        $pdo_conn = $this->getPDOConn();
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([
            "user_id" => $user_id,
            "living_start_date" => $living_start_date,
            "living_stop_date" => $living_stop_date,
            "text" => $text,
            "id" => $review_id
        ]);
    }

    /**
     * Изменить выбранные услуги в отзыве.
     */
    private function _editSelectedServices(string $review_id): void
    {
        $this->_deleteReviewServices($review_id);
        $this->_createServices($review_id);
    }

    /**
     * Удалить все услуги, прикрепленные к определенному отзыву.
     */
    private function _deleteReviewServices(string $review_id): void
    {
        $this->deleteObjects("review_n_service", "review_id", $review_id);
    }

    // TODO. There should be a way to do it better.
    /**
     * На основании данных, введенных пользователем в веб-интерфейсе, 
     * заполнить таблицу review_n_service.
     */
    private function _createServices(string $review_id)
    {
        for (
            $service_id = $this->getObjectByLimitId(LimitId::Min->value, "service")->id;
            $service_id <= $this->getObjectByLimitId(LimitId::Max->value, "service")->id;
            $service_id++
        ) {
            try {
                $service_obj_prefix = SERVICE_OBJ_PREFIX;
                $service_tag_name = "{$service_obj_prefix}{$service_id}";
                if (isset($_POST[$service_tag_name])) {
                    $query = "INSERT INTO review_n_service (review_id, service_id) 
                              VALUES (:review_id, :service_id)";
                    $pdo_conn = $this->getPDOConn();
                    $stmt = $pdo_conn->prepare($query);
                    $stmt->execute([
                        "review_id" => $review_id,
                        "service_id" => $service_id
                    ]);
                }
            } catch (Exception $exception) {
                continue;
            }
        }
    }

    /**
     * Создать объект review на основе информации, переданной пользователем 
     * через веб-интерфейс.
     */
    private function _create(
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
}
