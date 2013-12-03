<?php

class CHtml {

    const ID_PREFIX = 'yt_';

    public static $errorSummaryCss = 'errorSummary';
    public static $errorMessageCss = 'errorMessage';
    public static $errorCss = 'error';
    public static $errorContainerTag = 'div';
    public static $requiredCss = 'required';
    public static $beforeRequiredLabel = '';
    public static $afterRequiredLabel = ' <span class="required">*</span>';
    public static $count = 0;
    public static $liveEvents = true;
    public static $closeSingleTags = true;
    public static $renderSpecialAttributesValue = true;
    private static $_modelNameConverter;

    public static function encode($text) {
        return htmlspecialchars($text, ENT_QUOTES, Yii::app()->charset);
    }

    public static function decode($text) {
        return htmlspecialchars_decode($text, ENT_QUOTES);
    }

    public static function encodeArray($data) {
        $d = array();
        foreach ($data as $key => $value) {
            if (is_string($key))
                $key = htmlspecialchars($key, ENT_QUOTES, Yii::app()->charset);
            if (is_string($value))
                $value = htmlspecialchars($value, ENT_QUOTES, Yii::app()->charset);
            elseif (is_array($value))
                $value = self::encodeArray($value);
            $d[$key] = $value;
        }
        return $d;
    }

    public static function tag($tag, $htmlOptions = array(), $content = false, $closeTag = true) {
        $html = '<' . $tag . self::renderAttributes($htmlOptions);
        if ($content === false)
            return $closeTag && self::$closeSingleTags ? $html . ' />' : $html . '>';
        else
            return $closeTag ? $html . '>' . $content . '</' . $tag . '>' : $html . '>' . $content;
    }

    public static function openTag($tag, $htmlOptions = array()) {
        return '<' . $tag . self::renderAttributes($htmlOptions) . '>';
    }

    public static function closeTag($tag) {
        return '</' . $tag . '>';
    }

    public static function getIdByName($name) {
        return str_replace(array('[]', '][', '[', ']', ' '), array('', '_', '_', '', '_'), $name);
    }

    public static function listOptions($selection, $listData, &$htmlOptions) {
        $raw = isset($htmlOptions['encode']) && !$htmlOptions['encode'];
        $content = '';
        if (isset($htmlOptions['prompt'])) {
            $content.='<option value="">' . strtr($htmlOptions['prompt'], array('<' => '&lt;', '>' => '&gt;')) . "</option>\n";
            unset($htmlOptions['prompt']);
        }
        if (isset($htmlOptions['empty'])) {
            if (!is_array($htmlOptions['empty']))
                $htmlOptions['empty'] = array('' => $htmlOptions['empty']);
            foreach ($htmlOptions['empty'] as $value => $label)
                $content.='<option value="' . self::encode($value) . '">' . strtr($label, array('<' => '&lt;', '>' => '&gt;')) . "</option>\n";
            unset($htmlOptions['empty']);
        }

        if (isset($htmlOptions['options'])) {
            $options = $htmlOptions['options'];
            unset($htmlOptions['options']);
        }
        else
            $options = array();

        $key = isset($htmlOptions['key']) ? $htmlOptions['key'] : 'primaryKey';
        if (is_array($selection)) {
            foreach ($selection as $i => $item) {
                if (is_object($item))
                    $selection[$i] = $item->$key;
            }
        }
        elseif (is_object($selection))
            $selection = $selection->$key;

        foreach ($listData as $key => $value) {
            if (is_array($value)) {
                $content.='<optgroup label="' . ($raw ? $key : self::encode($key)) . "\">\n";
                $dummy = array('options' => $options);
                if (isset($htmlOptions['encode']))
                    $dummy['encode'] = $htmlOptions['encode'];
                $content.=self::listOptions($selection, $value, $dummy);
                $content.='</optgroup>' . "\n";
            }
            else {
                $attributes = array('value' => (string) $key, 'encode' => !$raw);
                if (!is_array($selection) && !strcmp($key, $selection) || is_array($selection) && in_array($key, $selection))
                    $attributes['selected'] = 'selected';
                if (isset($options[$key]))
                    $attributes = array_merge($attributes, $options[$key]);
                $content.=self::tag('option', $attributes, $raw ? (string) $value : self::encode((string) $value)) . "\n";
            }
        }

        unset($htmlOptions['key']);

        return $content;
    }

    public static function renderAttributes($htmlOptions) {
        static $specialAttributes = array(
    'async' => 1,
    'autofocus' => 1,
    'autoplay' => 1,
    'checked' => 1,
    'controls' => 1,
    'declare' => 1,
    'default' => 1,
    'defer' => 1,
    'disabled' => 1,
    'formnovalidate' => 1,
    'hidden' => 1,
    'ismap' => 1,
    'loop' => 1,
    'multiple' => 1,
    'muted' => 1,
    'nohref' => 1,
    'noresize' => 1,
    'novalidate' => 1,
    'open' => 1,
    'readonly' => 1,
    'required' => 1,
    'reversed' => 1,
    'scoped' => 1,
    'seamless' => 1,
    'selected' => 1,
    'typemustmatch' => 1,
        );

        if ($htmlOptions === array())
            return '';

        $html = '';
        if (isset($htmlOptions['encode'])) {
            $raw = !$htmlOptions['encode'];
            unset($htmlOptions['encode']);
        }
        else
            $raw = false;

        foreach ($htmlOptions as $name => $value) {
            if (isset($specialAttributes[$name])) {
                if ($value) {
                    $html .= ' ' . $name;
                    if (self::$renderSpecialAttributesValue)
                        $html .= '="' . $name . '"';
                }
            }
            elseif ($value !== null)
                $html .= ' ' . $name . '="' . ($raw ? $value : self::encode($value)) . '"';
        }

        return $html;
    }

    public static function cdata($text) {
        return '<![CDATA[' . $text . ']]>';
    }

    public static function image($src, $alt = '', $htmlOptions = array()) {
        $htmlOptions['src'] = $src;
        $htmlOptions['alt'] = $alt;
        return self::tag('img', $htmlOptions);
    }

    public static function textField($name, $value = '', $htmlOptions = array()) {
        return self::inputField('text', $name, $value, $htmlOptions);
    }

    public static function fileField($name, $value = '', $htmlOptions = array()) {
        return self::inputField('file', $name, $value, $htmlOptions);
    }

    public static function textArea($name, $value = '', $htmlOptions = array()) {
        $htmlOptions['name'] = $name;
        if (!isset($htmlOptions['id']))
            $htmlOptions['id'] = self::getIdByName($name);
        elseif ($htmlOptions['id'] === false)
            unset($htmlOptions['id']);
        return self::tag('textarea', $htmlOptions, isset($htmlOptions['encode']) && !$htmlOptions['encode'] ? $value : self::encode($value));
    }

    public static function radioButton($name, $checked = false, $htmlOptions = array()) {
        if ($checked)
            $htmlOptions['checked'] = 'checked';
        else
            unset($htmlOptions['checked']);
        $value = isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;

        if (array_key_exists('uncheckValue', $htmlOptions)) {
            $uncheck = $htmlOptions['uncheckValue'];
            unset($htmlOptions['uncheckValue']);
        }
        else
            $uncheck = null;

        if ($uncheck !== null) {
            // add a hidden field so that if the radio button is not selected, it still submits a value
            if (isset($htmlOptions['id']) && $htmlOptions['id'] !== false)
                $uncheckOptions = array('id' => self::ID_PREFIX . $htmlOptions['id']);
            else
                $uncheckOptions = array('id' => false);
            $hidden = self::hiddenField($name, $uncheck, $uncheckOptions);
        }
        else
            $hidden = '';

        // add a hidden field so that if the radio button is not selected, it still submits a value
        return $hidden . self::inputField('radio', $name, $value, $htmlOptions);
    }

    protected static function inputField($type, $name, $value, $htmlOptions) {
        $htmlOptions['type'] = $type;
        $htmlOptions['value'] = $value;
        $htmlOptions['name'] = $name;
        if (!isset($htmlOptions['id']))
            $htmlOptions['id'] = self::getIdByName($name);
        elseif ($htmlOptions['id'] === false)
            unset($htmlOptions['id']);
        return self::tag('input', $htmlOptions);
    }

    public static function checkBox($name, $checked = false, $htmlOptions = array()) {
        if ($checked)
            $htmlOptions['checked'] = 'checked';
        else
            unset($htmlOptions['checked']);
        $value = isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;

        if (array_key_exists('uncheckValue', $htmlOptions)) {
            $uncheck = $htmlOptions['uncheckValue'];
            unset($htmlOptions['uncheckValue']);
        }
        else
            $uncheck = null;

        if ($uncheck !== null) {
            // add a hidden field so that if the check box is not checked, it still submits a value
            if (isset($htmlOptions['id']) && $htmlOptions['id'] !== false)
                $uncheckOptions = array('id' => self::ID_PREFIX . $htmlOptions['id']);
            else
                $uncheckOptions = array('id' => false);
            $hidden = self::hiddenField($name, $uncheck, $uncheckOptions);
        }
        else
            $hidden = '';

        // add a hidden field so that if the check box is not checked, it still submits a value
        return $hidden . self::inputField('checkbox', $name, $value, $htmlOptions);
    }

    public static function dropDownList($name, $select, $data, $htmlOptions = array()) {
        $htmlOptions['name'] = $name;

        if (!isset($htmlOptions['id']))
            $htmlOptions['id'] = self::getIdByName($name);
        elseif ($htmlOptions['id'] === false)
            unset($htmlOptions['id']);

        $options = "\n" . self::listOptions($select, $data, $htmlOptions);
        $hidden = '';

        if (!empty($htmlOptions['multiple'])) {
            if (substr($htmlOptions['name'], -2) !== '[]')
                $htmlOptions['name'].='[]';

            if (isset($htmlOptions['unselectValue'])) {
                $hiddenOptions = isset($htmlOptions['id']) ? array('id' => self::ID_PREFIX . $htmlOptions['id']) : array('id' => false);
                $hidden = self::hiddenField(substr($htmlOptions['name'], 0, -2), $htmlOptions['unselectValue'], $hiddenOptions);
                unset($htmlOptions['unselectValue']);
            }
        }
        // add a hidden field so that if the option is not selected, it still submits a value
        return $hidden . self::tag('select', $htmlOptions, $options);
    }

    public static function listBox($name, $select, $data, $htmlOptions = array()) {
        if (!isset($htmlOptions['size']))
            $htmlOptions['size'] = 4;
        if (!empty($htmlOptions['multiple'])) {
            if (substr($name, -2) !== '[]')
                $name.='[]';
        }
        return self::dropDownList($name, $select, $data, $htmlOptions);
    }

    public static function button($label = 'button', $htmlOptions = array()) {
        if (!isset($htmlOptions['name'])) {
            if (!array_key_exists('name', $htmlOptions))
                $htmlOptions['name'] = self::ID_PREFIX . self::$count++;
        }
        if (!isset($htmlOptions['type']))
            $htmlOptions['type'] = 'button';
        if (!isset($htmlOptions['value']) && $htmlOptions['type'] != 'image')
            $htmlOptions['value'] = $label;
        return self::tag('input', $htmlOptions);
    }

    public static function htmlButton($label = 'button', $htmlOptions = array()) {
        if (!isset($htmlOptions['name']))
            $htmlOptions['name'] = self::ID_PREFIX . self::$count++;
        if (!isset($htmlOptions['type']))
            $htmlOptions['type'] = 'button';
        return self::tag('button', $htmlOptions, $label);
    }

    public static function submitButton($label = 'submit', $htmlOptions = array()) {
        $htmlOptions['type'] = 'submit';
        return self::button($label, $htmlOptions);
    }

    public static function metaTag($content, $name = null, $httpEquiv = null, $options = array()) {
        if ($name !== null)
            $options['name'] = $name;
        if ($httpEquiv !== null)
            $options['http-equiv'] = $httpEquiv;
        $options['content'] = $content;
        return self::tag('meta', $options);
    }

    public static function linkTag($relation = null, $type = null, $href = null, $media = null, $options = array()) {
        if ($relation !== null)
            $options['rel'] = $relation;
        if ($type !== null)
            $options['type'] = $type;
        if ($href !== null)
            $options['href'] = $href;
        if ($media !== null)
            $options['media'] = $media;
        return self::tag('link', $options);
    }

    public static function css($text, $media = '') {
        if ($media !== '')
            $media = ' media="' . $media . '"';
        return "<style type=\"text/css\"{$media}>\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n</style>";
    }

    public static function refresh($seconds, $url = '') {
        $content = "$seconds";
        if ($url !== '')
            $content.=';url=' . self::normalizeUrl($url);
        Yii::app()->clientScript->registerMetaTag($content, null, 'refresh');
    }

    public static function cssFile($url, $media = '') {
        return CHtml::linkTag('stylesheet', 'text/css', $url, $media !== '' ? $media : null);
    }

    public static function script($text, array $htmlOptions = array()) {
        $defaultHtmlOptions = array(
            'type' => 'text/javascript',
        );
        $htmlOptions = array_merge($defaultHtmlOptions, $htmlOptions);
        return self::tag('script', $htmlOptions, "\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n");
    }

    public static function scriptFile($url, array $htmlOptions = array()) {
        $defaultHtmlOptions = array(
            'type' => 'text/javascript',
            'src' => $url
        );
        $htmlOptions = array_merge($defaultHtmlOptions, $htmlOptions);
        return self::tag('script', $htmlOptions, '');
    }

    public static function asset($path, $hashByName = false) {
        return Yii::app()->assetManager->publish($path, $hashByName);
    }

}