<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

    /**
     * Return response
     * @param $data
     * @param int $code
     * @return Response
     */
    protected function response($data, $code = 200)
    {
        return response(json_encode($data), $code);
    }

}
