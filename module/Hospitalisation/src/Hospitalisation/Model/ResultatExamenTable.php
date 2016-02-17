<?php

namespace Hospitalisation\Model;

use Zend\Db\TableGateway\TableGateway;

class ResultatExamenTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getResultatExamen($idDemande)
	{
		$rowset = $this->tableGateway->select(array(
				'idDemande' => (int) $idDemande,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function saveResultatsExamens($donnees, $id_personne)
	{
		$today = new \DateTime ();
		$date = $today->format ( 'Y-m-d H:i:s' );
		
		$data = array(
				'techniqueUtiliser' => $donnees->techniqueUtiliser,
				'noteResultat' => $donnees->noteResultat,
				'conclusion' => $donnees->conclusion,
				'id_personne' => $id_personne,
		);
		
		if($donnees->update == 0) {
			$data['idDemande'] = $donnees->idDemande;
			$data['date_enregistrement'] = $date;
			$this->tableGateway->insert($data);
		} else {
			if($this->getResultatExamen($donnees->idDemande)) {
				$data['date_modifcation'] = $date;
				$this->tableGateway->update($data, array('idDemande' =>$donnees->idDemande));
			}
		}
	}
	
	/**
	 * Examen envoyer
	 */
	public function examenEnvoyer($idDemande)
	{
		$this->tableGateway->update(array('envoyer' => 1), array('idDemande' => $idDemande));
	}

}