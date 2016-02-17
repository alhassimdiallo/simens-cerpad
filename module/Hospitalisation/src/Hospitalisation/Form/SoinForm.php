<?php

namespace Hospitalisation\Form;

use Zend\Form\Form;

class SoinForm extends Form {
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
				'name' => 'id_soins',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Soin',
				),
				'attributes' => array (
						'id' => 'id_soins',
				)
		) );
		
		$this->add ( array (
				'name' => 'duree',
				'type' => 'Text',
				'options' => array (
						'label' => 'Duree (en jour)'
				),
				'attributes' => array (
						'id' => 'duree',
				)
		) );
		
		$this->add ( array (
				'name' => 'date_recommandee',
				'type' => 'Text',
				'options' => array (
						'label' => 'Date recommandee'
				),
				'attributes' => array (
						'id' => 'date_recommandee',
				)
		) );
		
		$this->add ( array (
				'name' => 'heure_recommandee',
				'type' => 'Text',
				'options' => array (
						'label' => 'Heure recommandee'
				),
				'attributes' => array (
						'id' => 'heure_recommandee',
				)
		) );
		
		$this->add ( array (
				'name' => 'note',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Note'
				),
				'attributes' => array (
						'id' => 'note',
				)
		) );
		
		$this->add ( array (
				'name' => 'motif',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif'
				),
				'attributes' => array (
						'id' => 'motif',
				)
		) );
		
		
		$this->add( array(
				'name' => 'terminersoin',
				'type' => 'Button',
				'attributes' => array(
						'value' => 'Terminer',
						'id' => 'terminersoin',
				),
		));
		
	}
}