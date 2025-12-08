<?php

namespace App\Service;

class ValidationService
{
    public function validateNotEmpty(array $fields)
    {
        foreach ($fields as $key => $value) {
            if (empty($value)) {
                throw new \Exception("Поле '$key' не може бути порожнім");
            }
        }
    }

    public function validateEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Некоректний email");
        }
    }

    public function validateDateRange(string $start, string $end)
    {
        if (strtotime($start) >= strtotime($end)) {
            throw new \Exception("Дата заїзду повинна бути раніше дати виїзду");
        }
    }
}
