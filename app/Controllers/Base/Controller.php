<?php

namespace App\Controllers\Base;

abstract class Controller
{
    protected static function getValidId($val)
    {
        if (is_numeric($val) && (int) $val > 0) {
            return (int) round($val + 0);
        }
        return null;
    }
}
