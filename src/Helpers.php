<?php

use RKWP\Utils\ObjectIterable;

if (!function_exists('objectIterable')) {
    function objectIterable($objOrArray)
    {
        if (gettype($objOrArray) == 'array') {
            $countable = count($objOrArray);
            return $countable ? new ObjectIterable($objOrArray) : $objOrArray;
        } elseif (gettype($objOrArray) == 'object') {
            return $objOrArray ? new ObjectIterable($objOrArray) : $objOrArray;
        }
    }
}
