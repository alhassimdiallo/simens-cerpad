<?php

namespace Archivage\Model;

use Zend\Db\TableGateway\TableGateway;

class ExamenTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getExamen($idExamen)
	{
		$rowset = $this->tableGateway->select(array(
				'idExamen' => (int) $idExamen,
		));
		
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
// 	public function getDemandesExamens($id_cons) 
// 	{
// 		$db = $this->tableGateway->getAdapter();
// 		$sql = new Sql($db);
// 		$sQuery = $sql->select();
// 		$sQuery->from(array('d' => 'demande'))->columns(array('*'))
// 		->where(array('d.idCons' => $id_cons))
// 		->order('d.idDemande ASC');

// 		$stat = $sql->prepareStatementForSqlObject($sQuery);
// 		$Result = $stat->execute();
		
// 		return $Result;
// 	}
}