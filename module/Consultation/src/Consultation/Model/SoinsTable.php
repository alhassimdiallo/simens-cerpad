<?php

namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;

class SoinsTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getSoins($id_soins)
	{
		$rowset = $this->tableGateway->select(array(
				'id_soins' => (int) $id_soins,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}

	public function updateSoins($id_soins)
	{
		$this->tableGateway->update(array(), array('id_soins' => $id_soins));
	}
	
	/**
	 * Liste des soins
	*/
	public function listeSoins()
	{
		$rowset = $this->tableGateway->select(array())->toArray();
		$options = array();
		foreach ($rowset as $data) {
			$options[$data['id_soins']] = $data['libelle'];
		}
		return $options;
	}
}