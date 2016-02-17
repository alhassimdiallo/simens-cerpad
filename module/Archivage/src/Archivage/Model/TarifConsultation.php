<?php
namespace Archivage\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class TarifConsultation implements InputFilterAwareInterface{

	public $id_tarif_consultation;
	public $libelle;
	public $pasf_tn;
	public $pasf_tgp;
	public $imp;
	public $ipm_tn;
	public $ipm_tgp;
	public $id_service;

	public function exchangeArray($data) {
		$this->id_tarif_consultation = (! empty ( $data ['ID_TARIF_CONSULTATION'] )) ? $data ['ID_TARIF_CONSULTATION'] : null;
		$this->libelle = (! empty ( $data ['LIBELLE'] )) ? $data ['LIBELLE'] : null;
		$this->pasf_tn = (! empty ( $data ['PASF_TN'] )) ? $data ['PASF_TN'] : null;
		$this->pasf_tgp = (! empty ( $data ['PASF_TGP'] )) ? $data ['PASF_TGP'] : null;
		$this->imp = (! empty ( $data ['IMP'] )) ? $data ['IMP'] : null;
		$this->ipm_tn = (! empty ( $data ['IPM_TN'] )) ? $data ['IPM_TN'] : null;
		$this->ipm_tgp = (! empty ( $data ['IPM_TGP'] )) ? $data ['IPM_TGP'] : null;
		$this->id_service = (! empty ( $data ['ID_SERVICE'] )) ? $data ['ID_SERVICE'] : null;
	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
	public function getInputFilter() {
		if (! $this->inputFilter) {
			$inputFilter = new InputFilter ();
			$factory = new InputFactory ();

			$inputFilter->add ($factory->createInput( array (
					'name' => 'id_tarif_consultation',
					'filters' => array (
							array (
									'name' => 'Int'
							)
					)
			) ));
		}
	}
}