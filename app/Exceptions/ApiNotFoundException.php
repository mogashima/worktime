<?php

namespace App\Exceptions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Exception;

class ApiNotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = 'API endpoint not found.2')
    {
        parent::__construct($message);
    }
}
