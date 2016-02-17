<?php

namespace Consultation\Form;

use Zend\Form\Form;

class LibererPatientForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();
		
		$this->add ( array (
				'name' => 'id_demande_hospi',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_demande_hospi',
				)
		) );
		
		$this->add ( array (
				'name' => 'temoin_transfert',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'temoin_transfert',
				)
		) );
		
		$this->add ( array (
				'name' => 'id_cons',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_cons',
				)
		) );

		$this->add ( array (
				'name' => 'resumer_medical',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Résumer médical')
				),
				'attributes' => array (
						'id' => 'resumer_medical',
						'required' => true
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_sorti',
				'type' => 'Text',
				'options' => array (
						'label' => 'Motif de la sortie'
				),
				'attributes' => array (
						'id' => 'motif_sorti',
						'required' => true
				)
		) );
		
	}
}