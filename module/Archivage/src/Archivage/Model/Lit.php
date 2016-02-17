<?php

namespace Archivage\Model;

use Zend\InputFilter\InputFilterInterface;

/**
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 * @author hassim
 *
 */
class Lit {
	
	public $id_materiel;
	public $id_salle;
	public $marque;
	public $modele;
	public $date_acquisition;
	public $serie;
	public $intitule;
	public $description;
	public $disponible;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 			$this->id_materiel = (! empty ( $data ['id_materiel'] )) ? $data ['id_materiel'] : null;
 			$this->id_salle = (! empty ( $data ['id_salle'] )) ? $data ['id_salle'] : null;
 			$this->marque = (! empty ( $data ['marque'] )) ? $data ['marque'] : null;
 			$this->modele = (! empty ( $data ['modele'] )) ? $data ['modele'] : null;
 			$this->date_acquisition = (! empty ( $data ['date_acquisition'] )) ? $data ['date_acquisition'] : null;
 			$this->serie = (! empty ( $data ['serie'] )) ? $data ['serie'] : null;
 			$this->intitule = (! empty ( $data ['intitule'] )) ? $data ['intitule'] : null;
 			$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
 			$this->disponible = (! empty ( $data ['disponible'] )) ? $data ['disponible'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
}