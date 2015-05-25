<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 24.05.2015
 * Time: 21:58
 */

namespace App\Http;


use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;

class MyInput extends Input
{

    /**
     * @return array
     */
    public static function all()
    {
        return Request::isJson() ? Input::json()->all() : Input::all();
    }

//    public static function get($key = NULL, $default = NULL)
//    {
//        self::checkParams();
//        return isset(self::$params[$key]) ? self::$params[$key] : $default;
//    }


}