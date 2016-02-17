<?php

namespace Hospitalisation\Form;

use Zend\Form\Form;

class HospitaliserPourGestionLitsForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();

		$this->add ( array (
				'name' => 'id_personne',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_personne'
				)
		) );
		
		$this->add ( array (
				'name' => 'code_demande',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'code_demande'
				)
		) );
		
		$this->add ( array (
				'name' => 'id_lit',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_lit'
				)
		) );
		
		$this->add ( array (
				'name' => 'division',
				'type' => 'text',
				'options' => array (
						'label' => 'Batiment',
				),
				'attributes' => array (
						'id' => 'division',
				)
		) );
		
		$this->add ( array (
				'name' => 'salle',
				'type' => 'text',
				'options' => array (
						'label' => 'Salle'
				),
				'attributes' => array (
						'id' => 'salle',
				)
		) );
		
		$this->add ( array (
				'name' => 'lit',
				'type' => 'text',
				'options' => array (
						'label' => 'Lit'
				),
				'attributes' => array (
						'id' => 'lit',
				)
		) );
		
		$this->add( array(
				'name' => 'valider',
				'type' => 'Submit',
				'attributes' => array(
						'value' => 'Valider',
						'id' => 'valider',
				),
		));
		
		$this->add( array(
				'name' => 'annuler',
				'type' => 'Button',
				'attributes' => array(
						'value' => 'Annuler',
						'id' => 'annuler',
				),
		));
		
	}
}