<?php

namespace Archivage\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;
use Zend\XmlRpc\Value\String;

class BatimentTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getBatiment($id_batiment)
	{
		$rowset = $this->tableGateway->select(array(
				'id_batiment' => (int) $id_batiment,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	/*
	 * Liste des batiments ayant des salles ayant des lits disponible
	 */
	public function listeBatiments()
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('bat'=>'batiment'));
		$select->columns(array ('IdBatiment'=>'id_batiment', 'IdSalle'=>'intitule'));
		$select->join(array('s'=>'salle'), 's.id_batiment = bat.id_batiment', array('*'));
		$select->join(array('l'=>'lit'), 'l.id_salle = s.id_salle', array('*'))
		->where(array('disponible'=> 0));
		$select->order('bat.id_batiment ASC');
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		$options = array();
		foreach ($result as $data) {
			$options[$data['IdBatiment']] = $data['IdSalle'];
		}
		return $options;
	}
	
	/*
	 * Liste des salles disponibles pour un batiment donne
	 */
	public function listeSalleDisponible($id_batiment)
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('s'=>'salle'))
		->columns(array('IdSalle' => 'id_salle', 'NumeroSalle' => 'numero_salle'))
		->where(array('id_batiment'=>$id_batiment));
		$select->join(array('l'=>'lit'), 'l.id_salle = s.id_salle', array('*'))
		->where(array('l.disponible'=> 0))
		->group('l.id_salle');
		$select->order('s.id_salle ASC');
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;
	}
	
	/*
	 * Liste des lits disponibles pour une salle donnee
	 */
	public function listeLitDisponible($id_salle)
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('l'=>'lit'))
		->columns(array('IdLit' => 'id_materiel', 'NomLit' => 'intitule'))
		->where(array('id_salle'=>$id_salle, 'l.disponible'=> 0));
		$select->order('l.id_materiel ASC');
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;
	}
}