<?php

namespace Archivage\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;
use Zend\XmlRpc\Value\String;

class HospitalisationTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getHospitalisation($id_hosp)
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
	
	/**
	 * Recuperer l'hospitalisation connaissant le code de la demande d'hospitalisation
	 */
	public function getHospitalisationWithCodedh($id_demande_hospi)
	{
		$rowset = $this->tableGateway->select(array(
				'code_demande_hospitalisation' => (int) $id_demande_hospi,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function saveHospitalisation($code_demande, $date_debut)
	{
		//$today = new \DateTime ();
		//$date = $today->format ( 'Y-m-d H:i:s' );
		
		$data = array(
				'date_debut' => $date_debut,
				'code_demande_hospitalisation' => $code_demande
		);
		return($this->tableGateway->getLastInsertValue($this->tableGateway->insert($data)));
	}
	
	/**
	 * Liberation du patient
	 */
	public function libererPatient($id_demande_hospi, $resumer_medical, $motif_sorti) 
	{
		$today = new \DateTime ();
		$date = $today->format ( 'Y-m-d H:i:s' );
		
		$data = array(
				'date_fin' => $date,
				'resumer_medical' => $resumer_medical,
				'motif_sorti' => $motif_sorti,
				'terminer' => 1
		);
		return $this->tableGateway->update($data, array('code_demande_hospitalisation' => $id_demande_hospi));
	}
}