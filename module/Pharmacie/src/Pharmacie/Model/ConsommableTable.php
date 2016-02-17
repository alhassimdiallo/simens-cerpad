<?php
namespace Pharmacie\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
//use Zend\Db\Adapter\Adapter;

class ConsommableTable{
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function compteNBLigne(){
		$resultSet = $this->tableGateway->select ();
		$nb = $resultSet->count();
		return $nb;
	}
	public function addConsommable($data){
		$this->tableGateway->insert($data);
	}
	public function updateConsommable($data)
	{
		$this->tableGateway->update($data, array('ID_MATERIEL'=> $data['ID_MATERIEL']));
	}
	public function getAllConsommable(){
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	public function getConsommable($id){
		$id = ( int ) $id;
		$rowset = $this->tableGateway->select ( array (
				'ID_MATERIEL' => $id
		) );
		$row =  $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}
	public function deleteConsommable($id){
		$this->tableGateway->delete ( array (
				'ID_MATERIEL' => $id
		) );
	}
	public function fetchCommandes()
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql ( $adapter );
		$select = $sql->select();
		$select->from(array('cons'=>'consommable'));
		//$select->columns(array(SQL_STAR ));
		$select->join(array (
				'cs' => 'commande_consommable'
		), 'cons.ID_MATERIEL = cs.ID_MATERIEL', array('ID_COMMANDE', 'ID_MATERIEL', 'QUANTITE', 'ETAT', 'Grandtotal'=>new Expression('SUM((cons.PRIX)*QUANTITE)')));
		$select->join(array (
				'com' => 'commande'
		), 'cs.ID_COMMANDE = com.ID_COMMANDE');
		$select->group('cs.ID_COMMANDE');
		$stat = $sql->prepareStatementForSqlObject($select);
		$listeCommandes =  $stat->execute();
		return $listeCommandes;
	}
	public function compteNBCommandes(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql ( $adapter );
		$select = $sql->select ( 'commande' );
		$select->group('ID_COMMANDE');
		$stat = $sql->prepareStatementForSqlObject($select);
		$nb = $stat->execute()->count();
		return $nb;
	}
	public function getCommande($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql ( $adapter );
		$select = $sql->select();
		$select->from(array('lacommande'=>'commande'));
		$select->columns(array('DATE','HEURE', 'ID_COMMANDE'));
		$select->where(array('ID_COMMANDE'=>$id));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
		return $result;
	}
	public function fetchMedicamentsCommande($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql ( $adapter );
		$select = $sql->select();
		$select->from(array('consom'=>'consommable'));
		$select->join(array (
				'com_cons' => 'commande_consommable'
		), 'consom.ID_MATERIEL = com_cons.ID_MATERIEL');
		$select->where(array('ID_COMMANDE'=>$id));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
		return $result;
	}
	public function fetchConsommable(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql ( $adapter );
		$select = $sql->select('consommable');
		$select->columns(array('ID_MATERIEL','INTITULE'));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
		foreach ($result as $data) {
			$options[$data['ID_MATERIEL']] = $data['INTITULE'];
		}
		return $options;
	}
	
	
	/**
	 * Recuperation de la liste des medicaments
	 */
	public function listeDeTousLesMedicaments(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql ( $adapter );
		$select = $sql->select('consommable');
		$select->columns(array('ID_MATERIEL','INTITULE'));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
		
		return $result;
	}
	
	
	/** 
	 * RECUPERER LA FORME DES MEDICAMENTS 
	 */

	public function formesMedicaments(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'forme' => 'forme_medicament' ));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
		
		return $result;
	}
	
	/**
	 * RECUPERER LES TYPES DE QUANTITE DES MEDICAMENTS
	 */
	
	public function typeQuantiteMedicaments(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'typeQuantite' => 'quantite_medicament' ));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
	
		return $result;
	}
}