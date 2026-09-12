<?php

namespace App\Foundation\Bootstrap;

use Illuminate\Foundation\Bootstrap\HandleExceptions as BaseHandleExceptions;
use Illuminate\Contracts\Foundation\Application;

/**
 * Laravel 7 + PHP 8.2: ignore deprecation notices that otherwise become fatal.
 */
class HandleExceptions extends BaseHandleExceptions
{
    public function bootstrap(Application $app)
    {
        parent::bootstrap($app);

        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
    }
}
