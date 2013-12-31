<?php

abstract class YiiIgniter {

    public $charset = 'utf-8';
    private static $_app;

    public static function app() {
        return self::$_app;
    }

    public static function initialize() {
        if (ENVIRONMENT === 'development') {
            define('YII_DEBUG', true);
        } else {
            define('YII_DEBUG', false);
        }

        if (self::$_app == null) {
            self::$_app = new Yii;
            self::$_app->registerClass();
            self::import('system.helpers.*');
            self::import('system.widgets.*');
            self::import('application.components.*');
            self::$_app->registerComponents();
            self::$_app->charset = self::getConfig('charset');
            self::$_app->baseUrl = self::getConfig('baseUrl');
            self::$_app->language = self::getConfig('language', 'en');
            self::$_app->basePath = self::getConfig('basePath');
        }
    }

    private function registerClass() {
        foreach (self::$_coreClasses as $key => $val) {
            // import core class
            self::import('system.' . $key);
            if ($val !== FALSE) {
                $this->$val = new $key;
                if (method_exists($this->$val, 'init')) {
                    $this->$val->init();
                }
            }
        }
    }

    private function registerComponents() {
        $components = self::getConfig('components', array());
        foreach ($components as $key => $val) {

            $class = self::getEndPathOfAlias($val['class'], '.');
            if (!class_exists($class)) {
                self::import($val['class']);
            }

            $this->$key = new $class;

            if (method_exists($this->$key, 'init')) {
                $this->$key->init();
            }

            unset($val['class']);
            foreach ($val as $var => $value) {
                $this->$key->$var = $value;
            }
        }
    }

    public static function getPathOfAlias($alias) {
        $alias = str_replace('.', '/', $alias);
        $oldpath = array('application/', 'ext/', 'sys/', 'zii/');
        $newpath = array('', 'extensions/', 'system/', 'system/zii/');
        return dirname(__FILE__) . '/' . str_replace($oldpath, $newpath, $alias);
    }

    public static function getEndPathOfAlias($alias, $divider = '/') {
        $ex = explode($divider, $alias);
        return $ex[count($ex) - 1];
    }

    public static function import($alias, $sort = SCANDIR_SORT_ASCENDING) {
        $path = self::getPathOfAlias($alias);
        if (self::getEndPathOfAlias($path) == '*') {
            $path = str_replace('/*', '', $path);
            foreach (scandir($path, $sort) as $key => $val) {
                if ($val != '.' && $val != '..' && pathinfo($val, PATHINFO_EXTENSION) == 'php') {
                    require_once $path . DIRECTORY_SEPARATOR . $val;
                }
            }
        } else {
            if (file_exists($path . EXT)) {
                require_once $path . EXT;
            } else {
                $path = str_replace('/', '\\', $path . EXT);
                throw new CException(Yii::t('yii', 'Unable to load the requested file: {path}', array('{path}' => $path)));
            }
        }
    }

    private static function getConfig($key, $value = FALSE) {
        $cfg = require_once dirname(__FILE__) . '/config.php';
        return isset($cfg[$key]) ? $cfg[$key] : $value;
    }

    public static function t($name, $message, $data) {
        $search = array();
        $replace = array();
        foreach ($data as $key => $value) {
            $search[] = $key;
            $replace[] = $value;
        }
        $message = str_replace($search, $replace, $message);
        return $message;
    }

    public function __call($name, $arguments) {
        if (substr($name, 0, 3) == 'get') {
            $method = strtolower(substr($name, 3, 1)) . substr($name, 4);
            return $this->$method;
        }
    }

    public static function getVersion() {
        return '1.0';
    }

    public static $_coreClasses = array(
        'CApplicationComponent' => FALSE,
        'CException' => FALSE,
        'CWidget' => 'widget',
        'CInputWidget' => FALSE,
        'CClientScript' => 'clientScript',
        'CAssetManager' => 'assetManager',
    );

}

class Yii extends YiiIgniter {
    
}