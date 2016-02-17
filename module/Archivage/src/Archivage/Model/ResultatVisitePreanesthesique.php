<?php
namespace Archivage\Model;

class ResultatVisitePreanesthesique{
	public $id;
	public $numeroVpa;
	public $typeIntervention;
	public $aptitude;
	public $idVpa;
	public $id_personne;

	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->numeroVpa = (! empty ( $data ['numeroVpa'] )) ? $data ['numeroVpa'] : null;
		$this->typeIntervention = (! empty ( $data ['typeIntervention'] )) ? $data ['typeIntervention'] : null;
		$this->aptitude = (! empty ( $data ['aptitude'] )) ? $data ['aptitude'] : null;
		$this->idVpa = (! empty ( $data ['idVpa'] )) ? $data ['idVpa'] : null;
		$this->id_personne = (! empty ( $data ['id_personne'] )) ? $data ['id_personne'] : null;
	}
}