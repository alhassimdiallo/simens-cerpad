<?php
namespace Hospitalisation;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Hospitalisation\Model\DemandehospitalisationTable;
use Hospitalisation\Model\Demandehospitalisation;
use Zend\Db\TableGateway\TableGateway;
use Hospitalisation\Model\PatientTable;
use Hospitalisation\Model\Patient;
use Hospitalisation\Model\BatimentTable;
use Hospitalisation\Model\Batiment;
use Hospitalisation\Model\HospitalisationTable;
use Hospitalisation\Model\Hospitalisation;
use Hospitalisation\Model\HospitalisationlitTable;
use Hospitalisation\Model\Hospitalisationlit;
use Hospitalisation\Model\LitTable;
use Hospitalisation\Model\Lit;
use Hospitalisation\Model\SalleTable;
use Hospitalisation\Model\Salle;
use Hospitalisation\Model\SoinhospitalisationTable;
use Hospitalisation\Model\Soinhospitalisation;
use Hospitalisation\Model\SoinsTable;
use Hospitalisation\Model\Soins;
use Hospitalisation\Model\DemandeTable;
use Hospitalisation\Model\Demande;
use Hospitalisation\Model\ExamenTable;
use Hospitalisation\Model\Examen;
use Hospitalisation\Model\ResultatExamenTable;
use Hospitalisation\Model\ResultatExamen;
use Hospitalisation\Model\ResultatVisitePreanesthesiqueTable;
use Hospitalisation\Model\ResultatVisitePreanesthesique;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
	public function registerJsonStrategy(MvcEvent $e)
	{
		$app          = $e->getTarget();
		$locator      = $app->getServiceManager();
		$view         = $locator->get('Zend\View\View');
		$jsonStrategy = $locator->get('ViewJsonStrategy');
	
		// Attach strategy, which is a listener aggregate, at high priority
		$view->getEventManager()->attach($jsonStrategy, 100);
	}
	
	public function getAutoloaderConfig()
	{
		return array(
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
						'Hospitalisation\Model\DemandehospitalisationTable' => function ($sm) {
							$tableGateway = $sm->get ( 'DemandehospitalisationHospiTableGateway' );
							$table = new DemandehospitalisationTable ( $tableGateway );
							return $table;
						},
						'DemandehospitalisationHospiTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Demandehospitalisation () );
							return new TableGateway ( 'demande_hospitalisation', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\PatientTable' => function ($sm) {
							$tableGateway = $sm->get ( 'PatientTableGateway' );
							$table = new PatientTable ( $tableGateway );
							return $table;
						},
						'PatientTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Patient () );
							return new TableGateway ( 'patient', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\BatimentTable' => function ($sm) {
							$tableGateway = $sm->get ( 'BatimentHospiTableGateway' );
							$table = new BatimentTable ( $tableGateway );
							return $table;
						},
						'BatimentHospiTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Batiment () );
							return new TableGateway ( 'batiment', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\HospitalisationTable' => function ($sm) {
							$tableGateway = $sm->get ( 'HospitalisationHospiTableGateway' );
							$table = new HospitalisationTable ( $tableGateway );
							return $table;
						},
						'HospitalisationHospiTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Hospitalisation() );
							return new TableGateway ( 'hospitalisation', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\HospitalisationlitTable' => function ($sm) {
							$tableGateway = $sm->get ( 'HospitalisationlitHospiTableGateway' );
							$table = new HospitalisationlitTable ( $tableGateway );
							return $table;
						},
						'HospitalisationlitHospiTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Hospitalisationlit() );
							return new TableGateway ( 'hospitalisation_lit', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\LitTable' => function ($sm) {
							$tableGateway = $sm->get ( 'LitHospiTableGateway' );
							$table = new LitTable ( $tableGateway );
							return $table;
						},
						'LitHospiTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Lit() );
							return new TableGateway ( 'lit', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\SalleTable' => function ($sm) {
							$tableGateway = $sm->get ( 'SalleHospiTableGateway' );
							$table = new SalleTable( $tableGateway );
							return $table;
						},
						'SalleHospiTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Salle() );
							return new TableGateway ( 'salle', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\SoinsTable' => function ($sm) {
							$tableGateway = $sm->get ( 'SoinsHospiTableGateway' );
							$table = new SoinsTable( $tableGateway );
							return $table;
						},
						'SoinsHospiTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Soins() );
							return new TableGateway ( 'soins', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\DemandeTable' => function ($sm) {
							$tableGateway = $sm->get ( 'DemandeTableGateway' );
							$table = new DemandeTable( $tableGateway );
							return $table;
						},
						'DemandeTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Demande() );
							return new TableGateway ( 'demande', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\ExamenTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ExamenTableGateway' );
							$table = new ExamenTable( $tableGateway );
							return $table;
						},
						'ExamenTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Examen() );
							return new TableGateway ( 'examens', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\ResultatExamenTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ResultatExamenHospiTableGateway' );
							$table = new ResultatExamenTable( $tableGateway );
							return $table;
						},
						'ResultatExamenHospiTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new ResultatExamen() );
							return new TableGateway ( 'resultats_examens', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\SoinhospitalisationTable' => function ($sm) {
							$tableGateway = $sm->get ( 'SoinhospitalisationHospiTableGateway' );
							$table = new SoinhospitalisationTable( $tableGateway );
							return $table;
						},
						'SoinhospitalisationHospiTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Soinhospitalisation() );
							return new TableGateway ( 'soins_hospitalisation', $dbAdapter, null, $resultSetPrototype );
						},
						'Hospitalisation\Model\ResultatVisitePreanesthesiqueTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ResultatVisitePreanesthesiqueTableGateway' );
							$table = new ResultatVisitePreanesthesiqueTable( $tableGateway );
							return $table;
						},
						'ResultatVisitePreanesthesiqueTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new ResultatVisitePreanesthesique() );
							return new TableGateway ( 'resultat_vpa', $dbAdapter, null, $resultSetPrototype );
						},
						
						)
				);
	}

}