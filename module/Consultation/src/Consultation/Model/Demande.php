<?php
namespace Consultation\Model;

class Demande{

	public $idDemande;
	public $dateDemande;
	public $noteDemande;
	public $idCons;
	public $idExamen;
	public $appliquer;
	
	public function exchangeArray($data) {
 			$this->idDemande = (! empty ( $data ['idDemande'] )) ? $data ['idDemande'] : null;
 			$this->dateDemande = (! empty ( $data ['dateDemande'] )) ? $data ['dateDemande'] : null;
 			$this->noteDemande = (! empty ( $data ['noteDemande'] )) ? $data ['noteDemande'] : null;
 			$this->idCons = (! empty ( $data ['idCons'] )) ? $data ['idCons'] : null;
 			$this->idExamen = (! empty ( $data ['idExamen'] )) ? $data ['idExamen'] : null;
 			$this->appliquer = (! empty ( $data ['appliquer'] )) ? $data ['appliquer'] : null;
	}
}