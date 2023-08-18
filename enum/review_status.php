<?php

/**
 * Хранение возможных констант из таблицы review_status БД.
 */
enum ReviewStatus: string
{
    case NotChecked = "Не проверен";
    case Suspended = "Приостановлен";
    case Published = "Опубликован";
}
