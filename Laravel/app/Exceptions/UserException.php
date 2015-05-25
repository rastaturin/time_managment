<?php

namespace App\Exceptions;


use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class UserException extends HttpException
{
    abstract function getHTTPCode();
    abstract function getDefaultMessage();

    public function __construct($message = null, \Exception $previous = null)
    {
        parent::__construct($this->getHTTPCode(), isset($message) ? $message : $this->getDefaultMessage(), $previous);
    }

}