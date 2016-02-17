<?php
namespace Consultation\Model;

class DonneesExamensPhysiques{
	public $code_examen;
	public $id_cons;
	public $libelle_examen;

	public function exchangeArray($data) {
		$this->code_examen = (! empty ( $data ['code_examen'] )) ? $data ['code_examen'] : null;
		$this->id_cons = (! empty ( $data ['idcons'] )) ? $data ['idcons'] : null;
		$this->libelle_examen = (! empty ( $data ['libelle_examen'] )) ? $data ['libelle_examen'] : null;
	}
}