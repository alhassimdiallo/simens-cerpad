<?php
namespace Archivage\Form;

use Zend\Form\Form;
// use Personnel\Model\Service;
// use Personnel\Model\ServiceTable;

class AdmissionForm extends Form{

	public function __construct() {
		parent::__construct ();

		$this->add ( array (
				'name' => 'id_patient',
				'type' => 'Hidden',
				'attributes' => array(
						'id' => 'id_patient'
				)
		) );

		$this->add ( array (
				'name' => 'service',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Service'),
						'value_options' => array (
								''=>''
						)
				),
				'attributes' => array (
						'registerInArrrayValidator' => true,
						'onchange' => 'getmontant(this.value)',
						'id' =>'service',
						'required' => true,
				)
		) );

		$this->add ( array (
				'name' => 'montant',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Montant')
				),
				'attributes' => array (
						'id' => 'montant',
				)
		) );

		$this->add ( array (
				'name' => 'numero',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Numero facture')
				),
				'attributes' => array (
						'id' => 'numero'
				)
		) );
		
		$this->add ( array (
				'name' => 'date',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Date d\'admission')
				),
				'attributes' => array (
						'id' => 'date',
						'required' => true,
				)
		) );
		
		$this->add ( array (
				'name' => 'liste_service',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								''=>''
						)
				),
				'attributes' => array (
						'id' => 'liste_service',
				)
		) );
	}
}