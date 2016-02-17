<?php
namespace Pharmacie\Form;

use Zend\Form\Form;

class MedicamentForm extends Form {

	public function __construct() {

		parent::__construct();

		$this->add ( array (
				'name' => 'id_medicament',
				'type' => 'Hidden'
		) );
		$this->add ( array (
				'name' => 'indication_therapeutique',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Indication Thérapeutique')
				)
		) );
		$this->add ( array (
				'name' => 'mise_en_garde',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Mise en garde')
				)
		) );
		$this->add ( array (
				'name' => 'fabricant',
				'type' => 'Text',
				'options' => array (
						'label' => 'Fabricant'
				)
		) );
		$this->add ( array (
				'name' => 'adresse_fabricant',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Adresse fabricant'
				)
		) );
		$this->add ( array (
				'name' => 'composition',
				'type' => 'Text',
				'options' => array (
						'label' => 'Composition'
				)
		) );
		$this->add ( array (
				'name' => 'excipient_notoire',
				'type' => 'Text',
				'options' => array (
						'label' => 'Excipient notoire'
				)
		) );
		$this->add ( array (
				'name' => 'voie_administration',
				'type' => 'Text',
				'options' => array (
						'label' => 'Voie administration'
				)
		) );
		$this->add ( array (
				'name' => 'intitule',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Intitulé')
				)
		) );
		$this->add ( array (
				'name' => 'description',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Description'
				)
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