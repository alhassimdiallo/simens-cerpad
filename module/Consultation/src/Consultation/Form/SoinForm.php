<?php

namespace Consultation\Form;

use Zend\Form\Form;

class SoinForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();
		
		$this->add ( array (
				'name' => 'id_personne',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_personne',
				)
		) );
		
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
				'name' => 'date_cons',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'date_cons',
				)
		) );
		
		$this->add ( array (
				'name' => 'id_demande_hospi',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_demande_hospi',
				)
		) );
		
	
		//NOUVEAU CODE POUR LA GESTION DES PRESCRIPTIONS DE SOIN
		//NOUVEAU CODE POUR LA GESTION DES PRESCRIPTIONS DE SOIN
		//NOUVEAU CODE POUR LA GESTION DES PRESCRIPTIONS DE SOIN
		$this->add ( array (
				'name' => 'duree',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Durée (en jour)')
				),
				'attributes' => array (
						'id' => 'duree',
				)
		) );
		
		$this->add ( array (
				'name' => 'medicament',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Médicament')
				),
				'attributes' => array (
						'id' => 'medicament',
				)
		) );
		
		$this->add ( array (
				'name' => 'voie_administration',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Voie d\'administration')
				),
				'attributes' => array (
						'id' => 'voie_administration',
				)
		) );
		
		$this->add ( array (
				'name' => 'frequence',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Fréquence/jour')
				),
				'attributes' => array (
						'id' => 'frequence',
				)
		) );
		
		$this->add ( array (
				'name' => 'dosage',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Dosage')
				),
				'attributes' => array (
						'id' => 'dosage',
				)
		) );
		
		$this->add ( array (
				'name' => 'date_application',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Date de début d\'application')
				),
				'attributes' => array (
						'id' => 'date_application',
				)
		) );
		
		$this->add ( array (
				'name' => 'heure_recommandee_',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Heures recommandées'),
						'value_options' => array (
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
						'id' => 'heure_recommandee_',
						'class' => 'SlectBox',
						'multiple' => 'multiple',
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Motif')
				),
				'attributes' => array (
						'id' => 'motif_',
				)
		) );
		
		$this->add ( array (
				'name' => 'note_',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Note')
				),
				'attributes' => array (
						'id' => 'note_',
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
		/**
		 * ************************* CONSTANTES *****************************************************
		 */
		/**
		 * ************************* CONSTANTES *****************************************************
		 */
		/**
		 * ************************* CONSTANTES *****************************************************
		 */
		$this->add ( array (
				'name' => 'poids',
				'type' => 'Text',
				'options' => array (
						'label' => 'Poids (kg)'
				),
				'attributes' => array (
						'id' => 'poids'
				)
		) );
		$this->add ( array (
				'name' => 'taille',
				'type' => 'Text',
				'options' => array (
						'label' => 'Taille (cm)'
				),
				'attributes' => array (
						'id' => 'taille'
				)
		) );
		$this->add ( array (
				'name' => 'temperature',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Température (°C)' )
				),
				'attributes' => array (
						'id' => 'temperature'
				)
		) );
		
		$this->add ( array (
				'name' => 'pressionarterielle',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8', 'Pression artérielle (mmHg)')
				),
				'attributes' => array (
						'id' => 'pressionarterielle'
				)
		) );
		
		$this->add ( array (
				'name' => 'pouls',
				'type' => 'Text',
				'options' => array (
						'label' => 'Pouls (bat/min)'
				),
				'attributes' => array (
						'id' => 'pouls'
				)
		) );
		$this->add ( array (
				'name' => 'frequence_respiratoire',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Fréquence respiratoire')
				),
				'attributes' => array (
						'id' => 'frequence_respiratoire'
				)
		) );
		$this->add ( array (
				'name' => 'glycemie_capillaire',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8', 'Glycémie capillaire (g/l)')
				),
				'attributes' => array (
						'id' => 'glycemie_capillaire'
				)
		) );
		$this->add ( array (
				'name' => 'bu',
				'type' => 'Text',
				'options' => array (
						'label' => 'Bandelette urinaire'
				),
				'attributes' => array (
						'id' => 'bu'
				)
		) );
		
		/*** LES TYPES DE BANDELETTES URINAIRES ***/
		/*** LES TYPES DE BANDELETTES URINAIRES ***/
		/*** LES TYPES DE BANDELETTES URINAIRES ***/
		$this->add ( array (
				'name' => 'albumine',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'0' => 'â€“',
								'1' => '+',
						)
				),
				'attributes' => array (
						'id' => 'albumine',
		
				)
		) );
		$this->add ( array (
				'name' => 'croixalbumine',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
						)
				),
				'attributes' => array (
						'id' => 'croixalbumine',
		
				)
		) );
		
		
		$this->add ( array (
				'name' => 'sucre',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'0' => 'â€“',
								'1' => '+',
						)
				),
				'attributes' => array (
						'id' => 'sucre',
		
				)
		) );
		$this->add ( array (
				'name' => 'croixsucre',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
						)
				),
				'attributes' => array (
						'id' => 'croixsucre',
		
				)
		) );
		
		
		
		$this->add ( array (
				'name' => 'corpscetonique',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'0' => 'â€“',
								'1' => '+',
						)
				),
				'attributes' => array (
						'id' => 'corpscetonique',
		
				)
		) );
		$this->add ( array (
				'name' => 'croixcorpscetonique',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
						)
				),
				'attributes' => array (
						'id' => 'croixcorpscetonique',
						'class' => 'croixcorpscetonique',
		
				)
		) );
		/*** FIN LES TYPES DE BANDELETTES URINAIRES ***/
		/*** FIN LES TYPES DE BANDELETTES URINAIRES ***/
		
		
		/*********DONNEES DE L EXAMEN PHYSIQUE***********/
		/*********DONNEES DE L EXAMEN PHYSIQUE***********/
		$this->add ( array (
				'name' => 'examen_donnee1',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','donnée 1')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'examen_donnee1'
				)
		) );
		$this->add ( array (
				'name' => 'examen_donnee2',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','donnée 2')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'examen_donnee2'
				)
		) );
		$this->add ( array (
				'name' => 'examen_donnee3',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','donnée 3')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'examen_donnee3'
				)
		) );
		$this->add ( array (
				'name' => 'examen_donnee4',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','donnée 4')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'examen_donnee4'
				)
		) );
		$this->add ( array (
				'name' => 'examen_donnee5',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','donnée 5')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'examen_donnee5'
				)
		) );
		
		/*********FIN DONNEES DE L EXAMEN PHYSIQUE***********/
		/*********FIN DONNEES DE L EXAMEN PHYSIQUE***********/
		
		
		/*************** TRANSFERT *************/
		/*************** TRANSFERT *************/
		$this->add ( array (
				'name' => 'hopital_accueil',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Hopital d\'accueil :' ),
				),
				'attributes' => array (
						'registerInArrrayValidator' => false,
						'onchange' => 'getservices(this.value)',
						'id' => 'hopital_accueil'
				)
		) );
		$this->add ( array (
				'name' => 'service_accueil',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Service d\'accueil :' )
				),
				'attributes' => array (
						'registerInArrrayValidator' => false,
						'id' => 'service_accueil'
				)
		) );
		$this->add ( array (
				'name' => 'motif_transfert',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif du transfert :'
				),
				'attributes' => array (
						'id' => 'motif_transfert'
				)
		) );
		/*************** FIN TRANSFERT *************/
		/*************** FIN TRANSFERT *************/
		
		
		/********** LES MOTIFS D ADMISSION **********/
		/********** LES MOTIFS D ADMISSION *********/
		$this->add ( array (
				'name' => 'motif_admission1',
				'type' => 'Text',
				'options' => array (
						'label' => 'plainte 1'
				),
				'attributes' => array (
						'id' => 'motif_admission1'
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission2',
				'type' => 'Text',
				'options' => array (
						'label' => 'plainte 2'
				),
				'attributes' => array (
						'id' => 'motif_admission2'
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission3',
				'type' => 'Text',
				'options' => array (
						'label' => 'plainte 3'
				),
				'attributes' => array (
						'id' => 'motif_admission3'
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission4',
				'type' => 'Text',
				'options' => array (
						'label' => 'plainte 4'
				),
				'attributes' => array (
						'id' => 'motif_admission4'
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission5',
				'type' => 'Text',
				'options' => array (
						'label' => 'plainte 5'
				),
				'attributes' => array (
						'id' => 'motif_admission5'
				)
		) );
		
		/********** FIN LES MOTIFS D ADMISSION **********/
		/********** FIN LES MOTIFS D ADMISSION *********/
	}
}