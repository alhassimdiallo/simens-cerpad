<?php
namespace Hospitalisation\Model;

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

// 	public function getDemandeVisitePreanesthesique($id){
// 		$adapter = $this->tableGateway->getAdapter();
// 		$sql = new Sql($adapter);
// 		$select = $sql->select();
// 		$select->from('demande_visite_preanesthesique');
// 		$select->columns(array('*'));
// 		$select->where(array('ID_CONS'=>$id));
// 		$stat = $sql->prepareStatementForSqlObject($select);
// 		$result = $stat->execute()->current();
// 		return $result;
// 	}
	
// 	public function updateDemandeVisitePreanesthesique($infoDemande){
// 		$this->tableGateway->delete(array('ID_CONS'=>$infoDemande['ID_CONS']));
		
// 		if($infoDemande['diagnostic'] !='' && $infoDemande['intervention_prevue'] !=''){
// 			$donneesVPA = array(
// 				'ID_CONS'     => $infoDemande['ID_CONS'],
// 				'DIAGNOSTIC'  => $infoDemande['diagnostic'],
// 				'OBSERVATION' => $infoDemande['observation'],
// 				'INTERVENTION_PREVUE' => $infoDemande['intervention_prevue'],
// 				'NUMERO_VPA'  => $infoDemande['numero_vpa'],
// 				'TYPE_ANESTHESIE' => $infoDemande['type_anesthesie'],
// 			);
// 			$this->tableGateway->insert($infoDemande);
// 		}
// 	}

	
}
