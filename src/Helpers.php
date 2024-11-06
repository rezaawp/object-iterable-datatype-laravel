<?php

use RKWP\Utils\ObjectIterable;

if (!function_exists('objectIterable')) {
    function objectIterable($objOrArray): ObjectIterable | array | null
    {
        return new ObjectIterable($objOrArray);
    }
}
