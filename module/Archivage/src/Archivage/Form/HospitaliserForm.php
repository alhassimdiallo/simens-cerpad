<?php

namespace Archivage\Form;

use Zend\Form\Form;

class HospitaliserForm extends Form {
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
				'name' => 'division',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Batiment',
				),
				'attributes' => array (
						'onchange' => 'getsalle(this.value)',
						'id' => 'division',
						'required' => true
				)
		) );
		
		$this->add ( array (
				'name' => 'salle',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Salle'
				),
				'attributes' => array (
						'onchange' => 'getlit(this.value)',
						'id' => 'salle',
						'required' => true
				)
		) );
		
		$this->add ( array (
				'name' => 'lit',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Lit'
				),
				'attributes' => array (
						'id' => 'lit',
						'required' => true
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