<?php

namespace Facturation\Form;

use Zend\Form\Form;

class AjoutNaissanceForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();
		$this->add ( array (
				'name' => 'ID_PERSONNE',
				'type' => 'Hidden',
				'attributes' =>  array (
						'id' => 'ID_PERSONNE',
				) 
				));

		$this->add ( array (
				'name' => 'NOM',
				'type' => 'Text',
				'options' => array (
						'label' => 'Nom'
				),
				'attributes' => array (
						'class' => 'only_Char',
						'id' => 'NOM',
						'required' => true,
				)
		) );
		$this->add ( array (
				'name' => 'PRENOM',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Prénom')
				),
				'attributes' => array (
						'id' => 'PRENOM',
						'class' => 'only_Char',
						'required' => true,
				)
		) );
		$this->add ( array (
				'name' => 'SEXE',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Sexe',
						'value_options' => array (
								'' => '',
								'Masculin'=>'Masculin',
								iconv ( 'ISO-8859-1', 'UTF-8','Féminin')=> iconv ( 'ISO-8859-1', 'UTF-8','Féminin')
						) 
				),
				'attributes' => array (
						'required' => true,
						'id' => 'SEXE'
				)
		) );
		$this->add ( array (
				'name' => 'DATE_NAISSANCE',
				'type' => 'Text',
				'options' => array (
						'label' => 'Date de naissance'
				),
				'attributes' => array (
						'required' => true,
						'id' => 'DATE_NAISSANCE'
				)
		) );
		$this->add ( array (
				'name' => 'HEURE_NAISSANCE',
				'type' => 'Text',
				'options' => array (
						'label' => 'Heure de naissance'
				),
				'attributes' => array (
						'required' => true,
						'id' => 'HEURE_NAISSANCE'
				)
		) );
		$this->add ( array (
				'name' => 'LIEU_NAISSANCE',
				'type' => 'Text',
				'options' => array (
						'label' => 'Lieu de naissance'
				),
				'attributes' => array (
						'required' => true,
						'id' => 'LIEU_NAISSANCE'
				)
		) );
		$this->add ( array (
				'name' => 'NATIONALITE_ORIGINE',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Nationalité origine'),
						'value_options' => array (
								''=>''
						)
				),
				'attributes' => array (
						'required' => true,
						'id' => 'NATIONALITE_ORIGINE'
				)
		) );
		$this->add ( array (
				'name' => 'NATIONALITE_ACTUELLE',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Nationalité actuelle'),
						'value_options' => array (
								''=>''
						)
				),
				'attributes' => array (
						'required' => true,
						'id' => 'NATIONALITE_ACTUELLE'
				)
		) );
		$this->add ( array (
				'name' => 'ADRESSE',
				'type' => 'Text',
				'options' => array (
						'label' => 'Adresse'
				),
				'attributes' => array (
						'id' => 'ADRESSE'
				)
		) );
		$this->add ( array (
				'name' => 'TAILLE',
				'type' => 'Text',
				'options' => array (
						'label' => 'Taille (cm)'
				),
				'attributes'=> array (
						'id' => 'TAILLE'
				)
		) );
		$this->add(array(
				'name' => 'POIDS',
				'type' => 'Text',
				'options' => array(
						'label' => 'Poids (kg)'
				),
				'attributes'=> array (
						'id' => 'POIDS'
				)
		));
		$this->add ( array (
				'name' => 'GROUPE_SANGUIN',
				'type' => 'Text',
				'options' => array (
						'label' => 'Groupe sanguin'
				),
				'attributes'=> array (
						'id' => 'GROUPE_SANGUIN'
				)
		) );
		$this->add(array(
				'name' => 'submit',
				'type' => 'Submit',
				'options' => array(
						'label' => 'Sauvegarder'
				),

		));
	}
}