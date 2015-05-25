<?php

namespace App\Exceptions;


class AccessDenied extends UserException
{
    public function getDefaultMessage()
    {
        return "Access Denied!";
    }

    function getHTTPCode()
    {
        return 403;
    }
}