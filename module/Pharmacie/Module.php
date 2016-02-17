<?php

namespace Pharmacie;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Pharmacie\Model\Consommable;
use Pharmacie\Model\ConsommableTable;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface {
	// public function onBootstrap(MvcEvent $e)
	// {
	// $eventManager = $e->getApplication()->getEventManager();
	// $moduleRouteListener = new ModuleRouteListener();
	// $moduleRouteListener->attach($eventManager);
	// }
	public function getAutoloaderConfig() {
		return array (
				// 'Zend\Loader\ClassMapAutoloader' => array(
				// __DIR__ . '/autoload_classmap.php',
				// ),
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
						)
				)
		);
	}
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	public function getServiceConfig() {
		return array (
				'factories' => array (
						'Pharmacie\Model\ConsommableTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ConsommableTableGateway' );
							$table = new ConsommableTable ( $tableGateway );
							return $table;
						},
						'ConsommableTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Consommable () );
							return new TableGateway ( 'consommable', $dbAdapter, null, $resultSetPrototype );
						}
				)
		);
	}
	public function getViewHelperConfig() {
		return array ();
	}
}