<?php

/**
 * Хранение констант для обработки желаемого поведения класса DBHandler.
 */
enum LimitId: string
{
    case Max = "max";
    case Min = "min";
}
