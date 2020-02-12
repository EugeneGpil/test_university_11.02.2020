<?php

namespace App\Helpers\NumericHelper;

function getInt($val)
{
    if (is_numeric($val)) {
        return (int) round($val + 0);
    }
    return 0;
}
