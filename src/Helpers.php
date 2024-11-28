<?php

use RKWP\Utils\ObjectIterable;

if (!function_exists('objectIterable')) {
    function objectIterable($arr, $caseInsensitive = true): array|null|ObjectIterable
    {
        if (is_bool($arr)) {
            return new ObjectIterable([]);
        }
        return new ObjectIterable($arr, $caseInsensitive);
    }
}
