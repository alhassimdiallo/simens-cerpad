<?php
namespace Consultation\Model;


class AntecedentPersonnel {
	public $id_personne;
	public $id_antecedent;
	public $date_debut;
	public $date_arret;
	public $nombre_paquet_jour;
	public $note;
	public $duree;
	public $regularite;
	public $dysmenorrhee;
	public $id_employe; 
	
	public function exchangeArray($data) {
		$this->id_personne = (! empty ( $data ['ID_PERSONNE'] )) ? $data ['ID_PERSONNE'] : null;
		$this->id_antecedent = (! empty ( $data ['ID_ANTECEDENT'] )) ? $data ['ID_ANTECEDENT'] : null;
		$this->date_debut = (! empty ( $data ['DATE_DEBUT'] )) ? $data ['DATE_DEBUT'] : null;
		$this->date_arret = (! empty ( $data ['DATE_ARRET'] )) ? $data ['DATE_ARRET'] : null;
		$this->nombre_paquet_jour = (! empty ( $data ['NOMBRE_PAQUET_JOUR'] )) ? $data ['NOMBRE_PAQUET_JOUR'] : null;
		$this->note = (! empty ( $data ['NOTE'] )) ? $data ['NOTE'] : null;
		$this->duree = (! empty ( $data ['DUREE'] )) ? $data ['DUREE'] : null;
		$this->regularite = (! empty ( $data ['REGULARITE'] )) ? $data ['REGULARITE'] : null;
		$this->dysmenorrhee = (! empty ( $data ['DYSMENORRHEE'] )) ? $data ['DYSMENORRHEE'] : null;
		$this->id_employe = (! empty ( $data ['ID_EMPLOYE'] )) ? $data ['ID_EMPLOYE'] : null;
		
	}
}