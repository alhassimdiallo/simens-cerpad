<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;

class AffectationTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getConversionDate(){
		$this->conversionDate = new DateHelper();
		
		return $this->conversionDate;
	}
	
	public function getAffectation($id_personne)
	{
		$id_personne  = (int) $id_personne;
		$rowset = $this->tableGateway->select(array('id_personne' => $id_personne));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function saveAffectation(Personnel $personnel, $id_personnel)
	{
		$this->getConversionDate();
		
 		$data = array(
 				'id_personne' => $id_personnel,
 				'id_service' => $personnel->service_accueil,
 				'date_debut' => $this->conversionDate->convertDateInAnglais($personnel->date_debut),
 				'date_fin' => $this->conversionDate->convertDateInAnglais($personnel->date_fin),
 				'numero_os' => $personnel->numero_os,
 		);
 		
 		$id_personne = (int)$personnel->id_personne;
 		if($id_personne == 0){
 			$this->tableGateway->insert($data);
 		} else {
 			if($this->getAffectation($id_personne)) {
 				$this->tableGateway->update($data, array('id_personne' => $id_personne));
 			} else {
 				if($personnel->service_accueil){
 					$this->tableGateway->insert($data);
 				}
 			}
 		}
	}
	
	public function deleteAffectation($id_personne){
		$id_personne = (int) $id_personne;
	
		if ($this->getAffectation($id_personne)) {
			$this->tableGateway->delete( array('id_personne' => $id_personne));
		} else {
			return null;
		}
	}
	
	/*
	 * Recuperer le service ou l'agent est affecte
	 */
	public function getServiceAgentAffecter($id_personne){
		$id_personne = (int) $id_personne;
		
		$row = $this->getAffectation($id_personne);
		
		if ($row) {
			return $row->service_accueil;
		} else {
			return null;
		}
	}
}