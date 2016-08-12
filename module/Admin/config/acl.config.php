<?php
return array(
    'acl' => array(
    		
        'roles' => array(
        		'guest'   => null,
        		'infirmier' => 'guest',
        		'admin' => 'guest',
        		'radiologie' => 'guest',
        		'anesthesie' => 'guest',
        		'major' => 'guest',
        		'facturation' => 'guest',
        		'etat_civil' => 'guest',
        		'archivage' => 'guest',
        		'surveillant' => 'guest',
        		'medecin'     => 'surveillant',
        		'superAdmin'  => 'medecin',
        		
        		
        		
        		//***Cerpad
        		//***Cerpad
        		'secretariat' => 'guest',
        		'laborantin' => 'guest',
        		//***************
        		//***************

        ),
    		

    		'resources' => array(
    		
    				'allow' => array(
    						
    						/***
    						 * AdminController
    						 */
    						
    						'Admin\Controller\Admin' => array(
    								'login' => 'guest',
    								'logout' => 'guest',
    								'bienvenue' => 'guest',
    								'modifier-password' => 'guest',
    								'verifier-password' => 'guest',
    								'mise-a-jour-user-password' => 'guest',
    								
    								'utilisateurs' => array('admin','superAdmin'),
    								'liste-utilisateurs-ajax' => array('admin','superAdmin'),
    								'modifier-utilisateur' => array('admin','superAdmin'),
    								'liste-agent-personnel-ajax' => array('admin','superAdmin'),
    								'visualisation' => array('admin','superAdmin'),
    								'nouvel-utilisateur' => array('admin','superAdmin'),
    								'verifier-username' => array('admin','superAdmin'),

    								'parametrages' => array('admin','superAdmin'),
    								'gestion-des-hopitaux' => array('admin','superAdmin'),
    								'liste-hopitaux-ajax' => array('admin','superAdmin'),
    								'get-departements' => array('admin','superAdmin'),
    								'ajouter-hopital' => array('admin','superAdmin'),
    								'get-infos-hopital' => array('admin','superAdmin'),
    								'get-infos-modification-hopital' => array('admin','superAdmin'),
    								
    								'gestion-des-batiments' => array('admin','superAdmin'),
    								'gestion-des-services' => array('admin','superAdmin'),
    								'liste-services-ajax' => array('admin','superAdmin'),
    								'get-infos-service' => array('admin','superAdmin'),
    								'get-infos-modification-service' => array('admin','superAdmin'),
    								'ajouter-service' => array('admin','superAdmin'),
    								'supprimer-service' => array('admin','superAdmin'),
    								
    								'gestion-des-actes' => array('admin','superAdmin'),
    								'liste-actes-ajax' => array('admin','superAdmin'),
    								'get-infos-acte' => array('admin','superAdmin'),
    								'get-infos-modification-acte' => array('admin','superAdmin'),
    								'ajouter-acte' => array('admin','superAdmin'),
    								'supprimer-acte'  => array('admin','superAdmin'),
    								
    						),
    						
    						
    						
    						//*************** CERPAD ********************
    						//*************** CERPAD ********************
    						//*************** CERPAD ********************
    						/***
    						 * SecretariatController
    						*/
    						'Secretariat\Controller\Secretariat' => array(
    								'ajouter-patient' => 'secretariat',
    								'enregistrement' => 'secretariat',
    								'liste-patient' => 'secretariat',
    								'liste-patients-ajax' => 'secretariat',
    								'infos-patient' => 'secretariat',
    								'modifier-patient' => 'secretariat',
    								'demandes-analyses' => 'secretariat',
    								'liste-recherche-patient-ajax' => 'secretariat',
    								'popup-vue-patient' => 'secretariat',
    								'demandes-analyses-vue' => 'secretariat',
    								'get-liste-analyses' => 'secretariat',
    								'get-tarif-analyse' => 'secretariat',
    								'envoyer-demandes-analyses' => 'secretariat',
    								
    								'liste-demandes-ajax' => 'secretariat',
    								'liste-demandes' => 'secretariat',
    								'get-informations-patient' => 'secretariat',
    								'liste-demandes-filtre-ajax' => 'secretariat',
    								'liste-demandes-analyse' => 'secretariat',
    								'get-liste-analyses-demandees' => 'secretariat',
    								'impression-demandes-analyses' => array('secretariat', 'laborantin'),
    								'impression-analyses-demandees' => array('secretariat', 'laborantin'),
    								
    						),
    						
    						/***
    						 * LaboratoireController
    						*/
    						'Laboratoire\Controller\Laboratoire' => array(
    								'liste-demandes-analyses' => 'laborantin', 
    								'liste-demandes-analyses-ajax' => 'laborantin',
    								'infos-patient' => 'laborantin', 
    								'get-informations-patient' => 'laborantin', 
    								'get-liste-analyses-demandees' => 'laborantin',
    								'recuperer-analyse' => 'laborantin',
    								'recuperer-les-analyses-de-la-demande' => 'laborantin',
    								'recuperer-les-analyses-demandees-par-type' => 'laborantin',
    						        'enregistrer-resultat' => 'laborantin',
    						        'enregistrer-resultats-demande' => 'laborantin',
    						        'get-liste-analyses' => 'laborantin',
        						    'recuperer-les-analyses-demandees-par-type-et-analyse' => 'laborantin',
    						        'recuperer-les-analyses-demandees-par-type-et-analyse-et-date' => 'laborantin',
    						        'impression-resultats-analyses-demandees' => 'laborantin',
    						),
    						
    						
    						
    						
    				),
    		),
    )
);