<?php

namespace Archivage\Model;

use Zend\InputFilter\InputFilterInterface;

/**
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 * @author hassim
 *
 */
class Salle {
	
	public $id_salle;
	public $numero_salle;
	public $id_batiment;
	public $date_inauguration;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 			$this->id_salle = (! empty ( $data ['id_salle'] )) ? $data ['id_salle'] : null;
 			$this->numero_salle = (! empty ( $data ['numero_salle'] )) ? $data ['numero_salle'] : null;
 			$this->id_batiment = (! empty ( $data ['id_batiment'] )) ? $data ['id_batiment'] : null;
 			$this->date_inauguration = (! empty ( $data ['date_inauguration'] )) ? $data ['date_inauguration'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
}