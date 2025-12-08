<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationList;
use Throwable;

class RuntimeConstraintExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $code = $this->getCode($exception);
        $errors = $this->getErrors($exception);

        $event->setResponse(new JsonResponse([
            "data" => [
                "code"   => $code,
                "errors" => $errors
            ]
        ], $code));
    }

    private function getCode(Throwable $exception): int
    {
        if (method_exists($exception, "getStatusCode")) {
            return array_key_exists($exception->getStatusCode(), Response::$statusTexts)
                ? $exception->getStatusCode()
                : Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        return array_key_exists($exception->getCode(), Response::$statusTexts)
            ? $exception->getCode()
            : Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    private function getErrors(Throwable $exception): array
    {
        $errors = [];

        // Якщо помилки ConstraintViolationList
        if (method_exists($exception, "getConstraintViolationList")) {
            return $this->buildErrorsFromList($exception->getConstraintViolationList());
        }

        // Якщо exception повертає JSON
        if ($tmpErrors = json_decode($exception->getMessage(), true)) {
            return $this->mapAssociativeErrors($tmpErrors["data"]["errors"] ?? $tmpErrors);
        }

        // Якщо звичайне повідомлення
        return [$exception->getMessage()];
    }

    private function mapAssociativeErrors(array $tmpErrors): array
    {
        $errors = [];

        foreach ($tmpErrors as $key => $error) {
            $errors[$key] = is_array($error)
                ? $this->mapAssociativeErrors($error)
                : $error;
        }

        return $errors;
    }

    private function buildErrorsFromList(ConstraintViolationList $list): array
    {
        $errors = [];

        foreach ($list as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }
}
