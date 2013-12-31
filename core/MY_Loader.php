<?php

require_once dirname(__FILE__) . '/../third_party/YiiIgniter/YiiIgniter.php';

class MY_Loader extends CI_Loader {

    public function __construct() {
        parent::__construct();
        Yii::initialize();
    }

    public function widget($alias = '', $data = array()) {
        Yii::app()->widget->createWidget($alias, $data);
    }

    public function beginWidget($alias = '', $data = array()) {
        return Yii::app()->widget->beginWidget($alias, $data);
    }

    public function endWidget($alias = '') {
        Yii::app()->widget->end($alias);
    }

    public function view($view, $vars = array(), $return = FALSE) {
        $view = parent::view($view, $vars, true);
        $search = array('{POS_HEAD}', '{POS_END}');
        $replace = array(Yii::app()->clientScript->posHead(), Yii::app()->clientScript->posEnd());
        $views = str_replace($search, $replace, $view);
        if ($return === true) {
            return $views;
        } else {
            echo $views;
        }
    }

}