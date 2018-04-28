<?php

namespace Auth;

return array(
	'controllers' => array(
 		'invokables' => array(
			'Auth\Controller\Index' => 'Auth\Controller\IndexController',
		),
	),
	// The following section is new and should be added to your file
	'router' => array(
		'routes' => array(
			'auth' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/auth[/:action][/:id]',
					'constraints' => array(
 						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
					),
					'defaults' => array(
						'controller' => 'Auth\Controller\Index',
 						'action'     => 'index',
					),
				),
			),
		),
	),
	'view_manager' => array(
		/*'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layoutAuth.phtml',
        ),*/
		'template_path_stack' => array(
			'auth' => __DIR__ . '/../view',
		),
	),
 );