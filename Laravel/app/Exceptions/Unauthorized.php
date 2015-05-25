<?php

namespace App\Exceptions;


class Unauthorized extends UserException
{
    public function getDefaultMessage()
    {
        return "Unauthorized!";
    }

    function getHTTPCode()
    {
        return 401;
    }
}