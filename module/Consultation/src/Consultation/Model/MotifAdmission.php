<?php
namespace Consultation\Model;

class MotifAdmission{
	public $id_motif;
	public $id_cons;
	public $libelle_motif;

	public function exchangeArray($data) {
		$this->id_motif = (! empty ( $data ['id_motif'] )) ? $data ['id_motif'] : null;
		$this->id_cons = (! empty ( $data ['id_cons'] )) ? $data ['id_cons'] : null;
		$this->libelle_motif = (! empty ( $data ['libelle_motif'] )) ? $data ['libelle_motif'] : null;
	}
}