<?php

namespace Hospitalisation\Form;

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
				'name' => 'resumer_medical',
				'type' => 'Text',
				'options' => array (
						'label' => 'Resumer medical'
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
		
		$this->add ( array (
				'name' => 'note',
				'type' => 'Text',
				'options' => array (
						'label' => 'Note'
				),
				'attributes' => array (
						'id' => 'note',
				)
		) );
		
	}
}