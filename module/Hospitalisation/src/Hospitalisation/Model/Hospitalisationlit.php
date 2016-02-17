<?php

namespace Hospitalisation\Model;

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
	public $liberation_lit;
	public $date_liberation_lit;
	public $note_liberation;
	public $id_major;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 			$this->id_hosp = (! empty ( $data ['id_hosp'] )) ? $data ['id_hosp'] : null;
 			$this->id_materiel = (! empty ( $data ['id_materiel'] )) ? $data ['id_materiel'] : null;
 			$this->date_debut = (! empty ( $data ['date_debut'] )) ? $data ['date_debut'] : null;
 			$this->liberation_lit = (! empty ( $data ['liberation_lit'] )) ? $data ['liberation_lit'] : null;
 			$this->date_liberation_lit = (! empty ( $data ['date_liberation_lit'] )) ? $data ['date_liberation_lit'] : null;
 			$this->note_liberation = (! empty ( $data ['note_liberation'] )) ? $data ['note_liberation'] : null;
 			$this->id_major = (! empty ( $data ['id_major'] )) ? $data ['id_major'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
}