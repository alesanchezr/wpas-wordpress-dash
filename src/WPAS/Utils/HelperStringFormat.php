<?php
namespace WPAS\Utils;

use \Exception;

class HelperStringFormat {

    public static function sprintf($format, array $args = array()) {
        $arg_nums = array_slice(array_flip(array_keys(array(0 => 0) + $args)), 1);

        for ($pos = 0; preg_match('/(?<=%)\(([a-zA-Z_][\w\s]*)\)/', $format, $match, PREG_OFFSET_CAPTURE, $pos);) {
            $arg_pos = $match[0][1];
            $arg_len = strlen($match[0][0]);
            $arg_key = $match[1][0];

            if (! array_key_exists($arg_key, $arg_nums)) {
                throw new Exception("sprintfn(): Missing argument '${arg_key}'");
            }
            $format = substr_replace($format, $replace = $arg_nums[$arg_key] . '$', $arg_pos, $arg_len);
            $pos = $arg_pos + strlen($replace); // skip to end of replacement for next iteration
        }

        return vsprintf($format, array_values($args));
    }
}