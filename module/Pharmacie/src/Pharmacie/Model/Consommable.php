<?php
namespace Pharmacie\Model;

class Consommable {
	public $id_materiel;
	public $indication_therapeutique;
	public $mise_en_garde;
	public $fabriquant;
	public $adresse_fabriquant;
	public $composition;
	public $excipient_notoire;
	public $voie_administration;
	public $intitule;
	public $description;
	public $image;
	public $prix;

	public function exchangeArray($data){
		$this->id_materiel = (! empty ( $data ['ID_MATERIEL'] )) ? $data ['ID_MATERIEL'] : null;
		$this->indication_therapeutique = (! empty ( $data ['INDICATION_THERAPEUTIQUE'] )) ? $data ['INDICATION_THERAPEUTIQUE'] : null;
		$this->mise_en_garde = (! empty ( $data ['MISE_EN_GARDE'] )) ? $data ['MISE_EN_GARDE'] : null;
		$this->fabriquant = (! empty ( $data ['FABRIQUANT'] )) ? $data ['FABRIQUANT'] : null;
		$this->adresse_fabriquant = (! empty ( $data ['ADRESSE_FABRIQUANT'] )) ? $data ['ADRESSE_FABRIQUANT'] : null;
		$this->composition = (! empty ( $data ['COMPOSITION'] )) ? $data ['COMPOSITION'] : null;
		$this->excipient_notoire = (! empty ( $data ['EXCIPIENT_NOTOIRE'] )) ? $data ['EXCIPIENT_NOTOIRE'] : null;
		$this->voie_administration = (! empty ( $data ['VOIE_ADMINISTRATION'] )) ? $data ['VOIE_ADMINISTRATION'] : null;
		$this->intitule = (! empty ( $data ['INTITULE'] )) ? $data ['INTITULE'] : null;
		$this->description = (! empty ( $data ['DESCRIPTION'] )) ? $data ['DESCRIPTION'] : null;
		$this->image = (! empty ( $data ['IMAGE'] )) ? $data ['IMAGE'] : null;
		$this->prix = (! empty ( $data ['PRIX'] )) ? $data ['PRIX'] : null;
	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
}