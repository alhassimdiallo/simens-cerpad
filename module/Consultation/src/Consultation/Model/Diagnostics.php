<?php
namespace Consultation\Model;

class Diagnostics{
	public $code_diagnostics;
	public $id_cons;
	public $libelle_diagnostics;

	public function exchangeArray($data) {
		$this->code_diagnostics = (! empty ( $data ['code_diagnostics'] )) ? $data ['code_diagnostics'] : null;
		$this->id_cons = (! empty ( $data ['idcons'] )) ? $data ['idcons'] : null;
		$this->libelle_diagnostics = (! empty ( $data ['libelle_diagnostics'] )) ? $data ['libelle_diagnostics'] : null;
	}
}