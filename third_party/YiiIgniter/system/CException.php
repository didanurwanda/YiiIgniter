<?php

class CException {

    public function __construct($message = '', $data = array(), $status_code = 500, $heading = '') {
        $search = array();
        $replace = array();
        foreach ($data as $key => $value) {
            $search[] = $key;
            $replace[] = $value;
        }
        $message = str_replace($search, $replace, $message);
        show_error($message, $status_code, $heading);
    }

}