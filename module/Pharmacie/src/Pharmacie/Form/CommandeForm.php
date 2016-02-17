<?php
namespace Pharmacie\Form;

use Zend\Form\Form;

class CommandeForm extends Form {

	public function __construct() {
		parent::__construct();

		$this->add ( array (
				'name' => 'id_medicament',
				'type' => 'Hidden',
				'attributes' => array (
						'class' => 'listeSelect'
				)
		) );
		$this->add ( array (
				'name' => 'intitule',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								''=>''
						)
				),
				'attributes' => array (
						'registerInArrrayValidator' => true,
						'onchange' => 'getMedicament(this.value)'
				)
		) );
		$this->add ( array (
				'name' => 'quantite',
				'type' => 'Text'
		) );
		$this->add ( array (
				'name' => 'prix_unitaire',
				'type' => 'Text'
		) );
		$this->add ( array (
				'name' => 'submit',
				'type' => 'Submit',
				'options' => array (
						'label' => 'Sauvegarder'
				)
		) );
	}
}