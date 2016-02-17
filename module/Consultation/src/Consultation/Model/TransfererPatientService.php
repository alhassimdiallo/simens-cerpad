<?php
namespace Consultation\Model;

class TransfererPatientService{
	public $id_service;
	public $id_medecin;
	public $date;
	public $motif_transfert;
	public $id_cons;

	public function exchangeArray($data) {
		$this->id_service = (! empty ( $data ['ID_SERVICE'] )) ? $data ['ID_SERVICE'] : null;
		$this->id_medecin = (! empty ( $data ['ID_MEDECIN'] )) ? $data ['ID_MEDECIN'] : null;
		$this->date = (! empty ( $data ['DATE'] )) ? $data ['DATE'] : null;
		$this->motif_transfert = (! empty ( $data ['MOTIF_TRANSFERT'] )) ? $data ['MOTIF_TRANSFERT'] : null;
		$this->id_cons = (! empty ( $data ['ID_CONS'] )) ? $data ['ID_CONS'] : null;
	}
}