<?php

namespace Hospitalisation\Form;

use Zend\Form\Form;

class AppliquerExamenForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();
		
		$this->add ( array (
				'name' => 'Examen_id_cons',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'Examen_id_cons',
				)
		) );
		
		$this->add ( array (
				'name' => 'idDemande',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'idDemande',
				)
		) );
		
		$this->add ( array (
				'name' => 'fichier',
				'type' => 'File',
				'options' => array (
						'label' => 'Image jointe',
				),
				'attributes' => array (
						'id' => 'fichier',
				)
		) );

		$this->add ( array (
				'name' => 'technique_utilise',
				'type' => 'Text',
				'options' => array (
						'label' => 'Technique utilisee',
				),
				'attributes' => array (
						'id' => 'technique_utilise',
				)
		) );
		
		$this->add ( array (
				'name' => 'resultat',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Resultat',
				),
				'attributes' => array (
						'id' => 'resultat',
				)
		) );
		
		$this->add ( array (
				'name' => 'conclusion',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Conslusion',
				),
				'attributes' => array (
						'id' => 'conclusion',
				)
		) );
		
	}
}