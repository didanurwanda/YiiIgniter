<?php

abstract class YiiIgniter {

    public $charset = 'utf-8';
    private static $_app;

    public static function app() {
        return self::$_app;
    }

    public static function initialize() {
        if (defined(ENVIRONMENT) && ENVIRONMENT == 'development') {
            define('YII_DEBUG', true);
        } else {
            define('YII_DEBUG', false);
        }

        if (self::$_app == null) {
            self::import('system.*');
            self::import('system.helpers.*');
            self::$_app = new Yii;
            self::$_app->registerClass();
            self::$_app->charset = get_instance()->config->item('charset');
            self::$_app->baseUrl = $_SERVER['REQUEST_URI'];
            self::$_app->basePath = realpath(dirname(__FILE__));
        }
    }

    private function registerClass() {
        foreach (self::$_YiiIgniterObject as $key => $val) {
            $this->$val = new $key;
            if (method_exists($this->$val, 'init')) {
                $this->$val->init();
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

    public static function import($alias) {
        $path = self::getPathOfAlias($alias);
        if (self::getEndPathOfAlias($path) == '*') {
            $path = str_replace('/*', '', $path);
            foreach (scandir($path, SCANDIR_SORT_DESCENDING) as $key => $val) {
                if ($val != '.' && $val != '..' && pathinfo($val, PATHINFO_EXTENSION) == 'php') {
                    require_once $path . DIRECTORY_SEPARATOR . $val;
                }
            }
        } else {
            if (file_exists($path . EXT)) {
                require_once $path . EXT;
            } else {
                show_error("Unable to load the requested file: " . str_replace('/', '\\', $path . EXT));
            }
        }
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

    public static $_YiiIgniterObject = array(
        'CWidget' => 'widget',
        'CAssetManager' => 'assetManager',
        'CClientScript' => 'clientScript'
    );

}

class Yii extends YiiIgniter {
    
}