<?php

namespace Secretariat\Model;

class Patient {
	public $idpersonne;
	public $date_enregistrement;
	public $date_modification;
	public $ethnie;
	public $typepatient;
	public $idemploye;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
		$this->idpersonne = (! empty ( $data ['idpersonne'] )) ? $data ['idpersonne'] : null;
 		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
 		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
 		$this->ethnie = (! empty ( $data ['ethnie'] )) ? $data ['ethnie'] : null;
 		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;
 		$this->typepatient = (! empty ( $data ['typepatient'] )) ? $data ['typepatient'] : null;
 		
	}
	
}