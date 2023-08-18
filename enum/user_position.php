<?php

/**
 * Хранение возможных констант из таблицы user_position БД.
 */
enum UserPosition: string
{
    case Guest = "Гость";
    case Customer = "Клиент";
    case Admin = "Администратор";
}
