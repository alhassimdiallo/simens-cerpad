<?php
namespace Facturation\Model;

class Naissance {
	public $ID_BEBE;
	public $ID_MAMAN;
	public $HEURE_NAISSANCE;
	public $POIDS;
	public $TAILLE;
	public $GROUPE_SANGUIN;
	public $DATE_ENREGISTREMENT;
	public $DATE_MODIFICATION;
	public $DATE_NAISSANCE;
	public $NOTE;
	public $ID_EMPLOYE;

	public function exchangeArray($data) {
		$this->ID_BEBE = (! empty ( $data ['ID_BEBE'] )) ? $data ['ID_BEBE'] : null;
		$this->ID_MAMAN = (! empty ( $data ['ID_MAMAN'] )) ? $data ['ID_MAMAN'] : null;
		$this->HEURE_NAISSANCE = (! empty ( $data ['HEURE_NAISSANCE'] )) ? $data ['HEURE_NAISSANCE'] : null;
		$this->POIDS = (! empty ( $data ['POIDS'] )) ? $data ['POIDS'] : null;
		$this->TAILLE = (! empty ( $data ['TAILLE'] )) ? $data ['TAILLE'] : null;
		$this->GROUPE_SANGUIN = (! empty ( $data ['GROUPE_SANGUIN'] )) ? $data ['GROUPE_SANGUIN'] : null;
		$this->DATE_ENREGISTREMENT = (! empty ( $data ['DATE_ENREGISTREMENT'] )) ? $data ['DATE_ENREGISTREMENT'] : null;
		$this->DATE_MODIFICATION = (! empty ( $data ['DATE_MODIFICATION'] )) ? $data ['DATE_MODIFICATION'] : null;
		$this->DATE_NAISSANCE = (! empty ( $data ['DATE_NAISSANCE'] )) ? $data ['DATE_NAISSANCE'] : null;
		$this->NOTE = (! empty ( $data ['NOTE'] )) ? $data ['NOTE'] : null;
		$this->ID_EMPLOYE = (! empty ( $data ['ID_EMPLOYE'] )) ? $data ['ID_EMPLOYE'] : null;
	}
}
