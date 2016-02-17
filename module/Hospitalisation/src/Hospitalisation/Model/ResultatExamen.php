<?php

namespace Hospitalisation\Model;

/**
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 * @author hassim
 *
 */
class ResultatExamen {
	
	public $idResultat;
	public $idDemande;
	public $techniqueUtiliser;
	public $noteResultat;
	public $conclusion;
	public $envoyer;
	public $id_personne;
	public $date_enregistrement;
	public $date_modification;
	
	public function exchangeArray($data) {
 			$this->idResultat = (! empty ( $data ['idResultat'] )) ? $data ['idResultat'] : null;
 			$this->idDemande = (! empty ( $data ['idDemande'] )) ? $data ['idDemande'] : null;
 			$this->techniqueUtiliser = (! empty ( $data ['techniqueUtiliser'] )) ? $data ['techniqueUtiliser'] : null;
 			$this->noteResultat = (! empty ( $data ['noteResultat'] )) ? $data ['noteResultat'] : null;
 			$this->conclusion = (! empty ( $data ['conclusion'] )) ? $data ['conclusion'] : null;
 			$this->envoyer = (! empty ( $data ['envoyer'] )) ? $data ['envoyer'] : null;
 			$this->id_personne = (! empty ( $data ['id_personne'] )) ? $data ['id_personne'] : null;
 			$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
 			$this->date_modification = (! empty ( $data ['date_modifcation'] )) ? $data ['date_modifcation'] : null;
	}
	
}