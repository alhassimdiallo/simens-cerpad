<?php

namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;

class HospitalisationlitTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getHospitalisationlit($id_hosp)
	{
		$rowset = $this->tableGateway->select(array(
				'id_hosp' => (int) $id_hosp,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function saveHospitalisationlit($id_hosp, $id_lit)
	{
		$today = new \DateTime ();
		$date = $today->format ( 'Y-m-d H:i:s' );
		
		$data = array(
				'date_debut' => $date,
				'id_hosp' => $id_hosp,
				'id_materiel' => $id_lit
		);
		$this->tableGateway->insert($data);
	}
}