<?php

namespace Hospitalisation\Model;

use Zend\Db\TableGateway\TableGateway;

class SalleTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getSalle($id_salle)
	{
		$rowset = $this->tableGateway->select(array(
				'id_salle' => (int) $id_salle,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}

	public function updateSalle($id_salle)
	{
		$this->tableGateway->update(array(), array('id_salle' => $id_salle));
	}
}