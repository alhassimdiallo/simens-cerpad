<?php
namespace Facturation\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Deces implements InputFilterAwareInterface{
	public $id_deces;
	public $date_deces;
	public $heure_deces;
	public $age_deces;
	public $lieu_deces;
	public $circonstances_deces;
	public $id_patient;
	public $date_enregistrement;
	public $note;

	public function exchangeArray($data) {
		$this->id_deces = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->date_deces = (! empty ( $data ['date_deces'] )) ? $data ['date_deces'] : null;
		$this->heure_deces = (! empty ( $data ['heure_deces'] )) ? $data ['heure_deces'] : null;
		$this->age_deces = (! empty ( $data ['age_deces'] )) ? $data ['age_deces'] : null;
		$this->lieu_deces = (! empty ( $data ['lieu_deces'] )) ? $data ['lieu_deces'] : null;
		$this->circonstances_deces = (! empty ( $data ['circonstances_deces'] )) ? $data ['circonstances_deces'] : null;
		$this->id_patient = (! empty ( $data ['id_patient'] )) ? $data ['id_patient'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->note = (! empty ( $data ['note'] )) ? $data ['note'] : null;
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
				//$factory = new InputFactory ();

				$inputFilter->add ( array (
						'name' => 'id_deces',
						'required' => true,
						'filters' => array (
								array (
										'name' => 'Int'
								)
						)
				) );

				$this->inputFilter = $inputFilter;
			}

			return $this->inputFilter;
		}
}