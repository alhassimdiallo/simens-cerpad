<?php

namespace Hospitalisation\Model;

use Zend\InputFilter\InputFilterInterface;

/**
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 * @author hassim
 *
 */
class Batiment {
	
	public $id_batiment;
	public $type;
	public $localisation;
	public $date_fabrication;
	public $intitule;
	public $description;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 			$this->id_batiment = (! empty ( $data ['id_batiment'] )) ? $data ['id_batiment'] : null;
 			$this->type = (! empty ( $data ['type'] )) ? $data ['type'] : null;
 			$this->localisation = (! empty ( $data ['localisation'] )) ? $data ['localisation'] : null;
 			$this->date_fabrication = (! empty ( $data ['date_fabrication'] )) ? $data ['date_fabrication'] : null;
 			$this->intitule = (! empty ( $data ['intitule'] )) ? $data ['intitule'] : null;
 			$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
}