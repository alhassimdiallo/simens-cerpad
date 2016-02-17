<?php

namespace Hospitalisation\Model;

use Zend\InputFilter\InputFilterInterface;

/**
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 * @author hassim
 *
 */
class Demandehospitalisation {
	
	public $id_demande_hospi;
	public $motif_demande_hospi;
	public $date_demande_hospi;
	public $date_fin_prevue_hospi;
	public $valider_demande_hospi;
	public $id_cons;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 			$this->id_demande_hospi = (! empty ( $data ['id_demande_hospi'] )) ? $data ['id_demande_hospi'] : null;
 			$this->motif_demande_hospi = (! empty ( $data ['motif_demande_hospi'] )) ? $data ['motif_demande_hospi'] : null;
 			$this->date_demande_hospi = (! empty ( $data ['date_demande_hospi'] )) ? $data ['date_demande_hospi'] : null;
 			$this->date_fin_prevue_hospi = (! empty ( $data ['date_fin_prevue_hospi'] )) ? $data ['date_fin_prevue_hospi'] : null;
 			$this->valider_demande_hospi = (! empty ( $data ['valider_demande_hospi'] )) ? $data ['valider_demande_hospi'] : null;
 			$this->id_cons = (! empty ( $data ['id_cons'] )) ? $data ['id_cons'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
}