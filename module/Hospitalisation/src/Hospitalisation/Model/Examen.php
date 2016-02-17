<?php

namespace Hospitalisation\Model;

/**
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 * @author hassim
 *
 */
class Examen {
	
	public $idExamen;
	public $libelleExamen;
	public $idType;
	
	public function exchangeArray($data) {
 			$this->idExamen = (! empty ( $data ['idExamen'] )) ? $data ['idExamen'] : null;
 			$this->libelleExamen = (! empty ( $data ['libelleExamen'] )) ? $data ['libelleExamen'] : null;
 			$this->idType = (! empty ( $data ['idType'] )) ? $data ['idType'] : null;
	}
	
}