<?php

namespace Hospitalisation\Form;

use Zend\Form\Form;

class LibererPatientMajorForm extends Form {
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
				'name' => 'id_hosp',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_hosp',
				)
		) );
		
		$this->add ( array (
				'name' => 'id_lit',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_lit',
				)
		) );
		
		$this->add ( array (
				'name' => 'liberer_lit',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'liberer_lit',
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
						//'required' => true,
				)
		) );
		
	}
}