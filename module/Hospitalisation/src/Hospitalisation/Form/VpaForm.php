<?php

namespace Hospitalisation\Form;

use Zend\Form\Form;

class VpaForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();
		
		$this->add ( array (
				'name' => 'idPersonne',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'idPersonne',
				)
		) );
		
		$this->add ( array (
				'name' => 'idVpa',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'idVpa',
				)
		) );
		
		$this->add ( array (
				'name' => 'numero_vpa',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Numéro VPA') ,
				),
				'attributes' => array (
						'id' => 'numero_vpa',
						'required' => true
				)
		) );
		
		$this->add ( array (
				'name' => 'type_intervention',
				'type' => 'Text',
				'options' => array (
						'label' => 'Type intervention',
				),
				'attributes' => array (
						'id' => 'type_intervention',
						'required' => true
				)
		) );
		
		$this->add ( array (
				'name' => 'aptitude',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'0' => 'Non',
								'1' => 'Oui',
						)
				),
				'attributes' => array (
						'id' => 'aptitude',
						'required' => true
				)
		) );
		
	}
}