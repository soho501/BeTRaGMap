<?php

/**
 *  Module specific configuration for Lal module
 */
return array(
		    
    /*  Specify location of module controller  */

    'controllers' => array(
        'invokables' => array(
            'BTRG\Controller\Dashboard' => 'BTRG\Controller\DashboardController',
        ),
    ),

    	
	'router' => array(
		'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'BTRG\Controller\Dashboard',
                        'action'     => 'index',
                   ),
                ),
            ),
     	),
	),
		
	'console' => array(
				'router' => array(
				 	'routes' => array(
				 			'facebook' => array(
				 					'options' => array(
				 							'route'    => 'facebook --fetgroupchusers',
				 							'defaults' => array(
				 									'controller' =>  'BTRG\Controller\Dashboard',
				 									'action'     => 'index',
				 							),
				 					),
				 			),
				 	),
				),
		),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'                    => __DIR__ . '/../view/layout.phtml',
            'btrg/dashboard/index'    	 	   => __DIR__ . '/../view/dashboard/dashboard.phtml',    'error/404'                        => __DIR__ . '/../view/404.phtml',
            'error/index'                      => __DIR__ . '/../view/error.phtml',
         ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),                                                          
);

