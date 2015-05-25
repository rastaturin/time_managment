<?php

namespace App\Exceptions;


class BadRequest extends UserException
{
    public function getDefaultMessage()
    {
        return "Bad request";
    }

    function getHTTPCode()
    {
        return 400;
    }
}