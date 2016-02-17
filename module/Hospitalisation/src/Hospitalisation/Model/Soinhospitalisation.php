<?php

namespace Hospitalisation\Model;

/**
 * POUR LA RECUPERATION DES DONNEES DU FORMULAIRE
 * @author hassim
 *
 */
class Soinhospitalisation {
	
	public $id_sh;
	public $id_hosp;
	public $medicament;
	public $voie_administration;
	public $frequence;
	public $dosage;
	public $date_debut_application;
	public $duree;
	public $note;
	public $motif;
	public $date_enregistrement;
	public $appliquer;
	public $date_application;
	public $date_modifcation_medecin;
	public $id_personne;
	
	public function exchangeArray($data) {
		$this->id_sh = (! empty ( $data ['id_sh'] )) ? $data ['id_sh'] : null;
		$this->id_hosp = (! empty ( $data ['id_hosp'] )) ? $data ['id_hosp'] : null;
		$this->medicament = (! empty ( $data ['medicament'] )) ? $data ['medicament'] : null;
		$this->voie_administration = (! empty ( $data ['voie_administration'] )) ? $data ['voie_administration'] : null;
		$this->frequence = (! empty ( $data ['frequence'] )) ? $data ['frequence'] : null;
		$this->dosage = (! empty ( $data ['dosage'] )) ? $data ['dosage'] : null;
		$this->date_debut_application = (! empty ( $data ['date_debut_application'] )) ? $data ['date_debut_application'] : null;
		$this->duree = (! empty ( $data ['duree'] )) ? $data ['duree'] : null;
		$this->note = (! empty ( $data ['note'] )) ? $data ['note'] : null;
		$this->motif = (! empty ( $data ['motif'] )) ? $data ['motif'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->appliquer = (! empty ( $data ['appliquer'] )) ? $data ['appliquer'] : null;
		$this->date_application = (! empty ( $data ['date_application'] )) ? $data ['date_application'] : null;
		$this->date_modifcation_medecin = (! empty ( $data ['date_modifcation_medecin'] )) ? $data ['date_modifcation_medecin'] : null;
		$this->id_personne = (! empty ( $data ['id_personne'] )) ? $data ['id_personne'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	}