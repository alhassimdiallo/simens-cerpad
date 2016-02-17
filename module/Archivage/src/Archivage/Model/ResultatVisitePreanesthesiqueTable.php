<?php
namespace Archivage\Model;

use Zend\Db\TableGateway\TableGateway;

class ResultatVisitePreanesthesiqueTable{
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getResultatVpa($idVpa)
	{
		$rowset = $this->tableGateway->select(array(
				'idVpa' => (int) $idVpa,
		));
	
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function saveResultat($resultatVpa)
	{
		$data = array(
				'numeroVpa' => $resultatVpa->numero_vpa,
				'typeIntervention' => $resultatVpa->type_intervention,
				'aptitude' => $resultatVpa->aptitude,
				'idVpa' => $resultatVpa->idVpa,
				'id_personne' => $resultatVpa->idPersonne,
		);
		
		$this->tableGateway->insert($data);
	}
	
}
