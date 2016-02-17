<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Hospitalisation\Controller\Hospitalisation' => 'Hospitalisation\Controller\HospitalisationController',
				),
		),
		'router' => array(
				'routes' => array(
						'hospitalisation' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/hospitalisation[/][:action][/:id]',
										'constraints' => array(
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id'     => '[0-9]+',
										),
										'defaults' => array(
												'controller' => 'Hospitalisation\Controller\Hospitalisation',
												'action'     => 'liste',
										),
								),
						),
				),
		),
		'view_manager' => array(
				'template_map' => array (
						'layout/hospitalisation' => __DIR__ . '/../view/layout/hospitalisation.phtml',
						'layout/menugauchehospi' => __DIR__ . '/../view/layout/menugauche.phtml',
						'layout/piedpagehospi' => __DIR__ . '/../view/layout/piedpage.phtml'
				),
				'template_path_stack' => array(
						'hospitalisation' => __DIR__ . '/../view',
				),
		),
);