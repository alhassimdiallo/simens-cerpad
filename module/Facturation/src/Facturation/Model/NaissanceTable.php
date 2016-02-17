<?php

namespace Facturation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class NaissanceTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getNaissance() {
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array());
		
		$select->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = p.ID_PERSONNE' ,array(
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Taille' => 'TAILLE',
				'Id' => 'ID_PERSONNE'
		) );
		$select->join ( array (
				'n' => 'naissance'
		), 'p.ID_PERSONNE = n.ID_BEBE' );
		$select->order ( 'p.ID_PERSONNE DESC' );
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
		return $result;
	}
	
	public function nbPatientNaissance() {
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ( 'naissance' );
		$select->columns ( array (
				'id_bebe'
		) );
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$nb = $stat->execute ()->count ();
		return $nb;
	}
	
	public function getPatientNaissance($id)
	{
		$id = ( int ) $id;
		$rowset = $this->tableGateway->select ( array (
				'ID_BEBE' => $id
		) );
		$row = $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;

	}
	
	public function updateBebe($data, $id_bebe){
		$this->tableGateway->update ( $data, array ( 'ID_BEBE' => $id_bebe ) );
	}
	
	public function deleteNaissance($id){
		$this->tableGateway->delete ( array (
				'id_bebe' => $id
		) );
	}
	
	public function addNaissance($donneesNaissance){
		$this->tableGateway->insert($donneesNaissance);
	}
	
}