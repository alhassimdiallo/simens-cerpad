<?php
namespace Consultation\Model;

class DemandeVisitePreanesthesique{
	public $idVpa;
	public $id_cons;
	public $diagnostic;
	public $observation;
	public $intervention_prevue;
	public $date_enregistrement;
	public $date_modification;

	public function exchangeArray($data) {
		$this->idVpa = (! empty ( $data ['idVpa'] )) ? $data ['idVpa'] : null;
		$this->id_cons = (! empty ( $data ['ID_CONS'] )) ? $data ['ID_CONS'] : null;
		$this->diagnostic = (! empty ( $data ['DIAGNOSTIC'] )) ? $data ['DIAGNOSTIC'] : null;
		$this->observation = (! empty ( $data ['OBSERVATION'] )) ? $data ['OBSERVATION'] : null;
		$this->intervention_prevue = (! empty ( $data ['INTERVENTION_PREVUE'] )) ? $data ['INTERVENTION_PREVUE'] : null;
		$this->date_enregistrement = (! empty ( $data ['DATE_ENREGISTREMENT'] )) ? $data ['DATE_ENREGISTREMENT'] : null;
		$this->date_modification = (! empty ( $data ['DATE_MODIFICATION'] )) ? $data ['DATE_MODIFICATION'] : null;
	}
}