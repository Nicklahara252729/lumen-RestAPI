<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public function report()
    {
        // Lakukan sesuatu jika perlu melaporkan pengecualian
    }

    public function render($request)
    {
        // return response()->view('errors.custom', [], 500);
        // Gantilah 'errors.custom' dengan tampilan yang sesuai
    }
}