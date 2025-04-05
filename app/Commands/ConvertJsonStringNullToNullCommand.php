<?php

namespace App\Commands;

class ConvertJsonStringNullToNullCommand
 {
    public static function execute(array $data)
    {
        foreach ($data as $key => &$value) {
            if (is_array($value) || is_object($value)) {
                // Rekursi untuk array atau object
                $value = self::execute($value);
            } elseif ($value === "null") {
                // Jika value adalah string "null", ubah jadi null
                $value = null;
            }
        }

        return $data;
    }
 }
