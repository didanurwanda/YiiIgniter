<?php

class CWidget {

    public $_id;
    
    private static $_id_prefix = 'yw_';
    private static $_id_number = 0;
    private $_widget;

    protected function getId() {
        return $this->_id = self::$_id_prefix . self::$_id_number++;
    }

    public function registerWidgetData($data = []) {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $this->$key = $val;
            }
        }
    }

    public function createWidget($alias, $data = []) {
        $this->beginWidget($alias, $data);
        $this->end($alias);
    }

    public function beginWidget($alias, $data = []) {
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