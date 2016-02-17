<?php

namespace Facturation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\NotIn;

class DecesTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function nbPatientDecedes() {
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ( 'deces' );
		$select->columns ( array (
				'id'
		) );
		$stmt = $sql->prepareStatementForSqlObject ( $select );
		$nb = $stmt->execute ()->count ();
		return $nb;
	}
	
	public function getPatientsDecedes() {
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$reqSelect = $sql->select ();
		$reqSelect->from ( array (
				'p' => 'patient'
		) )->columns ( array () )
		->join (array('pers' => 'personne') , 'pers.ID_PERSONNE = p.ID_PERSONNE' , array(
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Taille' => 'TAILLE',
				'Id' => 'ID_PERSONNE'
		))
		->join ( array (
				'd' => 'deces'
		), 'p.ID_PERSONNE = d.id_patient', array (
				'Id_deces' => 'id',
				'Heure_deces' => 'heure_deces',
				'Age_deces' => 'age_deces',
				'Date_deces' => 'date_deces',
				'Lieu_deces' => 'lieu_deces',
				'Circonstances_deces' => 'circonstances_deces',
				'Date_enregistrement' => 'date_enregistrement'
		) );
		$reqSelect->order ( 'Id_deces DESC' );
		$statement = $sql->prepareStatementForSqlObject ( $reqSelect );
		$result = $statement->execute ();
		return $result;
	}
	
	public function addDeces($donnees){
		$this->tableGateway->insert($donnees);
	}
	
	public function deletePatient($id){
		$this->tableGateway->delete(array('id_personne'=>$id));
	}
	
	public function getPatientDecede($id){
		$id = ( int ) $id;
		$rowset = $this->tableGateway->select ( array (
				'id_patient' => $id
		) );
		$row =  $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}
	
	public function updateDeces($donnees, $id_deces)
	{
		$this->tableGateway->update($donnees, array('id' => $id_deces));
	}
	
	public function getLePatientDecede($id){
		$id = ( int ) $id;
		$rowset = $this->tableGateway->select ( array (
				'id' => $id
		) );
		$row =  $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}

}