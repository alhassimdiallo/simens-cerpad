<?php

namespace Consultation\Model;

use Zend\InputFilter\InputFilterInterface;

/**
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 * @author hassim
 *
 */
class Soins {
	
	public $id_soins;
	public $libelle;
	public $date_ajout;
	public $horaire;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 			$this->id_soins = (! empty ( $data ['id_soins'] )) ? $data ['id_soins'] : null;
 			$this->libelle = (! empty ( $data ['libelle'] )) ? $data ['libelle'] : null;
 			$this->date_ajout = (! empty ( $data ['date_ajout'] )) ? $data ['date_ajout'] : null;
 			$this->horaire = (! empty ( $data ['horaire'] )) ? $data ['horaire'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
}