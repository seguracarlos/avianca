<?php

namespace Restful;

return array(
    'controllers' => array(
        'invokables' => array(
            'Restful\Controller\LoginRest'    => 'Restful\Controller\LoginRestController',
            'Restful\Controller\ArticlesRest' => 'Restful\Controller\ArticlesRestController',
            'Restful\Controller\CodeQrRest'   => 'Restful\Controller\CodeQrRestController',
            'Restful\Controller\UsersRest'    => 'Restful\Controller\UsersRestController',
            'Restful\Controller\PetsRest'    => 'Restful\Controller\PetsRestController',
        ),
    ),
 
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'restful' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/api',
                    'defaults' => array(
                        'controller' => 'Restful\Controller\LoginRest',
                    ),
                ),

                'may_terminate' => true,

                'child_routes' => array(

                	'auth' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/auth',
                            'defaults'  =>  array(
                            	'__NAMESPACE__' => 'Restful\Controller',
		                        'controller'    => 'LoginRest',
                                'action'        => 'auth'
                            ),
                            /*'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'LoginRest',
                                'action'     => 'index'
                            ),*/
                        ),
                    ),

                    // ARTICULOS
                	'articles' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/articles[/:action][/:id]',
                            'defaults'  =>  array(
                                '__NAMESPACE__' => 'Restful\Controller',
                                'controller'    => 'ArticlesRest',
                                'action'        => 'index'
                            ),
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ),
                        ),
                    ),

                    // MASCOTAS
                	'pets' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/pets[/:action][/:id]',
                            'defaults'  =>  array(
                                '__NAMESPACE__' => 'Restful\Controller',
                                'controller'    => 'PetsRest',
                                'action'        => 'index'
                            ),
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ),
                        ),
                    ),

                    'qr' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/qr[/:action][/:id]',
                            'defaults'  =>  array(
                                '__NAMESPACE__' => 'Restful\Controller',
                                'controller'    => 'CodeQrRest',
                                'action'        => 'homecodeqr'
                            ),
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ),
                        ),
                    ),

                    'users' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/users[/:action][/:id]',
                            'defaults'  =>  array(
                                '__NAMESPACE__' => 'Restful\Controller',
                                'controller'    => 'UsersRest',
                                'action'        => 'index'
                            ),
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ),
                        ),
                    ),

                ),

            ),
        ),
    )

);