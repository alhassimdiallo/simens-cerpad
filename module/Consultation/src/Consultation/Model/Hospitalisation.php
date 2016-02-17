<?php

namespace Consultation\Model;

use Zend\InputFilter\InputFilterInterface;

/**
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 * @author hassim
 *
 */
class Hospitalisation {
	
	public $id_hosp;
	public $code_demande_hospitalisation;
	public $date_debut;
	public $date_fin;
	public $resumer_medical;
	public $motif_sorti;
	public $terminer; 
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 			$this->id_hosp = (! empty ( $data ['id_hosp'] )) ? $data ['id_hosp'] : null;
 			$this->code_demande_hospitalisation = (! empty ( $data ['code_demande_hospitalisation'] )) ? $data ['code_demande_hospitalisation'] : null;
 			$this->date_debut = (! empty ( $data ['date_debut'] )) ? $data ['date_debut'] : null;
 			$this->date_fin = (! empty ( $data ['date_fin'] )) ? $data ['date_fin'] : null;
 			$this->resumer_medical = (! empty ( $data ['resumer_medical'] )) ? $data ['resumer_medical'] : null;
 			$this->motif_sorti = (! empty ( $data ['motif_sorti'] )) ? $data ['motif_sorti'] : null;
 			$this->terminer = (! empty ( $data ['terminer'] )) ? $data ['terminer'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
}