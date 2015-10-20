<?php
namespace Couch\Util;

abstract class Util
{
    public static function getSkip($offset, $limit) {
        $page = ($offset / $limit) + 1;
        $skip = $page * $limit;
        return $skip;
    }

    public static function quote($input) {
        return str_replace('"', '%22', $input);
    }

    public static function getArrayValue($key, array $array, $defaultValue = null) {
        return array_key_exists($key, $array)
            ? $array[$key] : $defaultValue;
    }
}
