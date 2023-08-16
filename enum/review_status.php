<?php
enum ReviewStatus: string
{
    case NotChecked = "Не проверен";
    case Suspended = "Приостановлен";
    case Published = "Опубликован";
}