<?php

namespace Pets;

return array(
	'controllers' => array(
 		'invokables' => array(
			'Pets\Controller\Index' => 'Pets\Controller\IndexController',
		),
	),
	// The following section is new and should be added to your file
	/*'router' => array(
		'routes' => array(
			'pets' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/pets/:controller[/:action][/:id]',
					'constraints' => array(
						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
 						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'Pets\Controller\Index',
 						'action'     => 'index',
					),
				),
			),
		),
	),*/

	'router' => array(
         'routes' => array(
             'pets' => array(
                 'type' => 'literal',
                 'options' => array(
                     'route'    => '/pets',
                     'defaults' => array(
                         'controller' => 'Pets\Controller\Index',
                         'action'     => 'index',
                     ),
                 ),
                 'may_terminate' => true,
                 'child_routes'  => array(
                     'detail' => array(
                         'type' => 'segment',
                         'options' => array(
                             'route'    => '[/:action][/:id]',
                             'defaults' => array(
                             	'controller' => 'Pets\Controller\Index',
                            	'action' => 'index'
                             ),
                             'constraints' => array(
                            	'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								//'id'     => '[0-9]+',
								'id'     => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                             )
                         )
                     )
                 )
             )
         )
	),



	'view_manager' => array(
		/*'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layoutAuth.phtml',
        ),*/
		'template_path_stack' => array(
			'pets' => __DIR__ . '/../view',
		),
	),
 );