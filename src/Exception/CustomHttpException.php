<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomHttpException extends HttpException
{
    public function __construct(string $message = 'An error occurred', int $statusCode = 400)
    {
        parent::__construct($statusCode, $message);
    }
}