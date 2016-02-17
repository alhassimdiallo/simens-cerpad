<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Pharmacie\Controller\Pharmacie' => 'Pharmacie\Controller\PharmacieController',
				),
		),
		'router' => array(
				'routes' => array(
						'pharmacie' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/pharmacie[/][:action][/:id]',
										'constraints' => array(
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id'     => '[0-9]+',
										),
										'defaults' => array(
												'controller' => 'Pharmacie\Controller\Pharmacie',
												'action'     => 'index',
										),
								),
						),
				),
		),
		'view_manager' => array(
				'template_map' => array (
						'layout/pharmacie' => __DIR__ . '/../view/layout/pharmacie.phtml',
						'layout/menugauche-pharmacie' => __DIR__ . '/../view/layout/menugauche.phtml',
						'layout/piedpage-pharmacie' => __DIR__ . '/../view/layout/piedpage.phtml'
				),
				'template_path_stack' => array(
						'pharmacie' => __DIR__ . '/../view',
				),
		),
);