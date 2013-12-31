<?php

return array(
    'basePath' => realpath(dirname(__FILE__)),
    'baseUrl' => get_instance()->config->base_url(),
    'charset' => get_instance()->config->item('charset'),
    
    'import' => array(
        
    ),
    
    // application components
    'components' => array(
    ),
    
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
    )
);