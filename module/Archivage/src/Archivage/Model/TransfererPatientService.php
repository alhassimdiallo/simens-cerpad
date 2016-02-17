<?php
namespace Archivage\Model;

class TransfererPatientService{
	public $id_service;
	public $id_personne;
	public $med_id_personne;
	public $date;
	public $motif_transfert;
	public $id_cons;

	public function exchangeArray($data) {
		$this->id_service = (! empty ( $data ['ID_SERVICE'] )) ? $data ['ID_SERVICE'] : null;
		$this->id_personne = (! empty ( $data ['ID_PERSONNE'] )) ? $data ['ID_PERSONNE'] : null;
		$this->med_id_personne = (! empty ( $data ['MED_ID_PERSONNE'] )) ? $data ['MED_ID_PERSONNE'] : null;
		$this->date = (! empty ( $data ['DATE'] )) ? $data ['DATE'] : null;
		$this->motif_transfert = (! empty ( $data ['motif_transfert'] )) ? $data ['motif_transfert'] : null;
		$this->id_cons = (! empty ( $data ['ID_CONS'] )) ? $data ['ID_CONS'] : null;
	}
}