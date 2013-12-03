YiiIgniter
==========

Run Yii Extensions on CodeIgniter

> *Dalam Tahap Pengembangan*

#### Example
```php
<?php $this->widget('zii.widgets.jui.CJuiButton', array(
	'buttonType'=>'submit',
	'name'=>'btnSubmit',
	'value'=>'1',
	'caption'=>'Submit form',
	'htmlOptions'=>array('class'=>'ui-button-primary')
)); ?>
```

#### Install
* Download YiiIgniter [YiiIgniter-master.zip](https://github.com/didanurwanda/YiiIgniter/archive/master.zip)
* Extract YiiIgniter-master.zip 
* Copy folder core and third_party to your project/application
* open your view or layout 
* add {POS_HEAD} to after tag &lt;/title&gt;
* add {POS_END} to before tag &lt;/body&gt;