<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
class DemandeVisitePreanesthesiqueTable{
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getDemandeVPA($id_cons)
	{
		$rowset = $this->tableGateway->select(array(
				'ID_CONS' => $id_cons,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}

	public function getDemandeVisitePreanesthesique($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('demande_visite_preanesthesique');
		$select->columns(array('*'));
		$select->where(array('ID_CONS'=>$id));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
		return $result;
	}
	
	public function getResultatVpa($idVpa){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('resultat_vpa');
		$select->columns(array('*'));
		$select->where(array('idVpa'=>$idVpa));
		
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
		return $result;
	}
	
	public function updateDemandeVisitePreanesthesique($infoDemande){
		
		$demandeVpa = $this->getDemandeVPA($infoDemande['ID_CONS']);
		
		$resultatVpa = null;
		if($demandeVpa){
			$resultatVpa = $this->getResultatVpa($demandeVpa->idVpa);
		}
		
		$today = new \DateTime ();
		$dateAujourdhui = $today->format ( 'Y-m-d H:i:s' );
		
		if(!$resultatVpa){
			$this->tableGateway->delete(array('ID_CONS'=>$infoDemande['ID_CONS']));
			
			if($infoDemande['diagnostic'] !='' && $infoDemande['intervention_prevue'] !=''){
				$donneesVPA = array(
						'ID_CONS'     => $infoDemande['ID_CONS'],
						'DIAGNOSTIC'  => $infoDemande['diagnostic'],
						'OBSERVATION' => $infoDemande['observation'],
						'INTERVENTION_PREVUE' => $infoDemande['intervention_prevue'],
						'DATE_ENREGISTREMENT' => $dateAujourdhui
				);
				$this->tableGateway->insert($donneesVPA);
			}
		}
	}
	
	
}
