<?php

/**
 *  Module specific configuration for MailHandler cosole application
 */

return array(
   'controllers' => array(
		'invokables' => array(
				'FacebookHandler\Controller\Facebook' => 'FacebookHandler\Controller\FacebookController',
				'FacebookHandler\Controller\FacebookRest' => 'FacebookHandler\Controller\FacebookRestController',
   		),
	),
   'view_manager' => array(
				'template_map' => array(
						'facebook-handler/facebook/fblogin'        => __DIR__ . '/../view/facebook/fblogin.phtml',
						'facebook-handler/facebook/fbloginredirect'  => __DIR__ . '/../view/facebook/fbloginredirect.phtml',
				),
		),
	
	'router' => array(
			'routes' => array(
				'facebook' => array(
					'type' => 'segment',
					'options' => array(
						'route' => '/facebook[/:action]',
						'constraints' => array(
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						),
						'defaults' => array(
								'controller' => 'FacebookHandler\Controller\Facebook',
								'action'     => 'fblogin',
						),
				),
			),
			
			'facebookapi' => array(
					'type'    => 'segment',
					'options' => array(
							'route'    => '/facebook/api/message[/:id]',
							'constraints' => array(
									'id'     => '[0-9]+',
							),
							'defaults' => array(
									'controller' => 'FacebookHandler\Controller\FacebookRest',
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
                    	'route'    => 'facebook --fetchmessages',
                    	'defaults' => array(
                    		'controller' =>  'FacebookHandler\Controller\Facebook',
                        	'action'     => 'fetch',
                   		 ),
               		 ),
            	),
			),
		),
  	 ),
    
    
);

