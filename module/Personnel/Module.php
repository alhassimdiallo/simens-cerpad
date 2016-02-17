<?php
namespace Personnel;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Personnel\Model\Service;
use Personnel\Model\ServiceTable;
use Personnel\Model\HopitalTable;
use Personnel\Model\Hopital;
use Personnel\Model\PersonnelTable;
use Personnel\Model\Personnel;
use Personnel\Model\MedecinTable;
use Personnel\Model\Medecin;
use Personnel\Model\MedicotechniqueTable;
use Personnel\Model\Medicotechnique;
use Personnel\Model\LogistiqueTable;
use Personnel\Model\Logistique;
use Personnel\Model\AffectationTable;
use Personnel\Model\Affectation;
use Personnel\Model\TypepersonnelTable;
use Personnel\Model\Typepersonnel;
use Personnel\Model\TransfertTable;
use Personnel\Model\Transfert2;
use Personnel\Model\HopitalServiceTable;
use Personnel\Model\HopitalService;
use Personnel\Model\InterventionTable;
use Personnel\Model\Intervention2;

class Module
{
// 	public function onBootstrap(MvcEvent $e)
// 	{
// 		$eventManager        = $e->getApplication()->getEventManager();
// 		$moduleRouteListener = new ModuleRouteListener();
// 		$moduleRouteListener->attach($eventManager);
// 	}

	public function getAutoloaderConfig()
	{
		return array(
// 				'Zend\Loader\ClassMapAutoloader' => array(
// 						__DIR__ . '/autoload_classmap.php',
// 				),
				'Zend\Loader\StandardAutoloader' => array(
						'namespaces' => array(
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
						),
				),
		);
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getServiceConfig()
	{
		return array (
				'factories' => array (
						'Personnel\Model\ServiceTable' => function ($sm) {
							$tableGateway = $sm->get('ServiceTableGateway');
							$table = new ServiceTable($tableGateway);
							return $table;
						},
						'ServiceTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Service());
							return new TableGateway('service', $dbAdapter, null, $resultSetPrototype);
						},
						'Personnel\Model\HopitalTable' => function ($sm) {
							$tableGateway = $sm->get('HopitalTableGateway');
							$table = new HopitalTable($tableGateway);
							return $table;
						},
						'HopitalTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Hopital());
							return new TableGateway('hopital', $dbAdapter, null, $resultSetPrototype);
						},
						'Personnel\Model\PersonnelTable' => function ($sm) {
							$tableGateway = $sm->get('PersonnelTableGateway');
							$table = new PersonnelTable($tableGateway);
							return $table;
						},
						'PersonnelTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Personnel());
							return new TableGateway('personnel2', $dbAdapter, null, $resultSetPrototype);
						},
						'Personnel\Model\MedecinTable' => function ($sm) {
							$tableGateway = $sm->get('MedecinTableGateway');
							$table = new MedecinTable($tableGateway);
							return $table;
						},
						'MedecinTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Medecin());
							return new TableGateway('personnel_medecin2', $dbAdapter, null, $resultSetPrototype);
						},
						'Personnel\Model\MedicotechniqueTable' => function ($sm) {
							$tableGateway = $sm->get('MedicotechniqueTableGateway');
							$table = new MedicotechniqueTable($tableGateway);
							return $table;
						},
						'MedicotechniqueTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Medicotechnique());
							return new TableGateway('personnel_medico_technique2', $dbAdapter, null, $resultSetPrototype);
						},
						'Personnel\Model\LogistiqueTable' => function ($sm) {
							$tableGateway = $sm->get('LogistiqueTableGateway');
							$table = new LogistiqueTable($tableGateway);
							return $table;
						},
						'LogistiqueTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Logistique());
							return new TableGateway('personnel_logistique2', $dbAdapter, null, $resultSetPrototype);
						},
						'Personnel\Model\AffectationTable' => function ($sm) {
							$tableGateway = $sm->get('AffectationTableGateway');
							$table = new AffectationTable($tableGateway);
							return $table;
						},
						'AffectationTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Affectation());
							return new TableGateway('affecter', $dbAdapter, null, $resultSetPrototype);
						},
						'Personnel\Model\TypepersonnelTable' => function ($sm) {
							$tableGateway = $sm->get('TypepersonnelTableGateway');
							$table = new TypepersonnelTable($tableGateway);
							return $table;
						},
						'TypepersonnelTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Typepersonnel());
							return new TableGateway('type_personnel', $dbAdapter, null, $resultSetPrototype);
						},
						'Personnel\Model\TransfertTable' => function ($sm) {
							$tableGateway = $sm->get('TransfertTableGateway');
							$table = new TransfertTable($tableGateway);
							return $table;
						},
						'TransfertTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Transfert2());
							return new TableGateway('transfert_service_personnel', $dbAdapter, null, $resultSetPrototype);
						},
						'Personnel\Model\HopitalServiceTable' => function ($sm) {
							$tableGateway = $sm->get('HopitalServiceTableGateway');
							$table = new HopitalServiceTable($tableGateway);
							return $table;
						},
						'HopitalServiceTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new HopitalService());
							return new TableGateway('hopital_service', $dbAdapter, null, $resultSetPrototype);
						},
						'Personnel\Model\InterventionTable' => function ($sm) {
							$tableGateway = $sm->get('InterventionTableGateway');
							$table = new InterventionTable($tableGateway);
							return $table;
						},
						'InterventionTableGateway' => function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Intervention2());
							return new TableGateway('intervention', $dbAdapter, null, $resultSetPrototype);
						}
						
				)
		);
	}

}