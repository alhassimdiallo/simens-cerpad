<?php

namespace Consultation\Model;

use Zend\InputFilter\InputFilterInterface;

/**
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 * @author hassim
 *
 */
class Hospitalisationlit {
	
	public $id_hosp;
	public $id_materiel;
	public $date_debut;
	public $date_fin;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 			$this->id_hosp = (! empty ( $data ['id_hosp'] )) ? $data ['id_hosp'] : null;
 			$this->id_materiel = (! empty ( $data ['id_materiel'] )) ? $data ['id_materiel'] : null;
 			$this->date_debut = (! empty ( $data ['date_debut'] )) ? $data ['date_debut'] : null;
 			$this->date_fin = (! empty ( $data ['date_fin'] )) ? $data ['date_fin'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
}