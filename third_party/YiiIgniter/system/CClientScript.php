<?php

class CClientScript {

    const POS_HEAD = 0;
    const POS_BEGIN = 1;
    const POS_END = 2;
    const POS_LOAD = 3;
    const POS_READY = 4;

    protected $scriptFileTop = array();
    protected $scriptFileBottom = array();
    protected $scriptTop = array();
    protected $scriptBottom = array();
    protected $cssFile = array();
    protected $css = array();
    protected $linkTag = '';
    protected $metaTag = '';
    public $coreScriptUrl = '';
    public $package = array();

    public function registerScriptFile($path, $position = self::POS_HEAD, $htmlOptions = array()) {
        if ($position == self::POS_END || $position == self::POS_LOAD) {
            $this->scriptFileBottom[] = CHtml::scriptFile($path, $htmlOptions);
        } elseif ($position == self::POS_BEGIN) {
            echo CHtml::scriptFile($path, $htmlOptions);
        } else {
            $this->scriptFileTop[] = CHtml::scriptFile($path, $htmlOptions);
        }
        return $this;
    }

    public function registerCssFile($url, $media = '') {
        $this->cssFile[] = CHtml::cssFile($url, $media);
        return $this;
    }

    public function registerCss($id, $css) {
        $this->css[$id] = $css;
        return $this;
    }

    public function registerScript($id, $script, $position = self::POS_END, $htmlOptions = array()) {
        if ($position == self::POS_END || $position == self::POS_LOAD) {
            $this->scriptBottom[$id] = $script;
        } elseif ($position == self::POS_BEGIN) {
            echo CHtml::script($script, $htmlOptions);
        } else {
            $this->scriptTop[$id] = $script;
        }
        return $this;
    }

    public function registerLinkTag($relation = null, $type = null, $href = null, $media = null, $options = null) {
        $this->linkTag .= CHtml::linkTag($relation, $type, $href, $media, $options);
        return $this;
    }

    public function registerMetaTag($content, $name = null, $httpEquiv = null, $options = array(), $id = '') {
        $this->metaTag .= CHtml::metaTag($content, $name, $httpEquiv, $options);
        return $this;
    }

    protected function render($value = array()) {
        $return = '';
        foreach ($value as $key => $val) {
            $return .= $val;
        }
        return $return;
    }

    protected function renderScript($pos) {
        $script = $this->render($pos);
        return $script !== '' ? CHtml::script($script) : '';
    }

    protected function renderScriptFile($pos) {
        return $this->render(array_unique($pos));
    }

    protected function renderCss() {
        $style = $this->render($this->css);
        return $style !== '' ? CHtml::css($style) : '';
    }

    protected function renderCssFile() {
        $return = $this->render(array_unique($this->cssFile));
        return $return;
    }

    public function posHead() {
        return $this->metaTag .
                $this->linkTag .
                $this->renderCssFile() .
                $this->renderCss() .
                $this->renderScriptFile($this->scriptFileTop) .
                $this->renderScript($this->scriptTop);
    }

    public function posEnd() {
        return $this->renderScriptFile($this->scriptFileBottom) .
                $this->renderScript($this->scriptBottom);
    }

    public function getCoreScriptUrl() {
        return $this->coreScriptUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . '/js/source');
    }

    public function registerCoreScript($name) {
        $this->package = include_once dirname(__FILE__) . '/js/packages.php';
        if (isset($this->package[$name])) {
            if (isset($this->package[$name]['depends'])) {
                foreach ($this->package[$name]['depends'] as $key => $value) {
                    $this->renderCoreScript($value);
                }
            }
            $this->renderCoreScript($name);
        }
        return $this;
    }

    protected function renderCoreScript($name) {
        foreach ($this->package[$name] as $key => $packages) {
            if ($key == 'js') {
                $path = $this->getCoreScriptUrl() . '/' . $packages[0];
                $this->registerScriptFile($path, self::POS_HEAD);
            }
        }
    }

}