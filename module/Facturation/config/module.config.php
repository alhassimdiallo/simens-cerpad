<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Facturation\Controller\Facturation' => 'Facturation\Controller\FacturationController'
				)
		),
		'router' => array (
				'routes' => array (
						'facturation' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/facturation[/][:action][/:id][/:val]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'val' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Facturation\Controller\Facturation',
												'action' => 'liste-patient'
										)
								)
						)
				)
		),
		'view_manager' => array (
				'template_map' => array (
						'layout/facturation' => __DIR__ .'/../view/layout/facturation.phtml',
						'layout/menugauche' => __DIR__ .'/../view/layout/menugauche.phtml',
						'layout/piedpage' => __DIR__ .'/../view/layout/piedpage.phtml'
				),
				'template_path_stack' => array (
						'facturation' => __DIR__ .'/../view'
				),
				'strategies' => array(
						'ViewJsonStrategy',
				),
		)
);