<?php
namespace Couch\Util;

abstract class Util
{
    public static function getSkip($offset, $limit) {
        $page = ($offset / $limit) + 1;
        $skip = $page * $limit;
        return $skip;
    }
}
