<?php

namespace Archivage;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Archivage\Model\PatientTable;
use Archivage\Model\Patient;
use Archivage\Model\TarifConsultationTable;
use Archivage\Model\TarifConsultation;
use Archivage\Model\ConsultationTable;
use Archivage\Model\Consultation;
use Archivage\Model\ServiceTable;
use Archivage\Model\Service;
use Archivage\Model\DemandehospitalisationTable;
use Archivage\Model\Demandehospitalisation;
use Archivage\Model\BatimentTable;
use Archivage\Model\Batiment;
use Archivage\Model\HospitalisationTable;
use Archivage\Model\Hospitalisation;
use Archivage\Model\HospitalisationlitTable;
use Archivage\Model\Hospitalisationlit;
use Archivage\Model\LitTable;
use Archivage\Model\Lit;
use Archivage\Model\TransfererPatientServiceTable;
use Archivage\Model\TransfererPatientService;
use Archivage\Model\SalleTable;
use Archivage\Model\Salle;
use Archivage\Model\Soinhospitalisation3Table;
use Archivage\Model\Soinhospitalisation3;
use Archivage\Model\DemandeTable;
use Archivage\Model\Demande;
use Archivage\Model\ExamenTable;
use Archivage\Model\Examen;
use Archivage\Model\ResultatExamenTable;
use Archivage\Model\ResultatExamen;
use Archivage\Model\ResultatVisitePreanesthesiqueTable;
use Archivage\Model\ResultatVisitePreanesthesique;
use Archivage\Model\AdmissionTable;
use Archivage\Model\Admission;

class Module implements AutoloaderProviderInterface {
	
	public function getAutoloaderConfig() {
		return array (
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
						'Archivage\Model\PatientTable' => function ($sm) {
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
						'Archivage\Model\TarifConsultationTable' => function ($sm) {
							$tableGateway = $sm->get( 'TarifConsultationTableGateway' );
							$table = new TarifConsultationTable( $tableGateway );
							return $table;
						},
						'TarifConsultationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype (new TarifConsultation());
							return new TableGateway ( 'tarif_consultation', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\AdmissionTable' => function ($sm) {
							$tableGateway = $sm->get( 'AdmissionArchTableGateway' );
							$table = new AdmissionTable( $tableGateway );
							return $table;
						},
						'AdmissionArchTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Admission() );
							return new TableGateway ( 'admission', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\ConsultationTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ConsultationArchiveTableGateway' );
							$table = new ConsultationTable ( $tableGateway );
							return $table;
						},
						'ConsultationArchiveTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype ( new Consultation());
							return new TableGateway ( 'consultation', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\ServiceTable' => function ($sm) {
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
						'Archivage\Model\DemandehospitalisationTable' => function ($sm) {
							$tableGateway = $sm->get ( 'DemandehospitalisationArchiveTableGateway' );
							$table = new DemandehospitalisationTable ( $tableGateway );
							return $table;
						},
						'DemandehospitalisationArchiveTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Demandehospitalisation () );
							return new TableGateway ( 'demande_hospitalisation', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\BatimentTable' => function ($sm) {
							$tableGateway = $sm->get ( 'BatimentTableGateway' );
							$table = new BatimentTable ( $tableGateway );
							return $table;
						},
						'BatimentTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Batiment () );
							return new TableGateway ( 'batiment', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\HospitalisationTable' => function ($sm) {
							$tableGateway = $sm->get ( 'HospitalisationTableGateway' );
							$table = new HospitalisationTable ( $tableGateway );
							return $table;
						},
						'HospitalisationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Hospitalisation() );
							return new TableGateway ( 'hospitalisation', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\HospitalisationlitTable' => function ($sm) {
							$tableGateway = $sm->get ( 'HospitalisationlitTableGateway' );
							$table = new HospitalisationlitTable ( $tableGateway );
							return $table;
						},
						'HospitalisationlitTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Hospitalisationlit() );
							return new TableGateway ( 'hospitalisation_lit', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\LitTable' => function ($sm) {
							$tableGateway = $sm->get ( 'LitTableGateway' );
							$table = new LitTable ( $tableGateway );
							return $table;
						},
						'LitTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Lit() );
							return new TableGateway ( 'lit', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\TransfererPatientServiceTable' => function ($sm) {
							$tableGateway = $sm->get ( 'TransfererPatientServiceTableGateway' );
							$table = new TransfererPatientServiceTable($tableGateway);
							return $table;
						},
						'TransfererPatientServiceTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype ( new TransfererPatientService());
							return new TableGateway ( 'transferer_patient_service', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\SalleTable' => function ($sm) {
							$tableGateway = $sm->get ( 'SalleTableGateway' );
							$table = new SalleTable( $tableGateway );
							return $table;
						},
						'SalleTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Salle() );
							return new TableGateway ( 'salle', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\Soinhospitalisation3Table' => function ($sm) {
							$tableGateway = $sm->get ( 'Soinhospitalisation3TableGateway' );
							$table = new Soinhospitalisation3Table( $tableGateway );
							return $table;
						},
						'Soinhospitalisation3TableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Soinhospitalisation3() );
							return new TableGateway ( 'soins_hospitalisation', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\DemandeTable' => function ($sm) {
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
						'Archivage\Model\ExamenTable' => function ($sm) {
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
						'Archivage\Model\ResultatExamenTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ResultatExamenTableGateway' );
							$table = new ResultatExamenTable( $tableGateway );
							return $table;
						},
						'ResultatExamenTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new ResultatExamen() );
							return new TableGateway ( 'resultats_examens', $dbAdapter, null, $resultSetPrototype );
						},
						'Archivage\Model\ResultatVisitePreanesthesiqueTable' => function ($sm) {
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