<?php

class CJSON {

    public static function encode($value, $opt = 0) {
        return json_encode($value, $opt);
    }

    public static function nameValue($name, $value) {
        return self::encode(strval($name)) . ' : ' . self::encode($value);
    }

    public static function decode($json, $assoc = false, $depth = 512, $options = 0) {
        return json_decode($json, $assoc, $depth, $options);
    }

}