<?php

namespace Articles;

return array(
	'controllers' => array(
 		'invokables' => array(
			'Articles\Controller\Index' => 'Articles\Controller\IndexController',
		),
	),
	// The following section is new and should be added to your file
	/*'router' => array(
		'routes' => array(
			'articles' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/articles/:controller[/:action][/:id]',
					'constraints' => array(
						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
 						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'Articles\Controller\Index',
 						'action'     => 'index',
					),
				),
			),
		),
	),*/

	'router' => array(
         'routes' => array(
             'articles' => array(
                 'type' => 'literal',
                 'options' => array(
                     'route'    => '/articles',
                     'defaults' => array(
                         'controller' => 'Articles\Controller\Index',
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
                             	'controller' => 'Articles\Controller\Index',
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
			'articles' => __DIR__ . '/../view',
		),
	),
 );