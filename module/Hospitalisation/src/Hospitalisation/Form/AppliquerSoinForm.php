<?php

namespace Hospitalisation\Form;

use Zend\Form\Form;

class AppliquerSoinForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();
		
		$this->add ( array (
				'name' => 'id_sh',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_sh',
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
				'name' => 'heure_suivante',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'heure_suivante',
				)
		) );

		$this->add ( array (
				'name' => 'note',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Note',
				),
				'attributes' => array (
						'id' => 'note',
				)
		) );
		
		
		$this->add ( array (
				'name' => 'play',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'play',
				)
		) );
	}
}