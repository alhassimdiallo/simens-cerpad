<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Archivage\Controller\Archivage' => 'Archivage\Controller\ArchivageController'
				)
		),
		'router' => array (
				'routes' => array (
						'archivage' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/archivage[/][:action][/:id][/:val]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'val' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Archivage\Controller\Archivage',
												'action' => 'liste-dossiers-patients'
										)
								)
						)
				)
		),
		'view_manager' => array (
				'template_map' => array (
						'layout/archivage' => __DIR__ . '/../view/layout/archivage.phtml',
						'layout/menugaucheArchivage' => __DIR__ . '/../view/layout/menugaucheArch.phtml',
						'layout/piedpageArchivage' => __DIR__ . '/../view/layout/piedpageArch.phtml'
				),
				'template_path_stack' => array (
						'archivage' => __DIR__ . '/../view'
				),
				'strategies' => array(
						'ViewJsonStrategy',
				),
		)
);