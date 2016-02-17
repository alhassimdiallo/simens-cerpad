<?php
return array(
    'acl' => array(
    		
        'roles' => array(
        		'guest'   => null,
        		'infirmier' => 'guest',
        		'laborantin' => 'guest',
        		'admin' => 'guest',
        		'radiologie' => 'guest',
        		'anesthesie' => 'guest',
        		'major' => 'guest',
        		'facturation' => 'guest',
        		'etat_civil' => 'guest',
        		'archivage' => 'guest',
        		
        		'surveillant' => 'guest',
        		'medecin'     => 'surveillant',
        		
        		'superAdmin'  => 'medecin'
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
    								
    								'utilisateurs' => 'superAdmin',
    								'liste-utilisateurs-ajax' => 'superAdmin',
    								'modifier-utilisateur' => 'superAdmin',
    								'liste-agent-personnel-ajax' => 'superAdmin',
    								'visualisation' => 'superAdmin',
    								'nouvel-utilisateur' => 'superAdmin',
    								'verifier-username' => 'superAdmin',

    								'parametrages' => 'superAdmin',
    								'gestion-des-hopitaux' => 'superAdmin',
    								'liste-hopitaux-ajax' => 'superAdmin',
    								'get-departements' => 'superAdmin',
    								'ajouter-hopital' => 'superAdmin',
    								'get-infos-hopital' => 'superAdmin',
    								'get-infos-modification-hopital' => 'superAdmin',
    								
    								'gestion-des-batiments' => 'superAdmin',
    								
    						),
    						
    						
    						/***
    						 * FacturationController
    						 */
    						
    						'Facturation\Controller\Facturation' => array(
    								/*Menu Dosssier*/
    								'ajouter' => array('facturation','etat_civil'),
    								'info-patient' => array('facturation','etat_civil'),
    								'modifier' => array('facturation','etat_civil'),
    								'enregistrement-modification' => array('facturation','etat_civil'),
    								'enregistrement' => array('facturation','etat_civil'),
    								
    								'liste-patient' => array('facturation','etat_civil'),
    								'liste-patient-ajax' => array('facturation','etat_civil'),
    								
    								/*Menu Naissance*/
    								'ajouter-naissance' => 'etat_civil',
    								'lepatient' => 'etat_civil',
    								'enregistrer-bebe' => 'etat_civil',
    								'ajouter-naissance-ajax' => 'etat_civil',
    								
    								'liste-naissance' => 'etat_civil',
    								'liste-naissance-ajax' => 'etat_civil',
    								'vue-naissance' => 'etat_civil',
    								'vue-info-maman' => 'etat_civil',
    								'modifier-naissance' => 'etat_civil',
    								
    								'ajouter-maman' => 'etat_civil',
    								'enregistrement-maman' => 'etat_civil',
    								'ajouter-patient' => 'etat_civil',
    								'enregistrement-patient' => 'etat_civil',
    								
    								/*MENU DECES*/
    								'declarer-deces' => 'guest',
    								'liste-patient-declaration-deces-ajax' => 'etat_civil',
    								'le-patient' => 'etat_civil',
    								
    								'liste-patients-decedes' => 'etat_civil',
    								'liste-patient-deces-ajax' => 'etat_civil',
    								'vue-patient-decede' => 'etat_civil',
    								'modifier-deces' => 'etat_civil',
    								'supprimer-deces' => 'etat_civil',
    								
    								/*MENU ADMISSION*/
    								'admission' => 'facturation',
    								'liste-admission-ajax' => 'facturation',
    								'montant' => 'facturation',
    								'enregistrer-admission' => 'facturation',
    								
    								'liste-patients-admis' => 'facturation',
    								'vue-patient-admis' => 'facturation',
    								'supprimer-admission' => 'facturation',
    								'enregistrer-deces' => 'facturation',
    								
    						),
    						
    						
    						/***
    						 * ConsultationController
    						 */

    						'Consultation\Controller\Consultation' => array(
    								//============  SURVEILLANT  ===================
    								'recherche' => 'surveillant',
    								'espace-recherche-surv' => 'surveillant',
    								'maj-consultation' => 'surveillant',
    								'ajout-constantes' => 'surveillant',
    								'ajout-donnees-constantes' => 'surveillant',
    								'maj-cons-donnees' => 'surveillant',
    								
    								//============ MEDECIN =========================
    								'consultation-medecin' => 'medecin',
    								'espace-recherche-med' => 'medecin',
    								'complement-consultation' => 'medecin',
    								'services' => 'medecin',
    								'update-complement-consultation' => 'medecin',
    								'maj-complement-consultation' => 'medecin',
    								'visualisation-consultation' => 'medecin',
    								'visualisation-hospitalisation' => 'medecin',
    								'liste-soins-visualisation-hosp' => 'medecin',
    								
    								'imagesExamensMorphologiques' => 'medecin',
    								'supprimerImage' => 'medecin', 
    								'demande-examen' => 'medecin',
    								'demande-examen-biologique' => 'medecin',
    								
    								'en-cours' => 'medecin',
    								'liste-patient-encours-ajax' => 'medecin',
    								'info-patient' => 'medecin',
    								'detail-info-liberation-patient' => 'medecin',
    								'info-patient-hospi' => 'medecin',
    								'liste-soin' => 'medecin',
    								'supprimer-soin' => 'medecin',
    								'modifier-soin' => 'medecin',
    								'vue-soin-appliquer' => 'medecin',
    								'liberer-patient' => 'medecin',
    								'liste-soins-prescrits' => 'medecin',
    								'recherche-visualisation-consultation' => 'medecin',
    								'enregistrer-examen-du-jour' => 'medecin',
    								'liste-examen-du-jour' => 'medecin',
    								'supprimer-examen-jour' => 'medecin',
    								'vue-examen-jour' => 'medecin',
    								
    								'supprimer-image-morpho' => 'guest', //radiologie et archivage
    								'images-examens-morphologiques' => 'guest', //radiologie et archivage
    								
    								
    								'test-mp3' => 'medecin',
    								'ajouter-mp3' => 'medecin',
    								'afficher-mp3' => 'medecin',
    								'supprimer-mp3' => 'medecin',
    								'inserer-bd-mp3' => 'medecin',
    								'afficher-video' => 'medecin',
    								
    								/**PDF**/
    								'impression-Pdf' => 'medecin',
    								
    						),
    						
    						
    						/***
    						 * PersonnelController
    						 */
    						
    						'Personnel\Controller\Personnel' => array(
    								'liste-personnel' => 'admin',
    								'liste-personnel-ajax' => 'admin',
    								'info-personnel' => 'admin',
    								'supprimer' => 'admin',
    								'modifier-dossier' => 'admin',
    								'dossier-personnel' => 'admin',
    								
    								'transfert' => 'admin',
    								'liste-personnel-transfert-ajax' => 'admin',
    								'popup-agent-personnel' => 'admin',
    								'vue-agent-personnel' => 'admin',
    								'services' => 'admin',
    								
    								'liste-transfert' => 'admin',
    								'liste-transfert-ajax' => 'admin',
    								'supprimer-transfert' => 'admin',
    								
    								'intervention' => 'admin',
    								'liste-personnel-intervention-ajax' => 'admin',
    								'liste-intervention' => 'admin',
    								'liste-intervention-ajax' => 'admin',
    								'supprimer-transfert' => 'admin',
    								'info-personnel-intervention' => 'admin',
    								'vue-intervention-agent' => 'admin',
    								'supprimer-intervention' => 'admin',
    								'supprimer-une-intervention' => 'admin',
    								'save-intervention' => 'admin',
    								'modifier-intervention-agent' =>'admin',
    						),
    						
    						/***
    						 * HospitalisationController
    						 */
    						
    						'Hospitalisation\Controller\Hospitalisation' => array(
    								
    								'suivi-patient' => 'infirmier',
    								'liste-patient-suivi-ajax' => 'infirmier',
    								'vue-soin-appliquer' => 'infirmier',
    								'administrer-soin-patient' => 'infirmier',
    								'application-soin' => 'infirmier',
    								'raffraichir-liste' => 'infirmier',
    								
    								'en-cours' => 'infirmier',
    								'liste-patient-encours-ajax' => 'infirmier',
    								'liste-soin' => 'infirmier',
    								'supprimer-soin' => 'infirmier',
    								'modifier-soin' => 'infirmier',
    								'detail-info-liberation-patient' => 'infirmier',
    								'liberer-patient' => 'infirmier',
    								'heure-suivante' => 'infirmier',
    								'heure-passee' => 'infirmier',
    								'liste-soins-du-jour-a-appliquer' => 'infirmier',
    								'details-infos-soin-a-appliquer' => 'infirmier',
    								'heure-du-soin' => 'infirmier',
    								
    								
    								'liste-demandes-examens' => 'laborantin',
    								'liste-demandes-examens-ajax' => 'laborantin',
    								'liste-examens-demander' => 'laborantin', 
    								'vue-examen-appliquer' => 'laborantin',
    								'raffraichir-liste-examens-bio' => 'laborantin',
    								'envoyer-examen-bio' => 'laborantin',
    								
    								'verifier-si-resultat-existe' => 'guest',
    								'modifier-examen' => 'guest',
    								'envoyer-examen' => 'guest',
    								'appliquer-examen' => 'guest',
    								'raffraichir-liste-examens' => 'guest',
    								
    								'liste-examens-effectues' => 'laborantin',
    								'liste-recherche-examens-effectues-ajax' => 'laborantin',

    								
    								'liste-demandes-examens-morpho' => 'radiologie',
    								'liste-demandes-examens-morpho-ajax' => 'radiologie',
    								'liste-examens-demander-morpho' => 'radiologie',
    								'vue-examen-appliquer-morpho' => 'radiologie',
    								'liste-examens-effectues-morpho' => 'radiologie',
    								'liste-recherche-examens-effectues-morpho-ajax' => 'radiologie',
    								'ajouter-examen' => 'radiologie',
    								
    								
    								
    								'liste-demandes-vpa' =>      'anesthesie',
    								'liste-demandes-vpa-ajax' => 'anesthesie',
    								'details-demande-visite' =>  'anesthesie',
    								'save-result-vpa' => 'anesthesie',
    								'liste-recherche-vpa' => 'anesthesie',
    								'liste-recherche-vpa-ajax' => 'anesthesie',
    								'details-recherche-visite' => 'anesthesie',
    								
    								
    								
    								'demande-hospitalisation' => 'major',
    								'liste-liberer-patients' => 'major',
    								'liste-liberer-patient-ajax' => 'major',
    								'liberation-patient' => 'major',
    								'liberer-patient_major' => 'major',
    								'info-patient-liberer' => 'major',
    								
    								'liste-patient-ajax' => 'major',
    								'salles' => 'major',
    								'lits' => 'major',
    								'liste' => 'major',
    								'info-patient' => 'guest',
    								'info-patient-hospi' => 'major',
    								
    								'gestion-des-lits' => 'major',
    								'liste-lits-ajax' => 'major',
    								'occuper-lit' => 'major',
    								'liberer-lit' => 'major',
    								'etat-lit' => 'major',
    								'information-patient' => 'major',
    								'info-lit-salle-batiment' => 'major',
     						),
    						
    						
    						/***
    						 * ArchivageController
    						 * ArchivageController
    						 * ArchivageController
    						 * ArchivageController
    						 * ArchivageController
    						 * ArchivageController
    						 * ArchivageController
    						 * ArchivageController
    						 * ArchivageController
    						 * ArchivageController
    						 * ArchivageController
    						*/
    						
    						'Archivage\Controller\Archivage' => array(
    								/*Consultation */
    								'index' => array('archivage','medecin'),
    								'consulter' => array('archivage','medecin'),
    								'liste-consultation' => array('archivage','medecin'),
    								'hospitaliser' => array('archivage','medecin'),
    								'consultation' => array('archivage','medecin'), 
    								'update-complement-consultation' => array('archivage','medecin'), 
    								'visualisation-consultation' => array('archivage','medecin'),
    								'visualiser-consultation' => array('archivage','medecin'),
    								
    								/*Facturation*/
    								'ajouter' => array('archivage','medecin'),
    								'enregistrement' => array('archivage','medecin'),
    								'liste-dossiers-patients' => array('archivage','medecin'),
    								'liste-patient-ajax' => array('archivage','medecin'),
    								'info-dossier-patient' => array('archivage','medecin'),
    								'modifier' => array('archivage','medecin'),
    								'enregistrement-modification' => array('archivage','medecin'),
    								'admission' => array('archivage','medecin'),
    								'liste-admission-ajax' => array('archivage','medecin'),
    								'popup-visualisation' => array('archivage','medecin'),
    								'montant' => array('archivage','medecin'),
    								'enregistrer-admission' => array('archivage','medecin'),
    								'liste-admission' => array('archivage','medecin'),
    								'vue-patient-admis' => array('archivage','medecin'),
    								'supprimer-admission'  => array('archivage','medecin'),
    								
    								/*Hospitalisation*/
    								'info-patient' => array('archivage','medecin'),
    								'info-patient-hospi' => array('archivage','medecin'),
    								'liste-patient-encours-ajax' => array('archivage','medecin'),
    								'liste-demande-hospitalisation' => array('archivage','medecin'),
    								'salles' => array('archivage','medecin'),
    								'lits' => array('archivage','medecin'),
    								'services' => array('archivage','medecin'),
    								'administrer-soin' => array('archivage','medecin'),
    								'liste-soins-prescrits' => array('archivage','medecin'),
    								'appliquer-soin' => array('archivage','medecin'),
    								'liste-patient-suivi-ajax' => array('archivage','medecin'),
    								'info-patient-suivi' => array('archivage','medecin'),
    								'administrer-soin-patient' => array('archivage','medecin'),
    								'vue-soin-appliquer' => array('archivage','medecin'),
    								'heure-suivante' => array('archivage','medecin'),
    								'application-soin' => array('archivage','medecin'),
    								'raffraichir-liste' => array('archivage','medecin'),
    								'detail-info-liberation-patient' => array('archivage','medecin'),
    								'liste-demande-hospi-ajax' => array('archivage','medecin'),
    								'liberer-patient' => array('archivage','medecin'),
    								
    								/*Radiologie*/
    								'liste-resultats-radiologie' => array('archivage','medecin'),
    								'liste-recherche-examens-effectues-morpho-ajax' => array('archivage','medecin'),
    								'liste-examens-demander-morpho' => array('archivage','medecin'),
    								'verifier-si-resultat-existe' => array('archivage','medecin'),
    								'vue-examen-appliquer-morpho' => array('archivage','medecin'),
    								
    								/*Biologie*/
    								'ajouter-resultat-biologique' => array('archivage','medecin'),
    								'liste-demandes-examens-ajax' => array('archivage','medecin'),
    								'liste-examens-demander' => array('archivage','medecin'),
    								'verifier-si-resultat-existe' => array('archivage','medecin'),
    								'vue-examen-appliquer' => array('archivage','medecin'),
    								'appliquer-examen' => array('archivage','medecin'),
    								'raffraichir-liste-examens-bio' => array('archivage','medecin'),
    								'modifier-examen' => array('archivage','medecin'),
    								'envoyer-examen-bio' => array('archivage','medecin'),
    								'liste-resultats-biologie' => array('archivage','medecin'),
    								'liste-recherche-examens-effectues-ajax' => array('archivage','medecin'),
    								
    								/*Anesthésie*/
    								'ajouter-resultat-vpa' => array('archivage','medecin'),
    								'liste-demandes-vpa-ajax' => array('archivage','medecin'),
    								'details-demande-visite' => array('archivage','medecin'),
    								'liste-resultats-vpa' => array('archivage','medecin'),
    								'liste-recherche-vpa-ajax' => array('archivage','medecin'),
    								'save-result-vpa' => array('archivage','medecin'),
    								'details-recherche-visite' => array('archivage','medecin'),
    								
    						),
    				),
    		),
    )
);