<?php

namespace Secretariat\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\NotIn;

class AnalyseTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	//Recuperer la demande avec 'D'=date, 'P'=idpatient, 'A'=idanalyse
	public function getDemandeAnalyseDPA($date, $idpatient, $idanalyse){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('d_a' => 'demande_analyse'))->columns(array('*'))
		->where(array('date' => $date, 'idpatient' => $idpatient, 'idanalyse' => $idanalyse));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		return $stat->execute()->current();
	}
	
	public function addDemandeAnalyse($analyses, $idemploye, $idpatient){
		$date = (new \DateTime() ) ->format('Y-m-d');
		$time = (new \DateTime() ) ->format('H:i:s');
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		
		for($i = 1 ; $i <= count($analyses) ; $i++){
			
			if(!$this->getDemandeAnalyseDPA($date, $idpatient, $analyses[$i])){
				$donnees = array(
						'date' => $date,
						'time' => $time,
						'idsecretaire' => $idemploye,
						'idpatient' => $idpatient,
						'idanalyse' => $analyses[$i],
				);
					
				$sQuery = $sql->insert() ->into('demande_analyse') ->values( $donnees );
				$sql->prepareStatementForSqlObject($sQuery)->execute();
			}
				
		}
		
	}

	
	public function getResultatAnalysesDemandees($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('r' => 'resultat_demande_analyse'))->columns(array('*'))
	    ->where(array('iddemande_analyse' => $iddemande ));
	    
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	/**
	 * On ne supprime que les analyses pour lesquelles il n'y a pas encore de résultat
	 **/
	public function deleteDemandeAnalyse($idpatient){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);

	    $listeAnalysesDemandees = $this->getListeAnalysesDemandeesDP($idpatient);
	    for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
	        $iddemande = $listeAnalysesDemandees[$i]['iddemande'];
	        
	        if(!$this->getResultatAnalysesDemandees($iddemande)){
	            $sQuery = $sql->delete() ->from('demande_analyse')
	            ->where(array('iddemande' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery) ->execute();
	        }
	    }
	    
	}
	
	public function updateDemandeAnalyse($analyses, $idemploye, $idpatient){
	    $date = (new \DateTime() ) ->format('Y-m-d');
	    $time = (new \DateTime() ) ->format('H:i:s');
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	
	    $this->deleteDemandeAnalyse($idpatient);
	    
	    for($i = 1 ; $i <= count($analyses) ; $i++){
	        	
	        if(!$this->getDemandeAnalyseDPA($date, $idpatient, $analyses[$i])){
	            $donnees = array(
	                'date' => $date,
	                'time' => $time,
	                'idsecretaire' => $idemploye,
	                'idpatient' => $idpatient,
	                'idanalyse' => $analyses[$i],
	            );
	            	
	            $sQuery = $sql->insert() ->into('demande_analyse') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	
	    }
	
	}
	
	public function addDignosticAnalyse($idemploye, $idpatient, $diagnostic_demande){
	    $date = (new \DateTime() ) ->format('Y-m-d');
	    $time = (new \DateTime() ) ->format('H:i:s');
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    
	    $donnees = array(
	        'date' => $date,
	        'time' => $time,
	        'idsecretaire' => $idemploye,
	        'idpatient' => $idpatient,
	        'diagnostic_demande' => $diagnostic_demande,
	    );
	    	
	    $sQuery = $sql->insert() ->into('diagnostic_demande_analyse') ->values( $donnees );
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function updateDignosticAnalyse($idemploye, $idpatient, $diagnostic_demande){
	    $date = (new \DateTime() ) ->format('Y-m-d');
	    $time = (new \DateTime() ) ->format('H:i:s');
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	     
	    if($this->getDiagnosticAnalyse($idpatient, $date)){
	        $donnees = array(
	            'time' => $time,
	            'idsecretaire' => $idemploye,
	            'diagnostic_demande' => $diagnostic_demande,
	        );
	        
	        $db = $this->tableGateway->getAdapter();
	        $sql = new Sql($db);
	        $sQuery = $sql->update() ->table('diagnostic_demande_analyse')
	        ->set( $donnees ) ->where(array('idpatient' => $idpatient, 'date' => $date ));
	         
	        $sql->prepareStatementForSqlObject($sQuery)->execute();
	    }else{
	        $this->addDignosticAnalyse($idemploye, $idpatient, $diagnostic_demande);
	    }
	    
	}
	
	public function deleteDignosticAnalyse($idpatient){
	    $date = (new \DateTime() ) ->format('Y-m-d');
	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->delete() ->from('diagnostic_demande_analyse')
	    ->where(array('idpatient' => $idpatient, 'date' => $date ));
	    
	    $sql->prepareStatementForSqlObject($sQuery) ->execute();
	}
	
	public function getDiagnosticAnalyse($idpatient, $date){
	    $adapter = $this->tableGateway->getAdapter ();
	    $sql = new Sql($adapter);
	    $select = $sql->select();
	    $select->from(array('dda'=>'diagnostic_demande_analyse'));
	    $select->columns(array('*'));
	    $select->where(array('dda.idpatient' => $idpatient, 'dda.date' => $date));
	    return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	//Recuperer les informations sur l' analyse de la demande iddemande
	public function getAnalysesDemandees($iddemande){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('d.iddemande' => $iddemande));
		return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	public function getDemandeAnalysesAvecIddemande($iddemande){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->where(array('iddemande' => $iddemande));
		return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	//Recuperer la liste des analyses demandees pour la demande $iddemande
	public function getListeAnalysesDemandees($iddemande){
		$demande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $demande['date'], 'idpatient' => $demande['idpatient']));
		$select->order(array('a.Idanalyse' => 'ASC', 'idtype' =>'ASC'));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	/**
	 * Liste des analyses demandees par type pour toutes les demandes
	 */
	public function getListeAnalysesDemandeesParType($idtype){
		
		$adapter1 = $this->tableGateway->getAdapter ();
		$sql1 = new Sql($adapter1);
		$select1 = $sql1->select()->from(array('r'=>'resultat_demande_analyse'))->columns(array('iddemande' => 'iddemande_analyse'));
		
		$adapter2 = $this->tableGateway->getAdapter ();
		$sql2 = new Sql($adapter2);
		$select2 = $sql2->select();
		$select2->from(array('d'=>'demande_analyse'));
		$select2->columns(array('*'));
		$select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
		$select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
		$select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
		$select2->where(array('t.idtype' => $idtype, new NotIn ( 'd.iddemande', $select1 )));
		$select2->order(array('iddemande' => 'ASC', 'a.Idanalyse' => 'ASC'));
		return $sql2->prepareStatementForSqlObject($select2)->execute();
	}
	
	/**
	 * Liste des analyses demandees par type et par analyse pour toutes les demandes
	 */
	public function getListeAnalysesDemandeesParTypeEtAnalyse($idtype, $idanalyse){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql($adapter1);
	    $select1 = $sql1->select()->from(array('r'=>'resultat_demande_analyse'))->columns(array('iddemande' => 'iddemande_analyse'));
	
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, 'a.idanalyse' => $idanalyse, new NotIn ( 'd.iddemande', $select1 )));
	    $select2->order(array('iddemande' => 'ASC', 'a.Idanalyse' => 'ASC'));
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	/**
	 * Liste des analyses demandees par type et par analyse et par date pour toutes les demandes
	 */
	public function getListeAnalysesDemandeesParTypeEtDate($idtype, $date){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql($adapter1);
	    $select1 = $sql1->select()->from(array('r'=>'resultat_demande_analyse'))->columns(array('iddemande' => 'iddemande_analyse'));
	
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, 'd.date' => $date, new NotIn ( 'd.iddemande', $select1 )));
	    $select2->order(array('iddemande' => 'ASC', 'a.Idanalyse' => 'ASC'));
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	/**
	 * Liste des analyses demandees par type et par analyse et par date pour toutes les demandes
	 */
	public function getListeAnalysesDemandeesParTypeEtAnalyseEtDate($idtype, $idanalyse, $date){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql($adapter1);
	    $select1 = $sql1->select()->from(array('r'=>'resultat_demande_analyse'))->columns(array('iddemande' => 'iddemande_analyse'));
	
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, 'a.idanalyse' => $idanalyse, 'd.date' => $date, new NotIn ( 'd.iddemande', $select1 )));
	    $select2->order(array('iddemande' => 'ASC', 'a.Idanalyse' => 'ASC'));
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	
	
	
	
	
	
	
	/**
	 * Liste des analyses demandees par type pour toutes les demandes en regroupant par analyse 
	 */
	public function getListeAnalysesDemandeesParTypeGroupeAnalyse($idtype){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql($adapter1);
	    $select1 = $sql1->select()->from(array('r'=>'resultat_demande_analyse'))->columns(array('iddemande' => 'iddemande_analyse'));
	
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, new NotIn ( 'd.iddemande', $select1 )));
	    $select2->order(array('iddemande' => 'ASC', 'a.Idanalyse' => 'ASC'));
	    $select2->group('a.Idanalyse');
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	
	/**
	 * Liste des analyses demandees par type pour toutes les demandes en regroupant par date
	 */
	public function getListeAnalysesDemandeesParTypeGroupeDate($idtype){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql($adapter1);
	    $select1 = $sql1->select()->from(array('r'=>'resultat_demande_analyse'))->columns(array('iddemande' => 'iddemande_analyse'));
	
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, new NotIn ( 'd.iddemande', $select1 )));
	    $select2->order(array('d.date' => 'DESC'));
	    $select2->group('d.date');
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	
	/**
	 * Liste des analyses demandees par type pour toutes les demandes en regroupant par date
	 */
	public function getListeAnalysesDemandeesParTypeEtParAnalyseGroupeDate($idtype, $idanalyse){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql($adapter1);
	    $select1 = $sql1->select()->from(array('r'=>'resultat_demande_analyse'))->columns(array('iddemande' => 'iddemande_analyse'));
	
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, 'd.idanalyse' => $idanalyse, new NotIn ( 'd.iddemande', $select1 )));
	    $select2->order(array('d.date' => 'DESC'));
	    $select2->group('d.date');
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	//Recuperer la liste des analyses demandees avec 'D'=date, 'P'=idpatient
	public function getListeAnalysesDemandeesDP($idpatient){
	    $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('d' => 'demande_analyse'))->columns(array('*'))
	    ->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
	    ->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
	    ->where(array('d.date' => $aujourdhui, 'd.idpatient' => $idpatient))
	    ->order(array('result' => 'DESC', 't.idtype' => 'ASC', 'd.idanalyse' => 'ASC'));
	    
	    $resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
	    
	    $donnees = array();
	    foreach ($resultat as $result){
	        $donnees[] = $result;
	    }
	    
	    return $donnees;
	}
	
	
	
}