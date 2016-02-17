<?php

namespace Consultation\Form;

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
				'name' => 'medicament_m',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Médicament')
				),
				'attributes' => array (
						'id' => 'medicament_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'voie_administration_m',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Voie d\'administration')
				),
				'attributes' => array (
						'id' => 'voie_administration_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'frequence_m',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Fréquence/jour')
				),
				'attributes' => array (
						'id' => 'frequence_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'dosage_m',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Dosage')
				),
				'attributes' => array (
						'id' => 'dosage_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'date_application_m',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Date de début d\'application')
				),
				'attributes' => array (
						'id' => 'date_application_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'heure_recommandee_m',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Heures recommandées'),
						'value_options' =>  array (
								'01:00' => '01:00',
								'02:00' => '02:00',
								'03:00' => '03:00',
								'04:00' => '04:00',
								'05:00' => '05:00',
								'06:00' => '06:00',
								'07:00' => '07:00',
								'08:00' => '08:00',
								'09:00' => '09:00',
								'10:00' => '10:00',
								'11:00' => '11:00',
								'12:00' => '12:00',
								'13:00' => '13:00',
								'14:00' => '14:00',
								'15:00' => '15:00',
								'16:00' => '16:00',
								'17:00' => '17:00',
								'18:00' => '18:00',
								'19:00' => '19:00',
								'20:00' => '20:00',
								'21:00' => '21:00',
								'22:00' => '22:00',
								'23:00' => '23:00',
								'24:00' => '24:00',
						)
				),
				'attributes' => array (
						'id' => 'heure_recommandee_m',
						'class' => 'SlectBox_m',
						'multiple' => 'multiple',
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_m',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Motif')
				),
				'attributes' => array (
						'id' => 'motif_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'note_m',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Note')
				),
				'attributes' => array (
						'id' => 'note_m',
				)
		) );
		
		$this->add ( array (
				'name' => 'duree_m',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Durée (en jour)')
				),
				'attributes' => array (
						'id' => 'duree_m',
				)
		) );
	}
}