<?php

namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestCheckerService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Перевірка, що всі обов’язкові поля прийшли
     * 
     * @throws Exception
     */
    public function check(mixed $content, array $fields): bool
    {
        if (!isset($content)) {
            throw new BadRequestException('Empty content', Response::HTTP_BAD_REQUEST);
        }

        $errors = '';

        foreach ($fields as $field) {
            if (!isset($content[$field])) {
                $errors .= ' ' . $field . ';';
            }
        }

        if ($errors) {
            throw new BadRequestException(
                'Required fields are missed: ' . $errors,
                Response::HTTP_BAD_REQUEST
            );
        }

        return true;
    }

    /**
     * Валідація об’єкта або масиву за constraints Entity
     */
    public function validateRequestDataByConstraints(
        array|object $data,
        ?array $constraints = null,
        ?bool $removeSquareBracketFromPropertyPath = false
    ): void {
        $errors = $this->validator->validate(
            $data,
            !empty($constraints) ? new Collection($constraints) : null
        );

        if (count($errors) === 0) {
            return;
        }

        $validationErrors = [];

        foreach ($errors as $error) {
            $key = str_replace(['[', ']'], ['', ''], $error->getPropertyPath());

            if ($removeSquareBracketFromPropertyPath) {
                $key = preg_replace('/\[.*?\]/', '', $error->getPropertyPath());
            }

            $validationErrors[$key] = $error->getMessage();
        }

        throw new UnprocessableEntityHttpException(json_encode($validationErrors));
    }
}
