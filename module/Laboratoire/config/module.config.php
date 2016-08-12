<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Laboratoire\Controller\Laboratoire' => 'Laboratoire\Controller\LaboratoireController'
				)
		),
		'router' => array (
				'routes' => array (
						'laboratoire' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/laboratoire[/][:action][/:id][/:val]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'val' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Laboratoire\Controller\Laboratoire',
												'action' => 'liste-patient'
										)
								)
						)
				)
		),
		'view_manager' => array (
				'template_map' => array (
						//'layout/laboratoire' => __DIR__ .'/../view/layout/laboratoire.phtml',
						//'layout/menugauche' => __DIR__ .'/../view/layout/menugauche.phtml',
						'layout/piedpage' => __DIR__ .'/../view/layout/piedpage.phtml'
				),
				/**********************************/
				'template_path_stack' => array (
						'laboratoire' => __DIR__ .'/../view'
				),
				'strategies' => array(
						'ViewJsonStrategy',
				),
		)
);