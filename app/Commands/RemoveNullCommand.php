<?php

namespace App\Commands;


class RemoveNullCommand
{
    public static function execute(array $data, array $exceptKey=[]) : array
    {
        $original = $data;
        $filtered = array_filter($data, function ($value, $key) use ($exceptKey) {
            if (in_array($key, $exceptKey)) {
                return true;
            }
            return !is_null($value);
        }, ARRAY_FILTER_USE_BOTH);

        return $filtered;
    }
}
