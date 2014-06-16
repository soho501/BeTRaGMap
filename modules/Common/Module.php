<?php

namespace Common;

class Module {

    /*  This just ensures the Common namespace is accessible */
    
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array( __NAMESPACE__ => __DIR__ ,
                ),
            ),
        );
    }
}
