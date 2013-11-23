<?php

require_once dirname(__FILE__) . '/../third_party/YiiIgniter/YiiIgniter.php';

class MY_Loader extends CI_Loader {
    public function __construct() {
        parent::__construct();
        Yii::initialize();
    }
    
    public function widget($alias = '', $data = []) {
        Yii::app()->widget->createWidget($alias, $data);
    }
    
    public function beginWidget($alias = '', $data = []) {
        return Yii::app()->widget->beginWidget($alias, $data);
    }
    
    public function end($alias = '') {
        Yii::app()->widget->end($alias);
    }
}