<?php

/**
 *  Module specific configuration for Lal module
 */
return array(
		    
    /*  Specify location of module controller  */

    'controllers' => array(
        'invokables' => array(
            'BTRG\Controller\Facebook' => 'BTRG\Controller\FacebookController',
        	'BTRG\Controller\Dashboard' => 'BTRG\Controller\DashboardController',
        	'BTRG\Controller\Crowfunder' => 'BTRG\Controller\CrowfunderController',
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
				 							'route'    => 'facebook --fetchgroupusers',
				 							'defaults' => array(
				 									'controller' =>  'BTRG\Controller\Facebook',
				 									'action'     => 'index',
				 							),
				 					),
				 			),
				 			'growfunder' => array(
				 					'options' => array(
				 							'route'    => 'crowfunder --fetchgroupusers [--filter] [<url>]',
				 							'defaults' => array(
				 									'controller' =>  'BTRG\Controller\Crowfunder',
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

