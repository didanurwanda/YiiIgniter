<?php

abstract class CInputWidget extends CWidget {

    public $model;
    public $attribute;
    public $name;
    public $value;
    public $htmlOptions = array();

    protected function resolveNameID() {
        if ($this->name !== null)
            $name = $this->name;
        elseif (isset($this->htmlOptions['name']))
            $name = $this->htmlOptions['name'];
        //elseif ($this->hasModel())
        //  $name = CHtml::activeName($this->model, $this->attribute);
        else
            show_error(get_class($this) . ' must specify "model" and "attribute" or "name" property values.');
        if (($id = $this->getId(false)) === null) {
            if (isset($this->htmlOptions['id']))
                $id = $this->htmlOptions['id'];
        }

        return array($name, $id);
    }

    protected function hasModel() {
        $this; //return $this->model instanceof CModel && $this->attribute !== null;
    }

}