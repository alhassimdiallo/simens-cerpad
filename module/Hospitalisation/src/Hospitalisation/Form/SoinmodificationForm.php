<?php

namespace Hospitalisation\Form;

use Zend\Form\Form;

class SoinmodificationForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();
		
		$this->add ( array (
				'name' => 'id_sh_m',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_sh_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'id_hosp_m',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_hosp_m',
				)
		) );

		$this->add ( array (
				'name' => 'id_soins_m',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Soin',
				),
				'attributes' => array (
						'id' => 'id_soins_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'duree_m',
				'type' => 'Text',
				'options' => array (
						'label' => 'Duree (en jour)'
				),
				'attributes' => array (
						'id' => 'duree_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'date_recommandee_m',
				'type' => 'Text',
				'options' => array (
						'label' => 'Date recommandee'
				),
				'attributes' => array (
						'id' => 'date_recommandee_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'heure_recommandee_m',
				'type' => 'Text',
				'options' => array (
						'label' => 'Heure recommandee'
				),
				'attributes' => array (
						'id' => 'heure_recommandee_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'note_m',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Note'
				),
				'attributes' => array (
						'id' => 'note_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_m',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif'
				),
				'attributes' => array (
						'id' => 'motif_m',
				)
		) );
		
	}
}