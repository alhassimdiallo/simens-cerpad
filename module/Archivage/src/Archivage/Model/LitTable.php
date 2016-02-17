<?php

namespace Archivage\Model;

use Zend\Db\TableGateway\TableGateway;

class LitTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getLit($id_lit)
	{
		$rowset = $this->tableGateway->select(array(
				'id_materiel' => (int) $id_lit,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}

	public function updateLit($id_lit)
	{
		$this->tableGateway->update(array('disponible' => 1), array('id_materiel' => $id_lit));
	}
	
	public function libererLit($id_materiel)
	{
		$this->tableGateway->update(array('disponible' => 0), array('id_materiel' => $id_materiel));
	}
}