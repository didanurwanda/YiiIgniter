<?php

class CException {

    public function __construct($message = '', $status_code = 500) {
        show_error($message, $status_code);
    }

}