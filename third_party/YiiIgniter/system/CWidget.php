<?php

class CWidget {

    public $id;
    private static $id_number = 0;
    private $_widget;

    public function init() {
        
    }

    public function getId($autoGenerate = true) {
        if ($this->id !== null) {
            return $this->id;
        } else {
            return $this->id = 'yw_' . self::$id_number++;
        }
    }

    public function setId($value) {
        $this->id = $value;
    }

    public function registerWidgetData($data = array()) {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $this->$key = $val;
            }
        }
    }

    public function createWidget($alias, $data = array()) {
        $this->beginWidget($alias, $data);
        $this->end($alias);
    }

    public function beginWidget($alias, $data = array()) {
        Yii::import($alias);
        $class = Yii::getEndPathOfAlias($alias, '.');
        $this->_widget = new $class;
        $this->_widget->registerWidgetData($data);
        if (method_exists($this->_widget, 'init')) {
            $this->_widget->init();
        }
        return $this->_widget;
    }

    public function end($alias = '') {
        $this->_widget->run();
        $this->_widget = null;
    }

}