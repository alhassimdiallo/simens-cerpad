<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;

class LogistiqueTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getLogistique($id_personne)
	{
		$id_personne  = (int) $id_personne;
		$rowset = $this->tableGateway->select(array('id_personne' => $id_personne));
		$row = $rowset->current();
		if (!$row) {
			//throw new \Exception("Could not find row $id_personne");
			return null;
		}
		return $row;
	}
	
	public function saveLogistique(Personnel $personnel, $id_personnel)
	{
 		$data = array(
 				'id_personne' => $id_personnel,
 				'matricule' => $personnel->matricule_logistique,
 				'grade' => $personnel->grade_logistique,
 				'domaine' => $personnel->domaine_logistique,
 				'autres' => $personnel->autres_logistique,
 		);
 		
 		$id_personne = (int)$personnel->id_personne;
 		if($id_personne == 0){
 			$this->tableGateway->insert($data);
 		} else {
 			if($this->getLogistique($id_personne)) {
 				$this->tableGateway->update($data, array('id_personne' => $id_personne));
 			} else {
 				if($personnel->matricule_logistique){
 					$this->tableGateway->insert($data);
 				}
 			}
 		}
	}
	
	public function deleteLogistique($id_personne){
		$id_personne = (int) $id_personne;
	
		if ($this->getLogistique($id_personne)) {
			$this->tableGateway->delete( array('id_personne' => $id_personne));
		} else {
			//throw new \Exception('Cette personne n existe pas');
			return null;
		}
	}
}