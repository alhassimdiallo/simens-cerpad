<?php
namespace Consultation\Model;

class RvPatientCons {
	public $id_cons;
	public $note;
	public $heure;
	public $date;

	public function exchangeArray($data) {
		$this->id_cons = (! empty ( $data ['ID_CONS'] )) ? $data ['ID_CONS'] : null;
		$this->note = (! empty ( $data ['NOTE'] )) ? $data ['NOTE'] : null;
		$this->heure = (! empty ( $data ['HEURE'] )) ? $data ['HEURE'] : null;
		$this->date = (! empty ( $data ['DATE'] )) ? $data ['DATE'] : null;
	}
}