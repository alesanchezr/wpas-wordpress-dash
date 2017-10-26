<?php
namespace WPAS\Utils;

use \Exception;

class HelperHTTP {

    //$file = 'http://www.domain.com/somefile.jpg';
    public static function url_exists($file) {
        $file_headers = @get_headers($file);
        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $exists = false;
        }
        else {
            $exists = true;
        }
        return $exists;
    }
}