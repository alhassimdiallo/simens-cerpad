<?php

namespace Archivage\Model;

/**
 * POUR LA RECUPERATION DES DONNEES DU FORMULAIRE
 * @author hassim
 *
 */
class Soinhospitalisation3 {
	
	public $id_sh;
	public $id_hosp;
	public $medicament;
	public $voie_administration;
	public $frequence;
	public $dosage;
	public $date_application_recommandee;
	public $heure_recommandee;
	public $note;
	public $motif;
	public $date_enregistrement;
	public $appliquer;
	public $date_application;
	public $note_application;
	public $date_modification;
	public $id_personne;
	
	public function exchangeArray($data) {
		$this->id_sh = (! empty ( $data ['id_sh'] )) ? $data ['id_sh'] : null;
		$this->id_hosp = (! empty ( $data ['id_hosp'] )) ? $data ['id_hosp'] : null;
		$this->medicament = (! empty ( $data ['medicament'] )) ? $data ['medicament'] : null;
		$this->voie_administration = (! empty ( $data ['voie_administration'] )) ? $data ['voie_administration'] : null;
		$this->frequence = (! empty ( $data ['frequence'] )) ? $data ['frequence'] : null;
		$this->dosage = (! empty ( $data ['dosage'] )) ? $data ['dosage'] : null;
		$this->date_application_recommandee = (! empty ( $data ['date_application_recommandee'] )) ? $data ['date_application_recommandee'] : null;
		$this->heure_recommandee = (! empty ( $data ['heure_recommandee'] )) ? $data ['heure_recommandee'] : null;
		$this->note = (! empty ( $data ['note'] )) ? $data ['note'] : null;
		$this->motif = (! empty ( $data ['motif'] )) ? $data ['motif'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->appliquer = (! empty ( $data ['appliquer'] )) ? $data ['appliquer'] : null;
		$this->date_application = (! empty ( $data ['date_application'] )) ? $data ['date_application'] : null;
		$this->note_application = (! empty ( $data ['note_application'] )) ? $data ['note_application'] : null;
		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
		$this->id_personne = (! empty ( $data ['id_personne'] )) ? $data ['id_personne'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
}