<?php

namespace Consultation\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Facturation\Model\Patient;
use Consultation\Model\Consultation;
use Consultation\Model\MotifAdmission;
use Consultation\Model\RvPatientCons;
use Consultation\Model\RvPatientConsTable;
use Personnel\Model\Service;
use Consultation\Form\ConsultationForm;
use Zend\Json\Json;
use Zend\Mvc\Service\ViewJsonRendererFactory;
use Zend\Json\Expr;
use Consultation\Model\Consommable;
use Consultation\View\Helpers;
use Consultation\View\Helpers\DocumentPdf;
use Consultation\View\Helpers\OrdonnancePdf;
use ZendPdf\Font;
use ZendPdf\Page;
use ZendPdf\PdfDocument;
use Consultation\Model\ConsultationTable;
use Consultation\View\Helpers\TraitementChirurgicalPdf;
use Personnel\Model\ServiceTable;
use Consultation\View\Helpers\TransfertPdf;
use Consultation\View\Helpers\RendezVousPdf;
use Facturation\View\Helper\DateHelper;
use Zend\Mvc\Controller\Plugin\Layout;
use Admin\Form\UtilisateurForm;
use Consultation\Form\SoinForm;
use Consultation\Model\Soinhospitalisation2;
use Consultation\Form\LibererPatientForm;
use Zend\Form\View\Helper\FormRow;
use Zend\Form\View\Helper\FormTextarea;
use Zend\Form\View\Helper\FormHidden;
use Consultation\Form\SoinmodificationForm;
use Zend\Form\View\Helper\FormText;
use Zend\Form\View\Helper\FormSelect;
use Consultation\Form\ConsultationHospitalisationForm;
use Consultation\Model\Soinhospitalisation3;
use Zend\XmlRpc\Value\Base64;

class ConsultationController extends AbstractActionController {
	protected $controlDate;
	protected $patientTable;
	protected $consultationTable;
	protected $motifAdmissionTable;
	protected $rvPatientConsTable;
	protected $serviceTable;
	protected $hopitalTable;
	protected $transfererPatientServiceTable;
	protected $consommableTable;
	protected $donneesExamensPhysiquesTable;
	protected $diagnosticsTable;
	protected $ordonnanceTable;
	protected $demandeVisitePreanesthesiqueTable;
	protected $notesExamensMorphologiquesTable;
	protected $demandeExamensTable;
	protected $ordonConsommableTable;
	protected $antecedantPersonnelTable;
	protected $antecedantsFamiliauxTable;
	protected $demandeHospitalisationTable;

	protected $soinhospitalisationTable;
	protected $soinsTable;
	protected $hospitalisationTable;
	protected $hospitalisationlitTable;
	protected $litTable;
	protected $salleTable;
	protected $batimentTable;
	protected $resultatVpaTable;
	
	
	public function getPatientTable() {
		if (! $this->patientTable) {
			$sm = $this->getServiceLocator ();
			$this->patientTable = $sm->get ( 'Facturation\Model\PatientTable' );
		}
		return $this->patientTable;
	}
	public function getConsultationTable() {
		if (! $this->consultationTable) {
			$sm = $this->getServiceLocator ();
			$this->consultationTable = $sm->get ( 'Consultation\Model\ConsultationTable' );
		}
		return $this->consultationTable;
	}
	public function getMotifAdmissionTable() {
		if (! $this->motifAdmissionTable) {
			$sm = $this->getServiceLocator ();
			$this->motifAdmissionTable = $sm->get ( 'Consultation\Model\MotifAdmissionTable' );
		}
		return $this->motifAdmissionTable;
	}
	public function getRvPatientConsTable() {
		if (! $this->rvPatientConsTable) {
			$sm = $this->getServiceLocator ();
			$this->rvPatientConsTable = $sm->get ( 'Consultation\Model\RvPatientConsTable' );
		}
		return $this->rvPatientConsTable;
	}
	public function getServiceTable() {
		if (! $this->serviceTable) {
			$sm = $this->getServiceLocator ();
			$this->serviceTable = $sm->get ( 'Personnel\Model\ServiceTable' );
		}
		return $this->serviceTable;
	}
	public function getHopitalTable() {
		if (! $this->hopitalTable) {
			$sm = $this->getServiceLocator ();
			$this->hopitalTable = $sm->get ( 'Personnel\Model\HopitalTable' );
		}
		return $this->hopitalTable;
	}
	public function getTransfererPatientServiceTable() {
		if (! $this->transfererPatientServiceTable) {
			$sm = $this->getServiceLocator ();
			$this->transfererPatientServiceTable = $sm->get ( 'Consultation\Model\TransfererPatientServiceTable' );
		}
		return $this->transfererPatientServiceTable;
	}
	public function getConsommableTable() {
		if (! $this->consommableTable) {
			$sm = $this->getServiceLocator ();
			$this->consommableTable = $sm->get ( 'Pharmacie\Model\ConsommableTable' );
		}
		return $this->consommableTable;
	}
	public function getDonneesExamensPhysiquesTable() {
		if (! $this->donneesExamensPhysiquesTable) {
			$sm = $this->getServiceLocator ();
			$this->donneesExamensPhysiquesTable = $sm->get ( 'Consultation\Model\DonneesExamensPhysiquesTable' );
		}
		return $this->donneesExamensPhysiquesTable;
	}
	public function getDiagnosticsTable() {
		if (! $this->diagnosticsTable) {
			$sm = $this->getServiceLocator ();
			$this->diagnosticsTable = $sm->get ( 'Consultation\Model\DiagnosticsTable' );
		}
		return $this->diagnosticsTable;
	}
	public function getOrdonnanceTable() {
		if (! $this->ordonnanceTable) {
			$sm = $this->getServiceLocator ();
			$this->ordonnanceTable = $sm->get ( 'Consultation\Model\OrdonnanceTable' );
		}
		return $this->ordonnanceTable;
	}
	public function getDemandeVisitePreanesthesiqueTable() {
		if (! $this->demandeVisitePreanesthesiqueTable) {
			$sm = $this->getServiceLocator ();
			$this->demandeVisitePreanesthesiqueTable = $sm->get ( 'Consultation\Model\DemandeVisitePreanesthesiqueTable' );
		}
		return $this->demandeVisitePreanesthesiqueTable;
	}
	public function getNotesExamensMorphologiquesTable() {
		if (! $this->notesExamensMorphologiquesTable) {
			$sm = $this->getServiceLocator ();
			$this->notesExamensMorphologiquesTable = $sm->get ( 'Consultation\Model\NotesExamensMorphologiquesTable' );
		}
		return $this->notesExamensMorphologiquesTable;
	}
	public function demandeExamensTable() {
		if (! $this->demandeExamensTable) {
			$sm = $this->getServiceLocator ();
			$this->demandeExamensTable = $sm->get ( 'Consultation\Model\DemandeTable' );
		}
		return $this->demandeExamensTable;
	}
	public function getOrdonConsommableTable() {
		if (! $this->ordonConsommableTable) {
			$sm = $this->getServiceLocator ();
			$this->ordonConsommableTable = $sm->get ( 'Consultation\Model\OrdonConsommableTable' );
		}
		return $this->ordonConsommableTable;
	}
	public function getAntecedantPersonnelTable() {
		if (! $this->antecedantPersonnelTable) {
			$sm = $this->getServiceLocator ();
			$this->antecedantPersonnelTable = $sm->get ( 'Consultation\Model\AntecedentPersonnelTable' );
		}
		return $this->antecedantPersonnelTable;
	}
	
	public function getAntecedantsFamiliauxTable() {
		if (! $this->antecedantsFamiliauxTable) {
			$sm = $this->getServiceLocator ();
			$this->antecedantsFamiliauxTable = $sm->get ( 'Consultation\Model\AntecedentsFamiliauxTable' );
		}
		return $this->antecedantsFamiliauxTable;
	}
	
	public function getDemandeHospitalisationTable() {
		if (! $this->demandeHospitalisationTable) {
			$sm = $this->getServiceLocator ();
			$this->demandeHospitalisationTable = $sm->get ( 'Consultation\Model\DemandehospitalisationTable' );
		}
		return $this->demandeHospitalisationTable;
	}
	
	/*POUR LA GESTION DES HOSPITALISATIONS*/
	public function getSoinHospitalisationTable() {
		if (! $this->soinhospitalisationTable) {
			$sm = $this->getServiceLocator ();
			$this->soinhospitalisationTable = $sm->get ( 'Consultation\Model\SoinhospitalisationTable' );
		}
		return $this->soinhospitalisationTable;
	}
	
	public function getSoinsTable() {
		if (! $this->soinsTable) {
			$sm = $this->getServiceLocator ();
			$this->soinsTable = $sm->get ( 'Consultation\Model\SoinsTable' );
		}
		return $this->soinsTable;
	}
	
	public function getHospitalisationTable() {
		if (! $this->hospitalisationTable) {
			$sm = $this->getServiceLocator ();
			$this->hospitalisationTable = $sm->get ( 'Consultation\Model\HospitalisationTable' );
		}
		return $this->hospitalisationTable;
	}
	
	public function getHospitalisationlitTable() {
		if (! $this->hospitalisationlitTable) {
			$sm = $this->getServiceLocator ();
			$this->hospitalisationlitTable = $sm->get ( 'Consultation\Model\HospitalisationlitTable' );
		}
		return $this->hospitalisationlitTable;
	}
	
	public function getLitTable() {
		if (! $this->litTable) {
			$sm = $this->getServiceLocator ();
			$this->litTable = $sm->get ( 'Consultation\Model\LitTable' );
		}
		return $this->litTable;
	}
	
	public function getSalleTable() {
		if (! $this->salleTable) {
			$sm = $this->getServiceLocator ();
			$this->salleTable = $sm->get ( 'Consultation\Model\SalleTable' );
		}
		return $this->salleTable;
	}
	
	public function getBatimentTable() {
		if (! $this->batimentTable) {
			$sm = $this->getServiceLocator ();
			$this->batimentTable = $sm->get ( 'Consultation\Model\BatimentTable' );
		}
		return $this->batimentTable;
	}
	
	public function getResultatVpa() {
		if (! $this->resultatVpaTable) {
			$sm = $this->getServiceLocator ();
			$this->resultatVpaTable = $sm->get ( 'Consultation\Model\ResultatVisitePreanesthesiqueTable' );
		}
		return $this->resultatVpaTable;
	}
	/**
	 * =========================================================================
	 * =========================================================================
	 * =========================================================================
	 */
	protected $utilisateurTable;
	
	public function getUtilisateurTable(){
		if(!$this->utilisateurTable){
			$sm = $this->getServiceLocator();
			$this->utilisateurTable = $sm->get('Admin\Model\UtilisateursTable');
		}
		return $this->utilisateurTable;
	}
	
	public function user(){
		$uAuth = $this->getServiceLocator()->get('Admin\Controller\Plugin\UserAuthentication'); //@todo - We must use PluginLoader $this->userAuthentication()!!
		$username = $uAuth->getAuthService()->getIdentity();
		$user = $this->getUtilisateurTable()->getUtilisateursWithUsername($username);
		
		return $user;
	}
	
/***
 * *********************************************************************************************************************************
 * *********************************************************************************************************************************
 * *********************************************************************************************************************************
 */	
	public function  getDateHelper(){
		$this->controlDate = new DateHelper();
	}
	/***$$$$***/
	public function rechercheAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
		$user = $this->layout()->user;
		$NomService = $user['NomService'];
		$IdService = $user['IdService'];
		
		$patientsAdmis = $this->getPatientTable ()->tousPatientsAdmis ( $NomService , $IdService);
		
		//RECUPERER TOUS LES PATIENTS AYANT UN RV aujourd'hui
		$tabPatientRV = $this->getconsultationTable ()->getPatientsRV($IdService);

		$view = new ViewModel ( array (
				'donnees' => $patientsAdmis,
				'tabPatientRV' => $tabPatientRV,
		) );
		return $view;
	}
	
	/***$$$$***/
	public function ajoutConstantesAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
		$id_pat = $this->params ()->fromRoute ( 'id_patient', 0 );
	
		$user = $this->layout()->user;
		$id_surveillant = $user['id_personne'];
	
		$liste = $this->getPatientTable ()->getInfoPatient ( $id_pat );
		$image = $this->getPatientTable ()->getPhoto ( $id_pat );
	
		// creer le formulaire
		$today = new \DateTime ();
		$date = $today->format ( 'Y-m-d H:i:s' );
		$form = new ConsultationForm ();
		$id_cons = $form->get ( 'id_cons' )->getValue ();
		$heure_cons = $form->get ( 'heure_cons' )->getValue ();
		// peupler le formulaire
		$dateonly = $today->format ( 'Y-m-d' );
	
		$consult = array (
				'id_patient' => $id_pat,
				'id_surveillant' => $id_surveillant,
				'date_cons' => $date,
				'dateonly' => $dateonly
		);
		$form->populateValues ( $consult );
	
		//RECUPERER TOUS LES PATIENTS AYANT UN RV aujourd'hui
		$tabPatientRV = $this->getConsultationTable ()->getPatientsRV($user['IdService']);
		$resultRV = null;
		if(array_key_exists($id_pat, $tabPatientRV)){
			$resultRV = $tabPatientRV[ $id_pat ];
		}
	
		return new ViewModel ( array (
				'lesdetails' => $liste,
				'image' => $image,
				'form' => $form,
				'id_cons' => $id_cons,
				'heure_cons' => $heure_cons,
				'dateonly' => $consult['dateonly'],
				'resultRV' => $resultRV,
		) );
	}
	
	/***$$$$***/
	public function ajoutDonneesConstantesAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
	
		$form = new ConsultationForm ();
		$formData = $this->getRequest ()->getPost ();
		$form->setData ( $formData );
	
		$this->getConsultationTable ()->addConsultation ( $form, $IdDuService );
		$id_cons = $form->get ( "id_cons" )->getValue ();
		$this->getConsultationTable ()->addConsultationEffective($id_cons);
		
		//Recuperer les donnees sur les bandelettes urinaires
		//Recuperer les donnees sur les bandelettes urinaires
		$bandelettes = array(
				'id_cons' => $id_cons,
				'albumine' => $form->get ( "albumine" )->getValue (),
				'sucre' => $form->get ( "sucre" )->getValue (),
				'corpscetonique' => $form->get ( "corpscetonique" )->getValue (),
				'croixalbumine' => $form->get ( "croixalbumine" )->getValue (),
				'croixsucre' => $form->get ( "croixsucre" )->getValue (),
				'croixcorpscetonique' => $form->get ( "croixcorpscetonique" )->getValue (),
		);
		$this->getConsultationTable ()->addBandelette($bandelettes);
			
		$this->getMotifAdmissionTable ()->addMotifAdmission ( $form );
	
		return $this->redirect ()->toRoute ( 'consultation', array (
				'action' => 'recherche'
		));
	}
	
	/***$$$$***/
	public function majConsultationAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
	
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
	
		$id_pat = $this->params ()->fromQuery ( 'id_patient', 0 );
		$liste = $this->getPatientTable ()->getInfoPatient ( $id_pat );
	
		// Recuperer la photo du patient
		$image = $this->getPatientTable ()->getPhoto ( $id_pat );
	
		// Formulaire consultation
		$form = new ConsultationForm ();
	
		$id_cons = $this->params ()->fromQuery ( 'id_cons', 0 );
	
		$consult = $this->getConsultationTable ()->getConsult ( $id_cons );
	
		// instancier le motif d'admission et recupï¿½rer l'enregistrement
		$motif_admission = $this->getMotifAdmissionTable ()->getMotifAdmission ( $id_cons );
		$nbMotif = $this->getMotifAdmissionTable ()->nbMotifs ( $id_cons );
	
		// Pour verifier la date du rendez vous afin de le signaler
		$rendez_vous = $this->getRvPatientConsTable ()->getRendezVous ( $id_cons );
	
		$data = array (
				'id_cons' => $consult->id_cons,
				'id_surveillant' => $consult->id_surveillant,
				'id_patient' => $consult->id_patient,
				'date_cons' => $consult->date,
				'poids' => $consult->poids,
				'taille' => $consult->taille,
				'temperature' => $consult->temperature,
				'pressionarterielle' => $consult->pression_arterielle,
				'pouls' => $consult->pouls,
				'frequence_respiratoire' => $consult->frequence_respiratoire,
				'glycemie_capillaire' => $consult->glycemie_capillaire,
		);
		
		$k = 1;
		foreach ( $motif_admission as $Motifs ) {
			$data ['motif_admission' . $k] = $Motifs ['Libelle_motif'];
			$k ++;
		}
	
		// Pour recuper les bandelettes
		$bandelettes = $this->getConsultationTable ()->getBandelette($id_cons);
		
		$form->populateValues ( array_merge($data,$bandelettes) );
		
		//RECUPERER TOUS LES PATIENTS AYANT UN RV aujourd'hui
		$tabPatientRV = $this->getConsultationTable()->getPatientsRV($IdDuService);
		$resultRV = null;
		if(array_key_exists($id_pat, $tabPatientRV)){
			$resultRV = $tabPatientRV[ $id_pat ];
		}
		
		return array (
				'lesdetails' => $liste,
				'image' => $image,
				'form' => $form,
				'id_cons' => $id_cons,
				'verifieRV' => $rendez_vous,
				'heure_cons' => $consult->heurecons,
				'dateonly' => $consult->dateonly,
				'nbMotifs' => $nbMotif,
				'temoin' => $bandelettes['temoin'],
				'resultRV' => $resultRV,
		);
	}
	
	//***$$$$***
	public function majConsDonneesAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
	
		$form = new ConsultationForm ();
		if ($this->getRequest ()->isPost ()) {
			$formData = $this->getRequest ()->getPost ();
			$form->setData ( $formData );
	
			//mettre a jour la consultation
			$this->getConsultationTable ()->updateConsultation ( $form );
	
			//Recuperer les donnees sur les bandelettes urinaires
			//Recuperer les donnees sur les bandelettes urinaires
			$bandelettes = array(
					'id_cons' => $form->get ( "id_cons" )->getValue (),
					'albumine' => $form->get ( "albumine" )->getValue (),
					'sucre' => $form->get ( "sucre" )->getValue (),
					'corpscetonique' => $form->get ( "corpscetonique" )->getValue (),
					'croixalbumine' => $form->get ( "croixalbumine" )->getValue (),
					'croixsucre' => $form->get ( "croixsucre" )->getValue (),
					'croixcorpscetonique' => $form->get ( "croixcorpscetonique" )->getValue (),
			);
			// mettre a jour les bandelettes urinaires
			$this->getConsultationTable ()->deleteBandelette($form->get ( "id_cons" )->getValue ());
			$this->getConsultationTable ()->addBandelette($bandelettes);
	
			// mettre a jour les motifs d'admission
			$this->getMotifAdmissionTable ()->deleteMotifAdmission ( $form->get ( 'id_cons' )->getValue () );
			$this->getMotifAdmissionTable ()->addMotifAdmission ( $form );
	
			return $this->redirect ()->toRoute ( 'consultation', array (
					'action' => 'recherche'
			) );
		}
		 else {
			return $this->redirect ()->toRoute ( 'consultation', array (
					'action' => 'recherche'
			) );
		}
	}
	
	//***$$$$***
	public function espaceRechercheSurvAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
		$user = $this->layout()->user;
		$idService = $user['IdService'];
	
		$tab = $this->getConsultationTable()->tousPatientsCons ( $idService );
		return new ViewModel ( array (
				'donnees' => $tab
		) );
	}
	
	//***$$$$***
	public function consultationMedecinAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
		$user = $this->layout()->user;
		$idService = $user['IdService'];
	
		$lespatients = $this->getConsultationTable()->listePatientsConsParMedecin ( $idService );
		//RECUPERER TOUS LES PATIENTS AYANT UN RV aujourd'hui
		$tabPatientRV = $this->getConsultationTable ()->getPatientsRV($idService);
	
		return new ViewModel ( array (
				'donnees' => $lespatients,
				'tabPatientRV' => $tabPatientRV,
		) );
	}
	
	//***$$$$***
	public function complementConsultationAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
	
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		$id_medecin = $user['id_personne']; 
		
		$id_pat = $this->params ()->fromQuery ( 'id_patient', 0 );
		$id = $this->params ()->fromQuery ( 'id_cons' );
	
		$listeMedicament = $this->getConsultationTable()->listeDeTousLesMedicaments();
		$listeForme = $this->getConsultationTable()->formesMedicaments();
		$listetypeQuantiteMedicament = $this->getConsultationTable()->typeQuantiteMedicaments();
	
		$liste = $this->getConsultationTable ()->getInfoPatient ( $id_pat );
		$image = $this->getConsultationTable ()->getPhoto ( $id_pat );
	
		//RECUPERER TOUS LES PATIENTS AYANT UN RV aujourd'hui
		$tabPatientRV = $this->getConsultationTable()->getPatientsRV($IdDuService);
		$resultRV = null;
		if(array_key_exists($id_pat, $tabPatientRV)){
			$resultRV = $tabPatientRV[ $id_pat ];
		}
	
		$form = new ConsultationForm ();
	
		// instancier la consultation et rï¿½cupï¿½rer l'enregistrement
		$consult = $this->getConsultationTable ()->getConsult ( $id );
	
		// POUR LES HISTORIQUES OU TERRAIN PARTICULIER
		// POUR LES HISTORIQUES OU TERRAIN PARTICULIER
		// POUR LES HISTORIQUES OU TERRAIN PARTICULIER
		//*** Liste des consultations
		$listeConsultation = $this->getConsultationTable ()->getConsultationPatient($id_pat, $id);
	
		//*** Liste des Hospitalisations
		$listeHospitalisation = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdPatient($id_pat);
	
		// instancier le motif d'admission et recupï¿½rer l'enregistrement
		$motif_admission = $this->getMotifAdmissionTable ()->getMotifAdmission ( $id );
		$nbMotif = $this->getMotifAdmissionTable ()->nbMotifs ( $id );
	
		// rï¿½cupï¿½ration de la liste des hopitaux
		$hopital = $this->getTransfererPatientServiceTable ()->fetchHopital ();
		$form->get ( 'hopital_accueil' )->setValueOptions ( $hopital );
		// RECUPERATION DE L'HOPITAL DU SERVICE
		$transfertPatientHopital = $this->getTransfererPatientServiceTable ()->getHopitalPatientTransfert($IdDuService);
		$idHopital = $transfertPatientHopital['ID_HOPITAL'];
		
		// RECUPERATION DE LA LISTE DES SERVICES DE L'HOPITAL OU SE TROUVE LE SERVICE OU LE MEDECIN TRAVAILLE
		$serviceHopital = $this->getTransfererPatientServiceTable ()->fetchServiceWithHopitalNotServiceActual($idHopital, $IdDuService);
	
		// LISTE DES SERVICES DE L'HOPITAL
		$form->get ( 'service_accueil' )->setValueOptions ($serviceHopital);
	
		// liste des heures rv
		$heure_rv = array (
				'08:00' => '08:00',
				'09:00' => '09:00',
				'10:00' => '10:00',
				'15:00' => '15:00',
				'16:00' => '16:00'
		);
		$form->get ( 'heure_rv' )->setValueOptions ( $heure_rv );
	
		$data = array (
				'id_cons' => $consult->id_cons,
				'id_medecin' => $id_medecin,
				'id_patient' => $consult->id_patient,
				'date_cons' => $consult->date,
				'poids' => $consult->poids,
				'taille' => $consult->taille,
				'temperature' => $consult->temperature,
				'pouls' => $consult->pouls,
				'frequence_respiratoire' => $consult->frequence_respiratoire,
				'glycemie_capillaire' => $consult->glycemie_capillaire,
				'pressionarterielle' => $consult->pression_arterielle,
				'hopital_accueil' => $idHopital,
		);
		$k = 1;
		foreach ( $motif_admission as $Motifs ) {
			$data ['motif_admission' . $k] = $Motifs ['Libelle_motif'];
			$k ++;
		}
		
		// Pour recuper les bandelettes
		$bandelettes = $this->getConsultationTable ()->getBandelette($id);
		
		//RECUPERATION DES ANTECEDENTS
		//RECUPERATION DES ANTECEDENTS
		//RECUPERATION DES ANTECEDENTS
		$donneesAntecedentsPersonnels = $this->getAntecedantPersonnelTable()->getTableauAntecedentsPersonnels($id_pat);
		$donneesAntecedentsFamiliaux  = $this->getAntecedantsFamiliauxTable()->getTableauAntecedentsFamiliaux($id_pat);
		//var_dump($donneesAntecedentsFamiliaux); exit();
		//FIN ANTECEDENTS --- FIN ANTECEDENTS --- FIN ANTECEDENTS
		//FIN ANTECEDENTS --- FIN ANTECEDENTS --- FIN ANTECEDENTS
	
	
		$form->populateValues ( array_merge($data,$bandelettes,$donneesAntecedentsPersonnels,$donneesAntecedentsFamiliaux) );
		return array (
				'lesdetails' => $liste,
				'id_cons' => $id,
				'nbMotifs' => $nbMotif,
				'image' => $image,
				'form' => $form,
				'heure_cons' => $consult->heurecons,
				'dateonly' => $consult->dateonly,
				'liste_med' => $listeMedicament,
				'temoin' => $bandelettes['temoin'],
				'listeForme' => $listeForme,
				'listetypeQuantiteMedicament'  => $listetypeQuantiteMedicament,
				'donneesAntecedentsPersonnels' => $donneesAntecedentsPersonnels,
				'donneesAntecedentsFamiliaux'  => $donneesAntecedentsFamiliaux,
				'liste' => $listeConsultation,
				'resultRV' => $resultRV,
				'listeHospitalisation' => $listeHospitalisation,
		);
	}
	
	//***$$$$***
	public function updateComplementConsultationAction(){
	
		$this->getDateHelper();
		$id_cons = $this->params()->fromPost('id_cons');
		
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		$id_medecin = $user['id_personne'];
		
		//**********-- MODIFICATION DES CONSTANTES --********
		//**********-- MODIFICATION DES CONSTANTES --********
		//**********-- MODIFICATION DES CONSTANTES --********
		$form = new ConsultationForm ();
		$formData = $this->getRequest ()->getPost ();
		$form->setData ( $formData );
		
		// mettre a jour les motifs d'admission
		$this->getMotifAdmissionTable ()->deleteMotifAdmission ( $id_cons );
		$this->getMotifAdmissionTable ()->addMotifAdmission ( $form );
	
		//mettre a jour la consultation
		$this->getConsultationTable ()->updateConsultation ( $form );
	
		//Recuperer les donnees sur les bandelettes urinaires
		//Recuperer les donnees sur les bandelettes urinaires
		$bandelettes = array(
				'id_cons' => $id_cons,
				'albumine' => $this->params()->fromPost('albumine'),
				'sucre' => $this->params()->fromPost('sucre'),
				'corpscetonique' => $this->params()->fromPost('corpscetonique'),
				'croixalbumine' => $this->params()->fromPost('croixalbumine'),
				'croixsucre' => $this->params()->fromPost('croixsucre'),
				'croixcorpscetonique' => $this->params()->fromPost('croixcorpscetonique'),
		);
		//mettre a jour les bandelettes urinaires
		$this->getConsultationTable ()->deleteBandelette($id_cons);
		$this->getConsultationTable ()->addBandelette($bandelettes);
	
		//POUR LES EXAMENS PHYSIQUES
		//POUR LES EXAMENS PHYSIQUES
		//POUR LES EXAMENS PHYSIQUES
		$info_donnees_examen_physique = array(
				'id_cons' => $id_cons,
				'donnee1' => $this->params()->fromPost('examen_donnee1'),
				'donnee2' => $this->params()->fromPost('examen_donnee2'),
				'donnee3' => $this->params()->fromPost('examen_donnee3'),
				'donnee4' => $this->params()->fromPost('examen_donnee4'),
				'donnee5' => $this->params()->fromPost('examen_donnee5')
		);
		$this->getDonneesExamensPhysiquesTable()->updateExamenPhysique($info_donnees_examen_physique);
	
		//POUR LES ANTECEDENTS ANTECEDENTS ANTECEDENTS
		//POUR LES ANTECEDENTS ANTECEDENTS ANTECEDENTS
		//POUR LES ANTECEDENTS ANTECEDENTS ANTECEDENTS
		$donneesDesAntecedents = array(
				//**=== ANTECEDENTS PERSONNELS
				//**=== ANTECEDENTS PERSONNELS
				//LES HABITUDES DE VIE DU PATIENTS
				/*Alcoolique*/
				'AlcooliqueHV' => $this->params()->fromPost('AlcooliqueHV'),
				'DateDebutAlcooliqueHV' => $this->params()->fromPost('DateDebutAlcooliqueHV'),
				'DateFinAlcooliqueHV' => $this->params()->fromPost('DateFinAlcooliqueHV'),
				/*Fumeur*/
				'FumeurHV' => $this->params()->fromPost('FumeurHV'),
				'DateDebutFumeurHV' => $this->params()->fromPost('DateDebutFumeurHV'),
				'DateFinFumeurHV' => $this->params()->fromPost('DateFinFumeurHV'),
				'nbPaquetFumeurHV' => $this->params()->fromPost('nbPaquetFumeurHV'),
				/*Droguer*/
				'DroguerHV' => $this->params()->fromPost('DroguerHV'),
				'DateDebutDroguerHV' => $this->params()->fromPost('DateDebutDroguerHV'),
				'DateFinDroguerHV' => $this->params()->fromPost('DateFinDroguerHV'),
				 
				//LES ANTECEDENTS MEDICAUX
		'DiabeteAM' => $this->params()->fromPost('DiabeteAM'),
		'htaAM' => $this->params()->fromPost('htaAM'),
		'drepanocytoseAM' => $this->params()->fromPost('drepanocytoseAM'),
		'dislipidemieAM' => $this->params()->fromPost('dislipidemieAM'),
		'asthmeAM' => $this->params()->fromPost('asthmeAM'),
		 
		//GYNECO-OBSTETRIQUE
		/*Menarche*/
		'MenarcheGO' => $this->params()->fromPost('MenarcheGO'),
		'NoteMenarcheGO' => $this->params()->fromPost('NoteMenarcheGO'),
		/*Gestite*/
		'GestiteGO' => $this->params()->fromPost('GestiteGO'),
		'NoteGestiteGO' => $this->params()->fromPost('NoteGestiteGO'),
		/*Parite*/
		'PariteGO' => $this->params()->fromPost('PariteGO'),
		'NotePariteGO' => $this->params()->fromPost('NotePariteGO'),
		/*Cycle*/
		'CycleGO' => $this->params()->fromPost('CycleGO'),
		'DureeCycleGO' => $this->params()->fromPost('DureeCycleGO'),
		'RegulariteCycleGO' => $this->params()->fromPost('RegulariteCycleGO'),
		'DysmenorrheeCycleGO' => $this->params()->fromPost('DysmenorrheeCycleGO'),
		 
		//**=== ANTECEDENTS FAMILIAUX
		//**=== ANTECEDENTS FAMILIAUX
		'DiabeteAF' => $this->params()->fromPost('DiabeteAF'),
		'NoteDiabeteAF' => $this->params()->fromPost('NoteDiabeteAF'),
		'DrepanocytoseAF' => $this->params()->fromPost('DrepanocytoseAF'),
		'NoteDrepanocytoseAF' => $this->params()->fromPost('NoteDrepanocytoseAF'),
		'htaAF' => $this->params()->fromPost('htaAF'),
		'NoteHtaAF' => $this->params()->fromPost('NoteHtaAF'),
		);
		
		$id_personne = $this->getAntecedantPersonnelTable()->getIdPersonneParIdCons($id_cons);
		$this->getAntecedantPersonnelTable()->addAntecedentsPersonnels($donneesDesAntecedents, $id_personne, $id_medecin);
		$this->getAntecedantsFamiliauxTable()->addAntecedentsFamiliaux($donneesDesAntecedents, $id_personne, $id_medecin);

		//POUR LES RESULTATS DES EXAMENS MORPHOLOGIQUES
		//POUR LES RESULTATS DES EXAMENS MORPHOLOGIQUES
		//POUR LES RESULTATS DES EXAMENS MORPHOLOGIQUES
	
		$info_examen_morphologique = array(
				'id_cons'=> $id_cons,
				'8'  => $this->params()->fromPost('radio_'),
				'9'  => $this->params()->fromPost('ecographie_'),
				'12' => $this->params()->fromPost('irm_'),
				'11' => $this->params()->fromPost('scanner_'),
				'10' => $this->params()->fromPost('fibroscopie_'),
		);
	
		$this->getNotesExamensMorphologiquesTable()->updateNotesExamensMorphologiques($info_examen_morphologique);
		
		//POUR LES DIAGNOSTICS
		//POUR LES DIAGNOSTICS
		//POUR LES DIAGNOSTICS
		$info_diagnostics = array(
				'id_cons' => $id_cons,
				'diagnostic1' => $this->params()->fromPost('diagnostic1'),
				'diagnostic2' => $this->params()->fromPost('diagnostic2'),
				'diagnostic3' => $this->params()->fromPost('diagnostic3'),
				'diagnostic4' => $this->params()->fromPost('diagnostic4'),
		);
	
		$this->getDiagnosticsTable()->updateDiagnostics($info_diagnostics);
	
		//POUR LES TRAITEMENTS
		//POUR LES TRAITEMENTS
		//POUR LES TRAITEMENTS
		/**** MEDICAUX ****/
		/**** MEDICAUX ****/
		$dureeTraitement = $this->params()->fromPost('duree_traitement_ord');
		$donnees = array('id_cons' => $id_cons, 'duree_traitement' => $dureeTraitement);
	
		$Consommable = $this->getOrdonConsommableTable();
		$tab = array();
		$j = 1;
		for($i = 1 ; $i < 10 ; $i++ ){
			if($this->params()->fromPost("medicament_0".$i)){
				$tab[$j++] = $Consommable->getMedicamentByName($this->params()->fromPost("medicament_0".$i))['ID_MATERIEL'];
				$tab[$j++] = $this->params()->fromPost("forme_".$i);
				$tab[$j++] = $this->params()->fromPost("nb_medicament_".$i);
				$tab[$j++] = $this->params()->fromPost("quantite_".$i);
			}
		}
	
		/*Mettre a jour la duree du traitement de l'ordonnance*/
		$idOrdonnance = $this->getOrdonnanceTable()->updateOrdonnance($tab, $donnees);
		
		/*Mettre a jour les medicaments*/
		$resultat = $Consommable->updateOrdonConsommable($tab, $idOrdonnance);
	
		/*si aucun médicament n'est ajouté ($resultat = false) on supprime l'ordonnance*/
		if($resultat == false){ $this->getOrdonnanceTable()->deleteOrdonnance($idOrdonnance);}
	
		/**** CHIRURGICAUX ****/
		/**** CHIRURGICAUX ****/
		/**** CHIRURGICAUX ****/
		$infoDemande = array(
				'diagnostic' => $this->params()->fromPost("diagnostic_traitement_chirurgical"),
				'intervention_prevue' => $this->params()->fromPost("intervention_prevue"),
				'observation' => $this->params()->fromPost("observation"),
				'ID_CONS'=>$id_cons
		);
	
		$this->getDemandeVisitePreanesthesiqueTable()->updateDemandeVisitePreanesthesique($infoDemande);
		
	    /**** INSTRUMENTAL ****/
		/**** INSTRUMENTAL ****/
		/**** INSTRUMENTAL ****/
		$traitement_instrumental = array(
				'id_cons' => $id_cons,
				'endoscopie_interventionnelle' => $this->params()->fromPost('endoscopieInterventionnelle'),
				'radiologie_interventionnelle' => $this->params()->fromPost('radiologieInterventionnelle'),
				'cardiologie_interventionnelle' => $this->params()->fromPost('cardiologieInterventionnelle'),
				'autres_interventions' => $this->params()->fromPost('autresIntervention'),
		);
		$this->getConsultationTable()->addTraitementsInstrumentaux($traitement_instrumental);
		
		//POUR LES COMPTES RENDU DES TRAITEMENTS
		//POUR LES COMPTES RENDU DES TRAITEMENTS
		$note_compte_rendu1 = $this->params()->fromPost('note_compte_rendu_operatoire');
		$note_compte_rendu2 = $this->params()->fromPost('note_compte_rendu_operatoire_instrumental');
		
		$this->getConsultationTable()->addCompteRenduOperatoire($note_compte_rendu1, 1, $id_cons);
		$this->getConsultationTable()->addCompteRenduOperatoire($note_compte_rendu2, 2, $id_cons);
		
		//POUR LES RENDEZ VOUS
		//POUR LES RENDEZ VOUS
		//POUR LES RENDEZ VOUS
		$id_patient = $this->params()->fromPost('id_patient');
		$date_RV_Recu = $this->params()->fromPost('date_rv');
		if($date_RV_Recu){
			$date_RV = $this->controlDate->convertDateInAnglais($date_RV_Recu);
		}
		else{
			$date_RV = $date_RV_Recu;
		}
		$infos_rv = array(
				'ID_CONS' => $id_cons,
				'NOTE'    => $this->params()->fromPost('motif_rv'),
				'HEURE'   => $this->params()->fromPost('heure_rv'),
				'DATE'    => $date_RV,
		);
		$this->getRvPatientConsTable()->updateRendezVous($infos_rv);
	
		//POUR LES TRANSFERT
		//POUR LES TRANSFERT
		//POUR LES TRANSFERT
		$info_transfert = array(
				'ID_SERVICE'      => $this->params()->fromPost('id_service'),
				'ID_MEDECIN' => $this->params()->fromPost('med_id_personne'),
				'MOTIF_TRANSFERT' => $this->params()->fromPost('motif_transfert'),
				'ID_CONS' => $id_cons
		);
		
		$this->getTransfererPatientServiceTable()->updateTransfertPatientService($info_transfert);
	
		//POUR LES HOSPITALISATION
		//POUR LES HOSPITALISATION
		//POUR LES HOSPITALISATION
		$today = new \DateTime ();
		$dateAujourdhui = $today->format ( 'Y-m-d H:i:s' );
		$infoDemandeHospitalisation = array(
					'motif_demande_hospi' => $this->params()->fromPost('motif_hospitalisation'),
					'date_demande_hospi' => $dateAujourdhui,
					'date_fin_prevue_hospi' => $this->controlDate->convertDateInAnglais($this->params()->fromPost('date_fin_hospitalisation_prevue')),
					'id_cons' => $id_cons,
			);
	
		$this->getDemandeHospitalisationTable()->saveDemandehospitalisation($infoDemandeHospitalisation);
		
		//POUR LA PAGE complement-consultation
		//POUR LA PAGE complement-consultation
		//POUR LA PAGE complement-consultation
		if ($this->params ()->fromPost ( 'terminer' ) == 'save') {
	
			//VALIDER EN METTANT '1' DANS CONSPRISE Signifiant que le medecin a consulter le patient
			//Ajouter l'id du medecin ayant consulter le patient
			$valide = array (
					'VALIDER' => 1,
					'ID_CONS' => $id_cons,
					'ID_MEDECIN' => $this->params()->fromPost('med_id_personne')
							
			);
			$this->getConsultationTable ()->validerConsultation ( $valide );
		}
	
		return $this->redirect ()->toRoute ( 'consultation', array (
				'action' => 'consultation-medecin'
		) );
	}
	
	//***$$$$***
	public function majComplementConsultationAction() {

		$this->layout ()->setTemplate ( 'layout/consultation' );
		
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		$id_medecin = $user['id_personne'];
		 
		$this->getDateHelper(); 
		$id_pat = $this->params()->fromQuery ( 'id_patient', 0 );
		$id = $this->params()->fromQuery ( 'id_cons' );
		$form = new ConsultationForm();

		$liste = $this->getConsultationTable()->getInfoPatient ( $id_pat );
		$image = $this->getConsultationTable()->getPhoto ( $id_pat );
		 
	    //GESTION DES ALERTES
		//GESTION DES ALERTES
		//GESTION DES ALERTES
		//RECUPERER TOUS LES PATIENTS AYANT UN RV aujourd'hui
		$tabPatientRV = $this->getConsultationTable()->getPatientsRV($IdDuService);
		$resultRV = null;
		if(array_key_exists($id_pat, $tabPatientRV)){
			$resultRV = $tabPatientRV[ $id_pat ];
		}
		 
		//POUR LES CONSTANTES
		//POUR LES CONSTANTES
		//POUR LES CONSTANTES
		$consult = $this->getConsultationTable ()->getConsult ( $id );
		  
		$data = array (
				'id_cons' => $consult->id_cons,
		 		'id_medecin' => $consult->id_medecin,
		 		'id_patient' => $consult->id_patient,
		 		'date_cons' => $consult->date,
		 		'poids' => $consult->poids,
		 		'taille' => $consult->taille,
		 		'temperature' => $consult->temperature,
		 		'pressionarterielle' => $consult->pression_arterielle,
		 		'pouls' => $consult->pouls,
		 		'frequence_respiratoire' => $consult->frequence_respiratoire,
		 		'glycemie_capillaire' => $consult->glycemie_capillaire,
		);
		  
		//POUR LES MOTIFS D'ADMISSION
		//POUR LES MOTIFS D'ADMISSION
		//POUR LES MOTIFS D'ADMISSION
		// instancier le motif d'admission et recupï¿½rer l'enregistrement
		$motif_admission = $this->getMotifAdmissionTable ()->getMotifAdmission ( $id );
		$nbMotif = $this->getMotifAdmissionTable ()->nbMotifs ( $id );
		
		//POUR LES MOTIFS D'ADMISSION
		$k = 1;
		foreach ( $motif_admission as $Motifs ) {
			$data ['motif_admission' . $k] = $Motifs ['Libelle_motif'];
		 	$k ++;
		}
		
		//POUR LES EXAMEN PHYSIQUES
		//POUR LES EXAMEN PHYSIQUES
		//POUR LES EXAMEN PHYSIQUES
		$examen_physique = $this->getDonneesExamensPhysiquesTable()->getExamensPhysiques($id);
		
		//POUR LES EXAMEN PHYSIQUES
		$kPhysique = 1;
		foreach ($examen_physique as $Examen) {
		 	$data['examen_donnee'.$kPhysique] = $Examen['libelle_examen'];
		  	$kPhysique++;
		}
		
		// POUR LES ANTECEDENTS OU TERRAIN PARTICULIER
		// POUR LES ANTECEDENTS OU TERRAIN PARTICULIER
		// POUR LES ANTECEDENTS OU TERRAIN PARTICULIER
		$listeConsultation = $this->getConsultationTable ()->getConsultationPatient($id_pat, $id);
		
		//*** Liste des Hospitalisations
		$listeHospitalisation = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdPatient($id_pat);
		
		//POUR LES EXAMENS COMPLEMENTAIRES
		//POUR LES EXAMENS COMPLEMENTAIRES
		//POUR LES EXAMENS COMPLEMENTAIRES
		// DEMANDES DES EXAMENS COMPLEMENTAIRES
		$listeDemandesMorphologiques = $this->demandeExamensTable()->getDemandeExamensMorphologiques($id);
		$listeDemandesBiologiques = $this->demandeExamensTable()->getDemandeExamensBiologiques($id);
		
		////RESULTATS DES EXAMENS BIOLOGIQUES DEJA EFFECTUES ET ENVOYER PAR LE BIOLOGISTE
		$listeDemandesBiologiquesEffectuerEnvoyer = $this->demandeExamensTable()->getDemandeExamensBiologiquesEffectuesEnvoyer($id);
		$listeDemandesBiologiquesEffectuer = $this->demandeExamensTable()->getDemandeExamensBiologiquesEffectues($id);
		
		foreach ($listeDemandesBiologiquesEffectuerEnvoyer as $listeExamenBioEffectues){
		 	if($listeExamenBioEffectues['idExamen'] == 1){
		  		$data['groupe_sanguin'] =  $listeExamenBioEffectues['noteResultat'];
		}
		if($listeExamenBioEffectues['idExamen'] == 2){
			$data['hemogramme_sanguin'] =  $listeExamenBioEffectues['noteResultat'];
		}
		if($listeExamenBioEffectues['idExamen'] == 3){
			$data['bilan_hepatique'] =  $listeExamenBioEffectues['noteResultat'];
		}
		if($listeExamenBioEffectues['idExamen'] == 4){
			$data['bilan_renal'] =  $listeExamenBioEffectues['noteResultat'];
		}
		if($listeExamenBioEffectues['idExamen'] == 5){
			$data['bilan_hemolyse'] =  $listeExamenBioEffectues['noteResultat'];
		}
		if($listeExamenBioEffectues['idExamen'] == 6){
			$data['bilan_inflammatoire'] =  $listeExamenBioEffectues['noteResultat'];
		}
		}
		
		////RESULTATS DES EXAMENS MORPHOLOGIQUE
		$examen_morphologique = $this->getNotesExamensMorphologiquesTable()->getNotesExamensMorphologiques($id);
		  
		$data['radio'] = $examen_morphologique['radio'];
		$data['ecographie'] = $examen_morphologique['ecographie'];
		$data['fibrocospie'] = $examen_morphologique['fibroscopie'];
		$data['scanner'] = $examen_morphologique['scanner'];
		$data['irm'] = $examen_morphologique['irm'];
		  
		////RESULTATS DES EXAMENS MORPHOLOGIQUES DEJA EFFECTUES ET ENVOYER PAR LE BIOLOGISTE
		$listeDemandesMorphologiquesEffectuer = $this->demandeExamensTable()->getDemandeExamensMorphologiquesEffectues($id);

		//DIAGNOSTICS
		//DIAGNOSTICS
		//DIAGNOSTICS
		$infoDiagnostics = $this->getDiagnosticsTable()->getDiagnostics($id);
		// POUR LES DIAGNOSTICS
		$k = 1;
		foreach ($infoDiagnostics as $diagnos){
		  $data['diagnostic'.$k] = $diagnos['libelle_diagnostics'];
		  $k++;
		}
		
		//TRAITEMENT (Ordonnance) *********************************************************
		//TRAITEMENT (Ordonnance) *********************************************************
		//TRAITEMENT (Ordonnance) *********************************************************
		  
		//POUR LES MEDICAMENTS
		//POUR LES MEDICAMENTS
		//POUR LES MEDICAMENTS
		// INSTANCIATION DES MEDICAMENTS de l'ordonnance
		$listeMedicament = $this->getConsultationTable()->listeDeTousLesMedicaments();
		$listeForme = $this->getConsultationTable()->formesMedicaments();
		$listetypeQuantiteMedicament = $this->getConsultationTable()->typeQuantiteMedicaments();

		// INSTANTIATION DE L'ORDONNANCE
		$infoOrdonnance = $this->getOrdonnanceTable()->getOrdonnanceNonHospi($id); 
		
		if($infoOrdonnance) {
		 	$idOrdonnance = $infoOrdonnance->id_document; 
		  	$duree_traitement = $infoOrdonnance->duree_traitement;
		    //LISTE DES MEDICAMENTS PRESCRITS
		  	$listeMedicamentsPrescrits = $this->getOrdonnanceTable()->getMedicamentsParIdOrdonnance($idOrdonnance);
		  	$nbMedPrescrit = $listeMedicamentsPrescrits->count();
		}else{
		  	$nbMedPrescrit = null;
		  	$listeMedicamentsPrescrits =null;
		  	$duree_traitement = null;
		}
		
		//POUR LA DEMANDE PRE-ANESTHESIQUE
		//POUR LA DEMANDE PRE-ANESTHESIQUE
		//POUR LA DEMANDE PRE-ANESTHESIQUE
		$donneesDemandeVPA = $this->getDemandeVisitePreanesthesiqueTable()->getDemandeVisitePreanesthesique($id);
		
		$resultatVpa = null;
		if($donneesDemandeVPA) {
		  	$data['diagnostic_traitement_chirurgical'] = $donneesDemandeVPA['DIAGNOSTIC'];
		  	$data['observation'] = $donneesDemandeVPA['OBSERVATION'];
		  	$data['intervention_prevue'] = $donneesDemandeVPA['INTERVENTION_PREVUE'];
		  	
		  	$resultatVpa = $this->getResultatVpa()->getResultatVpa($donneesDemandeVPA['idVpa']);
		}
		  
		
		/**** INSTRUMENTAL ****/
		/**** INSTRUMENTAL ****/
		/**** INSTRUMENTAL ****/
		$traitement_instrumental = $this->getConsultationTable()->getTraitementsInstrumentaux($id);
		
		$data['endoscopieInterventionnelle'] = $traitement_instrumental['endoscopie_interventionnelle'];
		$data['radiologieInterventionnelle'] = $traitement_instrumental['radiologie_interventionnelle'];
		$data['cardiologieInterventionnelle'] = $traitement_instrumental['cardiologie_interventionnelle'];
		$data['autresIntervention'] = $traitement_instrumental['autres_interventions'];
		
		//POUR LES COMPTES RENDU OPERATOIRE
		//POUR LES COMPTES RENDU OPERATOIRE
		$compte_rendu_chirurgical = $this->getConsultationTable()->getCompteRenduOperatoire(1, $id);
		$data['note_compte_rendu_operatoire'] = $compte_rendu_chirurgical['note'];
		$compte_rendu_instrumental = $this->getConsultationTable()->getCompteRenduOperatoire(2, $id);
		$data['note_compte_rendu_operatoire_instrumental'] = $compte_rendu_instrumental['note'];
		
		//POUR LE TRANSFERT
		//POUR LE TRANSFERT
		//POUR LE TRANSFERT
		// INSTANCIATION DU TRANSFERT
		// RECUPERATION DE LA LISTE DES HOPITAUX
		$hopital = $this->getTransfererPatientServiceTable ()->fetchHopital ();  
		
		//LISTE DES HOPITAUX
		$form->get ( 'hopital_accueil' )->setValueOptions ( $hopital );
		// RECUPERATION DU SERVICE OU EST TRANSFERE LE PATIENT
		$transfertPatientService = $this->getTransfererPatientServiceTable ()->getServicePatientTransfert($id);
		
		  if( $transfertPatientService ){
		  	$idService = $transfertPatientService['ID_SERVICE'];
		    // RECUPERATION DE L'HOPITAL DU SERVICE
		  	$transfertPatientHopital = $this->getTransfererPatientServiceTable ()->getHopitalPatientTransfert($idService);
		  	$idHopital = $transfertPatientHopital['ID_HOPITAL'];
		    // RECUPERATION DE LA LISTE DES SERVICES DE L'HOPITAL OU SE TROUVE LE SERVICE OU IL EST TRANSFERE
		  	$serviceHopital = $this->getTransfererPatientServiceTable ()->fetchServiceWithHopital($idHopital);

		  	// LISTE DES SERVICES DE L'HOPITAL
		  	$form->get ( 'service_accueil' )->setValueOptions ($serviceHopital);
		  
		    // SELECTION DE L'HOPITAL ET DU SERVICE SUR LES LISTES
		  	$data['hopital_accueil'] = $idHopital;
		  	$data['service_accueil'] = $idService;
		  	$data['motif_transfert'] = $transfertPatientService['MOTIF_TRANSFERT'];
		  	$hopitalSelect = 1;
		  }else {
		  	$hopitalSelect = 0;
		  	// RECUPERATION DE L'HOPITAL DU SERVICE
		  	$transfertPatientHopital = $this->getTransfererPatientServiceTable ()->getHopitalPatientTransfert($IdDuService);
		  	$idHopital = $transfertPatientHopital['ID_HOPITAL'];
		  	$data['hopital_accueil'] = $idHopital;
		  	// RECUPERATION DE LA LISTE DES SERVICES DE L'HOPITAL OU SE TROUVE LE SERVICE OU LE MEDECIN TRAVAILLE
		  	$serviceHopital = $this->getTransfererPatientServiceTable ()->fetchServiceWithHopitalNotServiceActual($idHopital, $IdDuService);
		  	// LISTE DES SERVICES DE L'HOPITAL
		  	$form->get ( 'service_accueil' )->setValueOptions ($serviceHopital);
		  }
		  
		  //POUR LE RENDEZ VOUS
		  //POUR LE RENDEZ VOUS
		  //POUR LE RENDEZ VOUS
		  // RECUPERE LE RENDEZ VOUS
		  $leRendezVous = $this->getRvPatientConsTable()->getRendezVous($id);
		 
		  if($leRendezVous) { 
		  	$data['heure_rv'] = $leRendezVous->heure;
		  	$data['date_rv']  = $this->controlDate->convertDate($leRendezVous->date);
		  	$data['motif_rv'] = $leRendezVous->note;
		  }
		  // Pour recuper les bandelettes
		  $bandelettes = $this->getConsultationTable ()->getBandelette($id);
		  
		  //RECUPERATION DES ANTECEDENTS
		  //RECUPERATION DES ANTECEDENTS
		  //RECUPERATION DES ANTECEDENTS
		  $donneesAntecedentsPersonnels = $this->getAntecedantPersonnelTable()->getTableauAntecedentsPersonnels($id_pat);
		  $donneesAntecedentsFamiliaux = $this->getAntecedantsFamiliauxTable()->getTableauAntecedentsFamiliaux($id_pat);
		  //FIN ANTECEDENTS --- FIN ANTECEDENTS --- FIN ANTECEDENTS
		  //FIN ANTECEDENTS --- FIN ANTECEDENTS --- FIN ANTECEDENTS
		  
		  //POUR LES DEMANDES D'HOSPITALISATION
		  //POUR LES DEMANDES D'HOSPITALISATION
		  //POUR LES DEMANDES D'HOSPITALISATION
		  $donneesHospi = $this->getDemandeHospitalisationTable()->getDemandehospitalisationParIdcons($id);
		  if($donneesHospi){
		  	$data['motif_hospitalisation'] = $donneesHospi->motif_demande_hospi;
		  	$data['date_fin_hospitalisation_prevue'] = $this->controlDate->convertDate($donneesHospi->date_fin_prevue_hospi);
		  }
		 // var_dump($data); exit();
		  $form->populateValues ( array_merge($data,$bandelettes,$donneesAntecedentsPersonnels,$donneesAntecedentsFamiliaux) );
		  return array(
		 		'id_cons' => $id,
		 		'lesdetails' => $liste,
		 		'form' => $form,
		 		'nbMotifs' => $nbMotif,
		 		'image' => $image,
		 		'heure_cons' => $consult->heurecons,
		 		'liste' => $listeConsultation,
		  		'liste_med' => $listeMedicament,
		  		'nb_med_prescrit' => $nbMedPrescrit,
		  		'liste_med_prescrit' => $listeMedicamentsPrescrits,
		  		'duree_traitement' => $duree_traitement,
		  		'verifieRV' => $leRendezVous, 
		  		'listeDemandesMorphologiques' => $listeDemandesMorphologiques,
		  		'listeDemandesBiologiques' => $listeDemandesBiologiques,
		  		'hopitalSelect' =>$hopitalSelect,
		  		'nbDiagnostics'=> $infoDiagnostics->count(),
		  		'nbDonneesExamenPhysique' => $kPhysique,
		  		'dateonly' => $consult->dateonly,
		  		'temoin' => $bandelettes['temoin'],
		  		'listeForme' => $listeForme,
		  		'listetypeQuantiteMedicament'  => $listetypeQuantiteMedicament,
		  		'donneesAntecedentsPersonnels' => $donneesAntecedentsPersonnels,
		  		'donneesAntecedentsFamiliaux'  => $donneesAntecedentsFamiliaux,
		  		'resultRV' => $resultRV,
		  		'listeDemandesBioEff' => $listeDemandesBiologiquesEffectuer->count(),
		  		'listeDemandesMorphoEff' => $listeDemandesMorphologiquesEffectuer->count(),
		  		'resultatVpa' => $resultatVpa,
		  		'listeHospitalisation' => $listeHospitalisation,
		  );
	
	}
	
	
	//***$$$$***
	public function visualisationConsultationAction(){
	
		$this->layout ()->setTemplate ( 'layout/consultation' );
			
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		$id_medecin = $user['id_personne'];
	
		$this->getDateHelper();
		$id_pat = $this->params()->fromQuery ( 'id_patient', 0 );
		$id = $this->params()->fromQuery ( 'id_cons' );
		$id_demande_hospi = $this->params()->fromQuery ( 'id_demande_hospi' );
			
		if($id_demande_hospi){
			return $this->redirect ()->toRoute ( 'consultation', array ('action' => 'visualisation-hospitalisation'),
					array('query'=>array('id_demande_hospi' => $id_demande_hospi
					) ) );
		}
		
		$form = new ConsultationForm();
			
		$liste = $this->getConsultationTable()->getInfoPatient ( $id_pat );
		$image = $this->getConsultationTable()->getPhoto ( $id_pat );
	
		//GESTION DES ALERTES
		//GESTION DES ALERTES
		//GESTION DES ALERTES
		//RECUPERER TOUS LES PATIENTS AYANT UN RV aujourd'hui
		$tabPatientRV = $this->getConsultationTable()->getPatientsRV($IdDuService);
		$resultRV = null;
		if(array_key_exists($id_pat, $tabPatientRV)){
			$resultRV = $tabPatientRV[ $id_pat ];
		}
	
		//POUR LES CONSTANTES
		//POUR LES CONSTANTES
		//POUR LES CONSTANTES
		$consult = $this->getConsultationTable ()->getConsult ( $id );
			
		$data = array (
				'id_cons' => $consult->id_cons,
				'id_medecin' => $consult->id_medecin,
				'id_patient' => $consult->id_patient,
				'date_cons' => $consult->date,
				'poids' => $consult->poids,
				'taille' => $consult->taille,
				'temperature' => $consult->temperature,
				'pressionarterielle' => $consult->pression_arterielle,
				'pouls' => $consult->pouls,
				'frequence_respiratoire' => $consult->frequence_respiratoire,
				'glycemie_capillaire' => $consult->glycemie_capillaire,
		);
			
		//POUR LES MOTIFS D'ADMISSION
		//POUR LES MOTIFS D'ADMISSION
		//POUR LES MOTIFS D'ADMISSION
		// instancier le motif d'admission et recupï¿½rer l'enregistrement
		$motif_admission = $this->getMotifAdmissionTable ()->getMotifAdmission ( $id );
		$nbMotif = $this->getMotifAdmissionTable ()->nbMotifs ( $id );
			
		//POUR LES MOTIFS D'ADMISSION
		$k = 1;
		foreach ( $motif_admission as $Motifs ) {
			$data ['motif_admission' . $k] = $Motifs ['Libelle_motif'];
			$k ++;
		}
			
		//POUR LES EXAMEN PHYSIQUES
		//POUR LES EXAMEN PHYSIQUES
		//POUR LES EXAMEN PHYSIQUES
		$examen_physique = $this->getDonneesExamensPhysiquesTable()->getExamensPhysiques($id);
			
		//POUR LES EXAMEN PHYSIQUES
		$kPhysique = 1;
		foreach ($examen_physique as $Examen) {
			$data['examen_donnee'.$kPhysique] = $Examen['libelle_examen'];
			$kPhysique++;
		}
			
		// POUR LES ANTECEDENTS OU TERRAIN PARTICULIER
		// POUR LES ANTECEDENTS OU TERRAIN PARTICULIER
		// POUR LES ANTECEDENTS OU TERRAIN PARTICULIER
		$listeConsultation = $this->getConsultationTable ()->getConsultationPatient($id_pat, $id);
			
		//*** Liste des Hospitalisations
		$listeHospitalisation = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdPatient($id_pat);
			
		//POUR LES EXAMENS COMPLEMENTAIRES
		//POUR LES EXAMENS COMPLEMENTAIRES
		//POUR LES EXAMENS COMPLEMENTAIRES
		// DEMANDES DES EXAMENS COMPLEMENTAIRES
		$listeDemandesMorphologiques = $this->demandeExamensTable()->getDemandeExamensMorphologiques($id);
		$listeDemandesBiologiques = $this->demandeExamensTable()->getDemandeExamensBiologiques($id);
			
		////RESULTATS DES EXAMENS BIOLOGIQUES DEJA EFFECTUES ET ENVOYER PAR LE BIOLOGISTE
		$listeDemandesBiologiquesEffectuerEnvoyer = $this->demandeExamensTable()->getDemandeExamensBiologiquesEffectuesEnvoyer($id);
		$listeDemandesBiologiquesEffectuer = $this->demandeExamensTable()->getDemandeExamensBiologiquesEffectues($id);
			
		foreach ($listeDemandesBiologiquesEffectuerEnvoyer as $listeExamenBioEffectues){
			if($listeExamenBioEffectues['idExamen'] == 1){
				$data['groupe_sanguin'] =  $listeExamenBioEffectues['noteResultat'];
			}
			if($listeExamenBioEffectues['idExamen'] == 2){
				$data['hemogramme_sanguin'] =  $listeExamenBioEffectues['noteResultat'];
			}
			if($listeExamenBioEffectues['idExamen'] == 3){
				$data['bilan_hepatique'] =  $listeExamenBioEffectues['noteResultat'];
			}
			if($listeExamenBioEffectues['idExamen'] == 4){
				$data['bilan_renal'] =  $listeExamenBioEffectues['noteResultat'];
			}
			if($listeExamenBioEffectues['idExamen'] == 5){
				$data['bilan_hemolyse'] =  $listeExamenBioEffectues['noteResultat'];
			}
			if($listeExamenBioEffectues['idExamen'] == 6){
				$data['bilan_inflammatoire'] =  $listeExamenBioEffectues['noteResultat'];
			}
		}
			
		////RESULTATS DES EXAMENS MORPHOLOGIQUE
		$examen_morphologique = $this->getNotesExamensMorphologiquesTable()->getNotesExamensMorphologiques($id);
			
		$data['radio'] = $examen_morphologique['radio'];
		$data['ecographie'] = $examen_morphologique['ecographie'];
		$data['fibrocospie'] = $examen_morphologique['fibroscopie'];
		$data['scanner'] = $examen_morphologique['scanner'];
		$data['irm'] = $examen_morphologique['irm'];
			
		////RESULTATS DES EXAMENS MORPHOLOGIQUES DEJA EFFECTUES ET ENVOYER PAR LE BIOLOGISTE
		$listeDemandesMorphologiquesEffectuer = $this->demandeExamensTable()->getDemandeExamensMorphologiquesEffectues($id);
			
		//DIAGNOSTICS
		//DIAGNOSTICS
		//DIAGNOSTICS
		$infoDiagnostics = $this->getDiagnosticsTable()->getDiagnostics($id);
		// POUR LES DIAGNOSTICS
		$k = 1;
		foreach ($infoDiagnostics as $diagnos){
			$data['diagnostic'.$k] = $diagnos['libelle_diagnostics'];
			$k++;
		}
			
		//TRAITEMENT (Ordonnance) *********************************************************
		//TRAITEMENT (Ordonnance) *********************************************************
		//TRAITEMENT (Ordonnance) *********************************************************
			
		//POUR LES MEDICAMENTS
		//POUR LES MEDICAMENTS
		//POUR LES MEDICAMENTS
		// INSTANCIATION DES MEDICAMENTS de l'ordonnance
		$listeMedicament = $this->getConsultationTable()->listeDeTousLesMedicaments();
		$listeForme = $this->getConsultationTable()->formesMedicaments();
		$listetypeQuantiteMedicament = $this->getConsultationTable()->typeQuantiteMedicaments();
			
		// INSTANTIATION DE L'ORDONNANCE
		$infoOrdonnance = $this->getOrdonnanceTable()->getOrdonnanceNonHospi($id);
			
		if($infoOrdonnance) {
			$idOrdonnance = $infoOrdonnance->id_document;
			$duree_traitement = $infoOrdonnance->duree_traitement;
			//LISTE DES MEDICAMENTS PRESCRITS
			$listeMedicamentsPrescrits = $this->getOrdonnanceTable()->getMedicamentsParIdOrdonnance($idOrdonnance);
			$nbMedPrescrit = $listeMedicamentsPrescrits->count();
		}else{
			$nbMedPrescrit = null;
			$listeMedicamentsPrescrits =null;
			$duree_traitement = null;
		}
			
		//POUR LA DEMANDE PRE-ANESTHESIQUE
		//POUR LA DEMANDE PRE-ANESTHESIQUE
		//POUR LA DEMANDE PRE-ANESTHESIQUE
		$donneesDemandeVPA = $this->getDemandeVisitePreanesthesiqueTable()->getDemandeVisitePreanesthesique($id);
			
		$resultatVpa = null;
		if($donneesDemandeVPA) {
			$data['diagnostic_traitement_chirurgical'] = $donneesDemandeVPA['DIAGNOSTIC'];
			$data['observation'] = $donneesDemandeVPA['OBSERVATION'];
			$data['intervention_prevue'] = $donneesDemandeVPA['INTERVENTION_PREVUE'];
				
			$resultatVpa = $this->getResultatVpa()->getResultatVpa($donneesDemandeVPA['idVpa']);
		}
			
		/**** INSTRUMENTAL ****/
		/**** INSTRUMENTAL ****/
		/**** INSTRUMENTAL ****/
		$traitement_instrumental = $this->getConsultationTable()->getTraitementsInstrumentaux($id);
		$data['endoscopieInterventionnelle'] = $traitement_instrumental['endoscopie_interventionnelle'];
		$data['radiologieInterventionnelle'] = $traitement_instrumental['radiologie_interventionnelle'];
		$data['cardiologieInterventionnelle'] = $traitement_instrumental['cardiologie_interventionnelle'];
		$data['autresIntervention'] = $traitement_instrumental['autres_interventions'];
		
		//POUR LE TRANSFERT
		//POUR LE TRANSFERT
		//POUR LE TRANSFERT
		// INSTANCIATION DU TRANSFERT
		// RECUPERATION DE LA LISTE DES HOPITAUX
		$hopital = $this->getTransfererPatientServiceTable ()->fetchHopital ();
			
		//LISTE DES HOPITAUX
		$form->get ( 'hopital_accueil' )->setValueOptions ( $hopital );
		// RECUPERATION DU SERVICE OU EST TRANSFERE LE PATIENT
		$transfertPatientService = $this->getTransfererPatientServiceTable ()->getServicePatientTransfert($id);
			
		if( $transfertPatientService ){
			$idService = $transfertPatientService['ID_SERVICE'];
			// RECUPERATION DE L'HOPITAL DU SERVICE
			$transfertPatientHopital = $this->getTransfererPatientServiceTable ()->getHopitalPatientTransfert($idService);
			$idHopital = $transfertPatientHopital['ID_HOPITAL'];
			// RECUPERATION DE LA LISTE DES SERVICES DE L'HOPITAL OU SE TROUVE LE SERVICE OU IL EST TRANSFERE
			$serviceHopital = $this->getTransfererPatientServiceTable ()->fetchServiceWithHopital($idHopital);
				
			// LISTE DES SERVICES DE L'HOPITAL
			$form->get ( 'service_accueil' )->setValueOptions ($serviceHopital);
				
			// SELECTION DE L'HOPITAL ET DU SERVICE SUR LES LISTES
			$data['hopital_accueil'] = $idHopital;
			$data['service_accueil'] = $idService;
			$data['motif_transfert'] = $transfertPatientService['MOTIF_TRANSFERT'];
			$hopitalSelect = 1;
		}else {
			$hopitalSelect = 0;
			// RECUPERATION DE L'HOPITAL DU SERVICE
			$transfertPatientHopital = $this->getTransfererPatientServiceTable ()->getHopitalPatientTransfert($IdDuService);
			$idHopital = $transfertPatientHopital['ID_HOPITAL'];
			$data['hopital_accueil'] = $idHopital;
			// RECUPERATION DE LA LISTE DES SERVICES DE L'HOPITAL OU SE TROUVE LE SERVICE OU LE MEDECIN TRAVAILLE
			$serviceHopital = $this->getTransfererPatientServiceTable ()->fetchServiceWithHopitalNotServiceActual($idHopital, $IdDuService);
			// LISTE DES SERVICES DE L'HOPITAL
			$form->get ( 'service_accueil' )->setValueOptions ($serviceHopital);
		}
			
		//POUR LE RENDEZ VOUS
		//POUR LE RENDEZ VOUS
		//POUR LE RENDEZ VOUS
		// RECUPERE LE RENDEZ VOUS
		$leRendezVous = $this->getRvPatientConsTable()->getRendezVous($id);
	
		if($leRendezVous) {
			$data['heure_rv'] = $leRendezVous->heure;
			$data['date_rv']  = $this->controlDate->convertDate($leRendezVous->date);
			$data['motif_rv'] = $leRendezVous->note;
		}
		// Pour recuper les bandelettes
		$bandelettes = $this->getConsultationTable ()->getBandelette($id);
			
		//RECUPERATION DES ANTECEDENTS
		//RECUPERATION DES ANTECEDENTS
		//RECUPERATION DES ANTECEDENTS
		$donneesAntecedentsPersonnels = $this->getAntecedantPersonnelTable()->getTableauAntecedentsPersonnels($id_pat);
		$donneesAntecedentsFamiliaux = $this->getAntecedantsFamiliauxTable()->getTableauAntecedentsFamiliaux($id_pat);
		//FIN ANTECEDENTS --- FIN ANTECEDENTS --- FIN ANTECEDENTS
		//FIN ANTECEDENTS --- FIN ANTECEDENTS --- FIN ANTECEDENTS
			
		//POUR LES DEMANDES D'HOSPITALISATION
		//POUR LES DEMANDES D'HOSPITALISATION
		//POUR LES DEMANDES D'HOSPITALISATION
		$donneesHospi = $this->getDemandeHospitalisationTable()->getDemandehospitalisationParIdcons($id);
		if($donneesHospi){
			$data['motif_hospitalisation'] = $donneesHospi->motif_demande_hospi;
			$data['date_fin_hospitalisation_prevue'] = $this->controlDate->convertDate($donneesHospi->date_fin_prevue_hospi);
		}
		// var_dump($data); exit();
		$form->populateValues ( array_merge($data,$bandelettes,$donneesAntecedentsPersonnels,$donneesAntecedentsFamiliaux) );
		return array(
				'id_cons' => $id,
				'lesdetails' => $liste,
				'form' => $form,
				'nbMotifs' => $nbMotif,
				'image' => $image,
				'heure_cons' => $consult->heurecons,
				'liste' => $listeConsultation,
				'liste_med' => $listeMedicament,
				'nb_med_prescrit' => $nbMedPrescrit,
				'liste_med_prescrit' => $listeMedicamentsPrescrits,
				'duree_traitement' => $duree_traitement,
				'verifieRV' => $leRendezVous,
				'listeDemandesMorphologiques' => $listeDemandesMorphologiques,
				'listeDemandesBiologiques' => $listeDemandesBiologiques,
				'hopitalSelect' =>$hopitalSelect,
				'nbDiagnostics'=> $infoDiagnostics->count(),
				'nbDonneesExamenPhysique' => $kPhysique,
				'dateonly' => $consult->dateonly,
				'temoin' => $bandelettes['temoin'],
				'listeForme' => $listeForme,
				'listetypeQuantiteMedicament'  => $listetypeQuantiteMedicament,
				'donneesAntecedentsPersonnels' => $donneesAntecedentsPersonnels,
				'donneesAntecedentsFamiliaux'  => $donneesAntecedentsFamiliaux,
				'resultRV' => $resultRV,
				'listeDemandesBioEff' => $listeDemandesBiologiquesEffectuer->count(),
				'listeDemandesMorphoEff' => $listeDemandesMorphologiquesEffectuer->count(),
				'resultatVpa' => $resultatVpa,
				'listeHospitalisation' => $listeHospitalisation,
		);
			
	}
	
	//***$$$$***
	public function rechercheVisualisationConsultationAction(){
	
		$this->layout ()->setTemplate ( 'layout/consultation' );
	
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		$id_medecin = $user['id_personne'];
			
		$this->getDateHelper();
		$id_pat = $this->params()->fromQuery ( 'id_patient', 0 );
		$id = $this->params()->fromQuery ( 'id_cons' );
		$form = new ConsultationForm();
	
		$liste = $this->getConsultationTable()->getInfoPatient ( $id_pat );
		$image = $this->getConsultationTable()->getPhoto ( $id_pat );
			
		//GESTION DES ALERTES
		//GESTION DES ALERTES
		//GESTION DES ALERTES
		//RECUPERER TOUS LES PATIENTS AYANT UN RV aujourd'hui
		$tabPatientRV = $this->getConsultationTable()->getPatientsRV($IdDuService);
		$resultRV = null;
		if(array_key_exists($id_pat, $tabPatientRV)){
			$resultRV = $tabPatientRV[ $id_pat ];
		}
			
		//POUR LES CONSTANTES
		//POUR LES CONSTANTES
		//POUR LES CONSTANTES
		$consult = $this->getConsultationTable ()->getConsult ( $id );
	
		$data = array (
				'id_cons' => $consult->id_cons,
				'id_medecin' => $consult->id_medecin,
				'id_patient' => $consult->id_patient,
				'date_cons' => $consult->date,
				'poids' => $consult->poids,
				'taille' => $consult->taille,
				'temperature' => $consult->temperature,
				'pressionarterielle' => $consult->pression_arterielle,
				'pouls' => $consult->pouls,
				'frequence_respiratoire' => $consult->frequence_respiratoire,
				'glycemie_capillaire' => $consult->glycemie_capillaire,
		);
	
		//POUR LES MOTIFS D'ADMISSION
		//POUR LES MOTIFS D'ADMISSION
		//POUR LES MOTIFS D'ADMISSION
		// instancier le motif d'admission et recupï¿½rer l'enregistrement
		$motif_admission = $this->getMotifAdmissionTable ()->getMotifAdmission ( $id );
		$nbMotif = $this->getMotifAdmissionTable ()->nbMotifs ( $id );
	
		//POUR LES MOTIFS D'ADMISSION
		$k = 1;
		foreach ( $motif_admission as $Motifs ) {
			$data ['motif_admission' . $k] = $Motifs ['Libelle_motif'];
			$k ++;
		}
	
		//POUR LES EXAMEN PHYSIQUES
		//POUR LES EXAMEN PHYSIQUES
		//POUR LES EXAMEN PHYSIQUES
		$examen_physique = $this->getDonneesExamensPhysiquesTable()->getExamensPhysiques($id);
	
		//POUR LES EXAMEN PHYSIQUES
		$kPhysique = 1;
		foreach ($examen_physique as $Examen) {
			$data['examen_donnee'.$kPhysique] = $Examen['libelle_examen'];
			$kPhysique++;
		}
	
		// POUR LES ANTECEDENTS OU TERRAIN PARTICULIER
		// POUR LES ANTECEDENTS OU TERRAIN PARTICULIER
		// POUR LES ANTECEDENTS OU TERRAIN PARTICULIER
		$listeConsultation = $this->getConsultationTable ()->getConsultationPatient($id_pat, $id);
	
		//*** Liste des Hospitalisations
		$listeHospitalisation = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdPatient($id_pat);
	
		//POUR LES EXAMENS COMPLEMENTAIRES
		//POUR LES EXAMENS COMPLEMENTAIRES
		//POUR LES EXAMENS COMPLEMENTAIRES
		// DEMANDES DES EXAMENS COMPLEMENTAIRES
		$listeDemandesMorphologiques = $this->demandeExamensTable()->getDemandeExamensMorphologiques($id);
		$listeDemandesBiologiques = $this->demandeExamensTable()->getDemandeExamensBiologiques($id);
	
		////RESULTATS DES EXAMENS BIOLOGIQUES DEJA EFFECTUES ET ENVOYER PAR LE BIOLOGISTE
		$listeDemandesBiologiquesEffectuerEnvoyer = $this->demandeExamensTable()->getDemandeExamensBiologiquesEffectuesEnvoyer($id);
		$listeDemandesBiologiquesEffectuer = $this->demandeExamensTable()->getDemandeExamensBiologiquesEffectues($id);
	
		foreach ($listeDemandesBiologiquesEffectuerEnvoyer as $listeExamenBioEffectues){
			if($listeExamenBioEffectues['idExamen'] == 1){
				$data['groupe_sanguin'] =  $listeExamenBioEffectues['noteResultat'];
			}
			if($listeExamenBioEffectues['idExamen'] == 2){
				$data['hemogramme_sanguin'] =  $listeExamenBioEffectues['noteResultat'];
			}
			if($listeExamenBioEffectues['idExamen'] == 3){
				$data['bilan_hepatique'] =  $listeExamenBioEffectues['noteResultat'];
			}
			if($listeExamenBioEffectues['idExamen'] == 4){
				$data['bilan_renal'] =  $listeExamenBioEffectues['noteResultat'];
			}
			if($listeExamenBioEffectues['idExamen'] == 5){
				$data['bilan_hemolyse'] =  $listeExamenBioEffectues['noteResultat'];
			}
			if($listeExamenBioEffectues['idExamen'] == 6){
				$data['bilan_inflammatoire'] =  $listeExamenBioEffectues['noteResultat'];
			}
		}
	
		////RESULTATS DES EXAMENS MORPHOLOGIQUE
		$examen_morphologique = $this->getNotesExamensMorphologiquesTable()->getNotesExamensMorphologiques($id);
	
		$data['radio'] = $examen_morphologique['radio'];
		$data['ecographie'] = $examen_morphologique['ecographie'];
		$data['fibrocospie'] = $examen_morphologique['fibroscopie'];
		$data['scanner'] = $examen_morphologique['scanner'];
		$data['irm'] = $examen_morphologique['irm'];
	
		////RESULTATS DES EXAMENS MORPHOLOGIQUES DEJA EFFECTUES ET ENVOYER PAR LE BIOLOGISTE
		$listeDemandesMorphologiquesEffectuer = $this->demandeExamensTable()->getDemandeExamensMorphologiquesEffectues($id);
	
		//DIAGNOSTICS
		//DIAGNOSTICS
		//DIAGNOSTICS
		$infoDiagnostics = $this->getDiagnosticsTable()->getDiagnostics($id);
		// POUR LES DIAGNOSTICS
		$k = 1;
		foreach ($infoDiagnostics as $diagnos){
			$data['diagnostic'.$k] = $diagnos['libelle_diagnostics'];
			$k++;
		}
	
		//TRAITEMENT (Ordonnance) *********************************************************
		//TRAITEMENT (Ordonnance) *********************************************************
		//TRAITEMENT (Ordonnance) *********************************************************
	
		//POUR LES MEDICAMENTS
		//POUR LES MEDICAMENTS
		//POUR LES MEDICAMENTS
		// INSTANCIATION DES MEDICAMENTS de l'ordonnance
		$listeMedicament = $this->getConsultationTable()->listeDeTousLesMedicaments();
		$listeForme = $this->getConsultationTable()->formesMedicaments();
		$listetypeQuantiteMedicament = $this->getConsultationTable()->typeQuantiteMedicaments();
	
		// INSTANTIATION DE L'ORDONNANCE
		$infoOrdonnance = $this->getOrdonnanceTable()->getOrdonnanceNonHospi($id);
	
		if($infoOrdonnance) {
			$idOrdonnance = $infoOrdonnance->id_document;
			$duree_traitement = $infoOrdonnance->duree_traitement;
			//LISTE DES MEDICAMENTS PRESCRITS
			$listeMedicamentsPrescrits = $this->getOrdonnanceTable()->getMedicamentsParIdOrdonnance($idOrdonnance);
			$nbMedPrescrit = $listeMedicamentsPrescrits->count();
		}else{
			$nbMedPrescrit = null;
			$listeMedicamentsPrescrits =null;
			$duree_traitement = null;
		}
	
		//POUR LA DEMANDE PRE-ANESTHESIQUE
		//POUR LA DEMANDE PRE-ANESTHESIQUE
		//POUR LA DEMANDE PRE-ANESTHESIQUE
		$donneesDemandeVPA = $this->getDemandeVisitePreanesthesiqueTable()->getDemandeVisitePreanesthesique($id);
	
		$resultatVpa = null;
		if($donneesDemandeVPA) {
			$data['diagnostic_traitement_chirurgical'] = $donneesDemandeVPA['DIAGNOSTIC'];
			$data['observation'] = $donneesDemandeVPA['OBSERVATION'];
			$data['intervention_prevue'] = $donneesDemandeVPA['INTERVENTION_PREVUE'];
				
			$resultatVpa = $this->getResultatVpa()->getResultatVpa($donneesDemandeVPA['idVpa']);
		}
	
		/**** INSTRUMENTAL ****/
		/**** INSTRUMENTAL ****/
		/**** INSTRUMENTAL ****/
		$traitement_instrumental = $this->getConsultationTable()->getTraitementsInstrumentaux($id);
		
		$data['endoscopieInterventionnelle'] = $traitement_instrumental['endoscopie_interventionnelle'];
		$data['radiologieInterventionnelle'] = $traitement_instrumental['radiologie_interventionnelle'];
		$data['cardiologieInterventionnelle'] = $traitement_instrumental['cardiologie_interventionnelle'];
		$data['autresIntervention'] = $traitement_instrumental['autres_interventions'];
		
		//POUR LE TRANSFERT
		//POUR LE TRANSFERT
		//POUR LE TRANSFERT
		// INSTANCIATION DU TRANSFERT
		// RECUPERATION DE LA LISTE DES HOPITAUX
		$hopital = $this->getTransfererPatientServiceTable ()->fetchHopital ();
	
		//LISTE DES HOPITAUX
		$form->get ( 'hopital_accueil' )->setValueOptions ( $hopital );
		// RECUPERATION DU SERVICE OU EST TRANSFERE LE PATIENT
		$transfertPatientService = $this->getTransfererPatientServiceTable ()->getServicePatientTransfert($id);
	
		if( $transfertPatientService ){
			$idService = $transfertPatientService['ID_SERVICE'];
			// RECUPERATION DE L'HOPITAL DU SERVICE
			$transfertPatientHopital = $this->getTransfererPatientServiceTable ()->getHopitalPatientTransfert($idService);
			$idHopital = $transfertPatientHopital['ID_HOPITAL'];
			// RECUPERATION DE LA LISTE DES SERVICES DE L'HOPITAL OU SE TROUVE LE SERVICE OU IL EST TRANSFERE
			$serviceHopital = $this->getTransfererPatientServiceTable ()->fetchServiceWithHopital($idHopital);
				
			// LISTE DES SERVICES DE L'HOPITAL
			$form->get ( 'service_accueil' )->setValueOptions ($serviceHopital);
				
			// SELECTION DE L'HOPITAL ET DU SERVICE SUR LES LISTES
			$data['hopital_accueil'] = $idHopital;
			$data['service_accueil'] = $idService;
			$data['motif_transfert'] = $transfertPatientService['MOTIF_TRANSFERT'];
			$hopitalSelect = 1;
		}else {
			$hopitalSelect = 0;
			// RECUPERATION DE L'HOPITAL DU SERVICE
			$transfertPatientHopital = $this->getTransfererPatientServiceTable ()->getHopitalPatientTransfert($IdDuService);
			$idHopital = $transfertPatientHopital['ID_HOPITAL'];
			$data['hopital_accueil'] = $idHopital;
			// RECUPERATION DE LA LISTE DES SERVICES DE L'HOPITAL OU SE TROUVE LE SERVICE OU LE MEDECIN TRAVAILLE
			$serviceHopital = $this->getTransfererPatientServiceTable ()->fetchServiceWithHopitalNotServiceActual($idHopital, $IdDuService);
			// LISTE DES SERVICES DE L'HOPITAL
			$form->get ( 'service_accueil' )->setValueOptions ($serviceHopital);
		}
	
		//POUR LE RENDEZ VOUS
		//POUR LE RENDEZ VOUS
		//POUR LE RENDEZ VOUS
		// RECUPERE LE RENDEZ VOUS
		$leRendezVous = $this->getRvPatientConsTable()->getRendezVous($id);
			
		if($leRendezVous) {
			$data['heure_rv'] = $leRendezVous->heure;
			$data['date_rv']  = $this->controlDate->convertDate($leRendezVous->date);
			$data['motif_rv'] = $leRendezVous->note;
		}
		// Pour recuper les bandelettes
		$bandelettes = $this->getConsultationTable ()->getBandelette($id);
	
		//RECUPERATION DES ANTECEDENTS
		//RECUPERATION DES ANTECEDENTS
		//RECUPERATION DES ANTECEDENTS
		$donneesAntecedentsPersonnels = $this->getAntecedantPersonnelTable()->getTableauAntecedentsPersonnels($id_pat);
		$donneesAntecedentsFamiliaux = $this->getAntecedantsFamiliauxTable()->getTableauAntecedentsFamiliaux($id_pat);
		//FIN ANTECEDENTS --- FIN ANTECEDENTS --- FIN ANTECEDENTS
		//FIN ANTECEDENTS --- FIN ANTECEDENTS --- FIN ANTECEDENTS
	
		//POUR LES DEMANDES D'HOSPITALISATION
		//POUR LES DEMANDES D'HOSPITALISATION
		//POUR LES DEMANDES D'HOSPITALISATION
		$donneesHospi = $this->getDemandeHospitalisationTable()->getDemandehospitalisationParIdcons($id);
		if($donneesHospi){
			$data['motif_hospitalisation'] = $donneesHospi->motif_demande_hospi;
			$data['date_fin_hospitalisation_prevue'] = $this->controlDate->convertDate($donneesHospi->date_fin_prevue_hospi);
		}
		// var_dump($data); exit();
		$form->populateValues ( array_merge($data,$bandelettes,$donneesAntecedentsPersonnels,$donneesAntecedentsFamiliaux) );
		return array(
				'id_cons' => $id,
				'lesdetails' => $liste,
				'form' => $form,
				'nbMotifs' => $nbMotif,
				'image' => $image,
				'heure_cons' => $consult->heurecons,
				'liste' => $listeConsultation,
				'liste_med' => $listeMedicament,
				'nb_med_prescrit' => $nbMedPrescrit,
				'liste_med_prescrit' => $listeMedicamentsPrescrits,
				'duree_traitement' => $duree_traitement,
				'verifieRV' => $leRendezVous,
				'listeDemandesMorphologiques' => $listeDemandesMorphologiques,
				'listeDemandesBiologiques' => $listeDemandesBiologiques,
				'hopitalSelect' =>$hopitalSelect,
				'nbDiagnostics'=> $infoDiagnostics->count(),
				'nbDonneesExamenPhysique' => $kPhysique,
				'dateonly' => $consult->dateonly,
				'temoin' => $bandelettes['temoin'],
				'listeForme' => $listeForme,
				'listetypeQuantiteMedicament'  => $listetypeQuantiteMedicament,
				'donneesAntecedentsPersonnels' => $donneesAntecedentsPersonnels,
				'donneesAntecedentsFamiliaux'  => $donneesAntecedentsFamiliaux,
				'resultRV' => $resultRV,
				'listeDemandesBioEff' => $listeDemandesBiologiquesEffectuer->count(),
				'listeDemandesMorphoEff' => $listeDemandesMorphologiquesEffectuer->count(),
				'resultatVpa' => $resultatVpa,
				'listeHospitalisation' => $listeHospitalisation,
		);
	}
	//***$$$$***
	public function espaceRechercheMedAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
	
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		
		$tab = $this->getConsultationTable()->listePatientsConsMedecin ( $IdDuService );
		return new ViewModel ( array (
				'donnees' => $tab
		) );
	}
	
	//***$$$$***
	//******* Rï¿½cupï¿½rer les services correspondants en cliquant sur un hopital
	public function servicesAction()
	{
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		
		$id = (int)$this->params()->fromPost ('id');
	
		if ($this->getRequest()->isPost()){
			$liste_select = "";
			foreach($this->getServiceTable()->getServiceHopital($id, $IdDuService) as $listeServices){
				$liste_select.= "<option value=".$listeServices['Id_service'].">".$listeServices['Nom_service']."</option>";
			}
				
			$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
			return $this->getResponse ()->setContent(Json::encode ( $liste_select));
		}
	
	}
	
	//***$$$$***
	public function visualisationHospitalisationAction(){
		$this->layout ()->setTemplate ( 'layout/consultation' );
		$id_demande_hospi = $this->params()->fromQuery ( 'id_demande_hospi' );
	
		$demandeHospi = $this->getDemandeHospitalisationTable()->getDemandehospitalisationParIdDemande($id_demande_hospi);
		$id_cons = null;
		$id_personne = null;
		if($demandeHospi){
			$id_cons = $demandeHospi->id_cons;
			$consultation = $this->getConsultationTable()->getConsult($id_cons);
			$id_personne = $consultation->id_patient;
		}
	
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);
	
		$demande = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdcons($id_cons);
	
		$hospitalisation = $this->getHospitalisationTable()->getHospitalisationWithCodedh($id_demande_hospi);
		$id_hospitalisation = $this->getHospitalisationlitTable()->getHospitalisationlit($hospitalisation->id_hosp);
		$lit = $this->getLitTable()->getLit($id_hospitalisation->id_materiel);
		$salle = $this->getSalleTable()->getSalle($lit->id_salle);
		$batiment = $this->getBatimentTable()->getBatiment($salle->id_batiment);
	
		return array(
				'unPatient' => $unPatient,
				'photo' => $photo,
				'demande' => $demande,
				'hospitalisation' => $hospitalisation,
				'lit' => $lit,
				'salle' => $salle,
				'batiment' => $batiment,
				'id_hosp' => $hospitalisation->id_hosp,
		);
	}
	//***-*-*-*-*-*-*-*-**-*-*-*-*--*-**-*-*-*-*-*-*-*--*--**-*-*-*-*-*-**-*-*-*--**-*-*-*-*-*--*-**-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-
	//***-**-*-*-*-*-**-*-*-*-*-*-*-*-*-*--**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*--**-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-**--*-**-*-*-
	
	public function impressionPdfAction(){
		
		//*************************************
		//*************************************
		//***DONNEES COMMUNES A TOUS LES PDF***
		//*************************************
		//*************************************
		$id_patient = $this->params ()->fromPost ( 'id_patient', 0 );
		$id_cons = $this->params ()->fromPost ( 'id_cons', 0 );
        
		$donneesMedecin = array();
		$donneesPatient = array();
		//*************************************
		$values = $this->getConsultationTable()->getInfoPatientMedecin($id_cons);

		foreach ($values as $val) {
			//Information sur le médecin
			$donneesMedecin['service'] = $val['NomService'];
			$donneesMedecin['nomMedecin'] = $val['NomMedecin'];
			$donneesMedecin['prenomMedecin'] = $val['PrenomMedecin'];
			//Information sur le patient
			$donneesPatient['nomPatient'] = $val['NOM'];
			$donneesPatient['prenomPatient'] = $val['PRENOM'];
			$donneesPatient['adressePatient'] = $val['ADRESSE'];
			$donneesPatient['dateNaissancePatient'] = $val['DATE_NAISSANCE'];
			$donneesPatient['sexePatient'] = $val['SEXE'];
		}
		
		//var_dump($donneesPatient); exit();
		//**********ORDONNANCE*****************
		//**********ORDONNANCE*****************
		//**********ORDONNANCE*****************
		if(isset($_POST['ordonnance'])){
			//récupération de la liste des médicaments
			$medicaments = $this->getConsultationTable()->fetchConsommable();
			
			$tab = array();
			$j = 1;
			
			//NOUVEAU CODE AVEC AUTOCOMPLETION 
			for($i = 1 ; $i < 10 ; $i++ ){
				$nomMedicament = $this->params()->fromPost("medicament_0".$i);
				if($nomMedicament == true){
					$tab[$j++] = $this->params()->fromPost("medicament_0".$i);
					$tab[$j++] = $this->params()->fromPost("forme_".$i);
					$tab[$j++] = $this->params()->fromPost("nb_medicament_".$i);
					$tab[$j++] = $this->params()->fromPost("quantite_".$i);
				}
			}
			
			$donneesPatientOR = $this->getConsultationTable()->getInfoPatient($id_patient);
			//-***************************************************************
			//Création du fichier pdf
			//*************************
			//Créer le document
			$DocPdf = new DocumentPdf();
			//Créer la page
			$page = new OrdonnancePdf();

			//Envoyer l'id_cons
			$page->setIdCons($id_cons);
			//Envoyer les données sur le partient
			$page->setDonneesPatient($donneesPatientOR);
			//Envoyer les médicaments
			$page->setMedicaments($tab);
			
			//Ajouter une note à la page
			$page->addNote();
			//Ajouter la page au document
			$DocPdf->addPage($page->getPage());

			//Afficher le document contenant la page
			$DocPdf->getDocument();
		}
		else
		//**********TRAITEMENT CHIRURGICAL*****************
		//**********TRAITEMENT CHIRURGICAL*****************
		//**********TRAITEMENT CHIRURGICAL*****************
		if(isset($_POST['traitement_chirurgical'])){
			//Récupération des données
			$donneesDemande['diagnostic'] = $this->params ()->fromPost ( 'diagnostic_traitement_chirurgical' );
			$donneesDemande['intervention_prevue'] = $this->params ()->fromPost (  'intervention_prevue' );
			$donneesDemande['observation'] = $this->params()->fromPost('observation');
			
			//CREATION DU DOCUMENT PDF
			//Créer le document
			$DocPdf = new DocumentPdf();
			//Créer la page
			$page = new TraitementChirurgicalPdf();
			
			//Envoi Id de la consultation
			$page->setIdConsTC($id_cons);
			//Envoi des données du patient
			$page->setDonneesPatientTC($donneesPatient);
			//Envoi des données du medecin
			$page->setDonneesMedecinTC($donneesMedecin);
			//Envoi les données de la demande
			$page->setDonneesDemandeTC($donneesDemande);
			
			//Ajouter les donnees a la page
			$page->addNoteTC();
			//Ajouter la page au document
			$DocPdf->addPage($page->getPage());
			
			//Afficher le document contenant la page
			$DocPdf->getDocument();
			
		}
		else 
		//**********TRANSFERT DU PATIENT*****************
		//**********TRANSFERT DU PATIENT*****************
		//**********TRANSFERT DU PATIENT*****************
			if (isset ($_POST['transfert']))
			{
				$id_hopital = $this->params()->fromPost('hopital_accueil_tampon');
				$id_service = $this->params()->fromPost('service_accueil_tampon');
				$motif_transfert = $this->params()->fromPost('motif_transfert');
		
				//Récupérer le nom du service d'accueil
 				$service = $this->getServiceTable();
 				$infoService = $service->getServiceparId($id_service);
 				//Récupérer le nom de l'hopital d'accueil
 				$hopital = $this->getHopitalTable();
 				$infoHopital = $hopital->getHopitalParId($id_hopital);
 				
 				$donneesDemandeT['NomService'] = $infoService['NOM'];
 				$donneesDemandeT['NomHopital'] = $infoHopital['NOM_HOPITAL'];
 				$donneesDemandeT['MotifTransfert'] = $motif_transfert;

				//-***************************************************************
				//Création du fichier pdf
			
				//-***************************************************************
 				//Créer le document
 				$DocPdf = new DocumentPdf();
 				//Créer la page
 				$page = new TransfertPdf();
 					
 				//Envoi Id de la consultation
 				$page->setIdConsT($id_cons);
 				//Envoi des données du patient
 				$page->setDonneesPatientT($donneesPatient);
 				//Envoi des données du medecin
 				$page->setDonneesMedecinT($donneesMedecin);
 				//Envoi les données de la demande
 				$page->setDonneesDemandeT($donneesDemandeT);
 					
 				//Ajouter les donnees a la page
 				$page->addNoteT();
 				//Ajouter la page au document
 				$DocPdf->addPage($page->getPage());
 					
 				//Afficher le document contenant la page
 				$DocPdf->getDocument();
			}
			else
			//**********RENDEZ VOUS ****************
			//**********RENDEZ VOUS ****************
			//**********RENDEZ VOUS ****************
			if(isset ($_POST['rendezvous'])){
					
				$donneesDemandeT['dateRv'] = $this->params()->fromPost('date_rv_tampon');
				$donneesDemandeT['heureRV']   = $this->params()->fromPost('heure_rv_tampon');
				$donneesDemandeT['MotifRV'] = $this->params()->fromPost('motif_rv');
				
				//\Zend\Debug\Debug::dump($note); exit();
				//Création du fichier pdf
				//Créer le document
				$DocPdf = new DocumentPdf();
				//Créer la page
				$page = new RendezVousPdf();
				
				//Envoi Id de la consultation
				$page->setIdConsR($id_cons);
				//Envoi des données du patient
				$page->setDonneesPatientR($donneesPatient);
				//Envoi des données du medecin
				$page->setDonneesMedecinR($donneesMedecin);
				//Envoi les données du redez vous
				$page->setDonneesDemandeR($donneesDemandeT);
				
				//Ajouter les donnees a la page
				$page->addNoteR();
				//Ajouter la page au document
				$DocPdf->addPage($page->getPage());
				
				//Afficher le document contenant la page
				$DocPdf->getDocument();
			
			}
			else
			//**********TRAITEMENT INSTRUMENTAL ****************
			//**********TRAITEMENT INSTRUMENTAL ****************
			//**********TRAITEMENT INSTRUMENTAL ****************
			if(isset ($_POST['traitement_instrumental'])){
					echo ('<span style="font-size: 20px; font-family: Times New Roman; font-weight: bold; color: green;">En cours de develppement "TRAITEMENT INSTRUMENTAL"</span>'); exit();
			}
			else 
				//**********HOSPITALISATION ****************
				//**********HOSPITALISATION ****************
				//**********HOSPITALISATION ****************
				if(isset ($_POST['hospitalisation'])){
					echo ('<span style="font-size: 20px; font-family: Times New Roman; font-weight: bold; color: green;">En cours de develppement "HOSPITALISATION" </span>'); exit();
				}
			
	}
	
	//********************************************************
	//********************************************************
	//********************************************************
	public function imagesExamensMorphologiquesAction()
	{
		$id_cons = $this->params()->fromPost( 'id_cons' );
		$ajout = (int)$this->params()->fromPost( 'ajout' );
		$idExamen = (int)$this->params()->fromPost( 'typeExamen' ); /*Le type d'examen*/
		$utilisateur = (int)$this->params()->fromPost( 'utilisateur' ); /* 1==radiologue sinon Medecin  */
		
		$user = $this->layout()->user;
		$id_personne = $user['id_personne'];
		
		/***
		 * INSERTION DE LA NOUVELLE IMAGE
		 */
		if($ajout == 1) {
			/***
			 * Enregistrement de l'image
			 * Enregistrement de l'image
			 * Enregistrement de l'image
			*/
			$today = new \DateTime ( 'now' );
			$nomImage = $today->format ( 'dmy_His' );
			if($idExamen == 8) { $nomImage = "radio_".$nomImage;}
			if($idExamen == 9) { $nomImage = "echographie_".$nomImage;}
			if($idExamen == 10) { $nomImage = "irm_".$nomImage;}
			if($idExamen == 11) { $nomImage = "scanner_".$nomImage;}
			if($idExamen == 12) { $nomImage = "fibroscopie_".$nomImage;}
			
			$date_enregistrement = $today->format ( 'Y-m-d H:i:s' );
			$fileBase64 = $this->params ()->fromPost ( 'fichier_tmp' );
			
			$typeFichier = substr ( $fileBase64, 5, 5 );
			$formatFichier = substr ($fileBase64, 11, 4 );
			$fileBase64 = substr ( $fileBase64, 23 );
			
			if($utilisateur == 1){
				
				if($fileBase64 && $typeFichier == 'image' && $formatFichier =='jpeg'){
					$img = imagecreatefromstring(base64_decode($fileBase64));
					if($img){
						$resultatAjout = $this->demandeExamensTable()->ajouterImageMorpho($id_cons, $idExamen, $nomImage, $date_enregistrement, $id_personne);
					}
					if($resultatAjout){
						imagejpeg ( $img, 'C:\wamp\www\simens\public\images\images\\' . $nomImage . '.jpg' );
					}
				}
				
			}else {
				
				if($fileBase64 && $typeFichier == 'image' && $formatFichier =='jpeg'){
					$img = imagecreatefromstring(base64_decode($fileBase64));
					if($img){
						$resultatAjout = $this->demandeExamensTable()->ajouterImage($id_cons, $idExamen, $nomImage, $date_enregistrement, $id_personne);
					}
					if($resultatAjout){
						imagejpeg ( $img, 'C:\wamp\www\simens\public\images\images\\' . $nomImage . '.jpg' );
					}
				}
				
			}
			
		}
		
		/**
		 * RECUPERATION DE TOUS LES RESULTATS DES EXAMENS MORPHOLOGIQUES
		 */
		if($utilisateur == 1){
			$result = $this->demandeExamensTable()->resultatExamensMorpho($id_cons);
		}else {
			$result = $this->demandeExamensTable()->resultatExamens($id_cons);
		}

		$radio = false;
		$echographie = false;
		$irm = false;
		$scanner = false;
		$fibroscopie = false;
		
		$html = "";
		$pickaChoose = "";
		
		if($result){
			foreach ($result as $resultat) {
				/**==========================**/
				/**Recuperer les images RADIO**/
				/**==========================**/
				if($resultat['idExamen'] == 8 && $idExamen == 8){
					$radio = true;
					$pickaChoose .=" <li><a href='../images/images/".$resultat['NomImage'].".jpg'><img src='../images/images/".$resultat['NomImage'].".jpg'/></a><span></span></li>";
				} else
				/**================================**/
				/**Recuperer les images ECHOGRAPHIE**/
				/**================================**/
				if($resultat['idExamen'] == 9 && $idExamen == 9){
					$echographie = true;
					$pickaChoose .=" <li><a href='../images/images/".$resultat['NomImage'].".jpg'><img src='../images/images/".$resultat['NomImage'].".jpg'/></a><span></span></li>";
				} else
				/**================================**/
				/**Recuperer les images IRM**/
				/**================================**/
				if($resultat['idExamen'] == 10 && $idExamen == 10){
					$irm = true;
					$pickaChoose .=" <li><a href='../images/images/".$resultat['NomImage'].".jpg'><img src='../images/images/".$resultat['NomImage'].".jpg'/></a><span></span></li>";
				} else
				/**================================**/
				/**Recuperer les images SCANNER**/
				/**================================**/
				if($resultat['idExamen'] == 11 && $idExamen == 11){
					$scanner = true;
					$pickaChoose .=" <li><a href='../images/images/".$resultat['NomImage'].".jpg'><img src='../images/images/".$resultat['NomImage'].".jpg'/></a><span></span></li>";
				} else
				/**================================**/
				/**Recuperer les images FIBROSCOPIE**/
				/**================================**/
				if($resultat['idExamen'] == 12 && $idExamen == 12){
					$fibroscopie = true;
					$pickaChoose .=" <li><a href='../images/images/".$resultat['NomImage'].".jpg'><img src='../images/images/".$resultat['NomImage'].".jpg'/></a><span></span></li>";
				}
			}
		}

		if($radio) {
			$html ="<div id='pika2'>
				    <div class='pikachoose' style='height: 210px;'>
                      <ul id='pikame' class='jcarousel-skin-pika'>";
			$html .=$pickaChoose;
			$html .=" </ul>
                     </div>
				     </div>";

			$html.="<script>
					  $(function(){ $('.imageRadio').toggle(true);});
					  scriptExamenMorpho();
					</script>";
		} else 
			if($echographie) {
				$html ="<div id='pika4'>
				        <div class='pikachoose' style='height: 210px;'>
                          <ul id='pikameEchographie' class='jcarousel-skin-pika'>";
				$html .=$pickaChoose;
				$html .=" </ul>
                         </div>
				         </div>";
			
				$html.="<script>
						  $(function(){ $('.imageEchographie').toggle(true);});
					      scriptEchographieExamenMorpho();
					    </script>";
			} else 
				if($irm) {
					$html ="<div id='pika6'>
				             <div class='pikachoose' style='height: 210px;'>
                              <ul id='pikameIRM' class='jcarousel-skin-pika'>";
					$html .=$pickaChoose;
					$html .=" </ul>
                              </div>
				             </div>";
						
					$html.="<script>
						     $(function(){ $('.imageIRM').toggle(true);});
					         scriptIRMExamenMorpho();
					        </script>";
				} else 
					if($scanner) {
						$html ="<div id='pika8'>
				             <div class='pikachoose' style='height: 210px;'>
                              <ul id='pikameScanner' class='jcarousel-skin-pika'>";
						$html .=$pickaChoose;
						$html .=" </ul>
                              </div>
				             </div>";
					
						$html.="<script>
						     $(function(){ $('.imageScanner').toggle(true);});
					         scriptScannerExamenMorpho();
					        </script>";
					} else 
						if($fibroscopie) {
							$html ="<div id='pika10'>
				             <div class='pikachoose' style='height: 210px;'>
                              <ul id='pikameFibroscopie' class='jcarousel-skin-pika'>";
							$html .=$pickaChoose;
							$html .=" </ul>
                              </div>
				             </div>";
								
							$html.="<script>
						     $(function(){ $('.imageFibroscopie').toggle(true);});
					         scriptFibroscopieExamenMorpho();
					        </script>";
						}
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	
	//************************************************************************************
	//************************************************************************************
	//************************************************************************************
	public function supprimerImageAction()
	{
		$id_cons = $this->params()->fromPost('id_cons');
		$id = $this->params()->fromPost('id'); //numero de l'image dans le diapo
		$typeExamen = $this->params()->fromPost('typeExamen');

		/**
		 * RECUPERATION DE TOUS LES RESULTATS DES EXAMENS MORPHOLOGIQUES
		 */
		$result = $this->demandeExamensTable()->recupererDonneesExamen($id_cons, $id, $typeExamen);
		/**
		 * SUPPRESSION PHYSIQUE DE L'IMAGE
		 */
		unlink ( 'C:\wamp\www\simens\public\images\images\\' . $result['NomImage'] . '.jpg' );
		/**
		 * SUPPRESSION DE L'IMAGE DANS LA BASE
		 */
		$this->demandeExamensTable()->supprimerImage($result['IdImage']);
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( ));
	}
	
	/** POUR LES EXAMENS MORPHOLOGIQUES **/
	/** POUR LES EXAMENS MORPHOLOGIQUES **/
	/** POUR LES EXAMENS MORPHOLOGIQUES **/
	public function supprimerImageMorphoAction()
	{
		$id_cons = $this->params()->fromPost('id_cons');
		$id = $this->params()->fromPost('id'); //numero de l'image dans le diapo
		$typeExamen = $this->params()->fromPost('typeExamen');
	
		/**
		 * RECUPERATION DE TOUS LES RESULTATS DES EXAMENS MORPHOLOGIQUES
		*/
		 $result = $this->demandeExamensTable()->recupererDonneesExamenMorpho($id_cons, $id, $typeExamen);
		/**
		 * SUPPRESSION PHYSIQUE DE L'IMAGE
		*/
		 unlink ( 'C:\wamp\www\simens\public\images\images\\' . $result['NomImage'] . '.jpg' );
		/**
		 * SUPPRESSION DE L'IMAGE DANS LA BASE
		*/
		 $this->demandeExamensTable()->supprimerImage($result['IdImage']);
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ());
	}
	
	//************************************************************************************
	//************************************************************************************
	//************************************************************************************
	public function demandeExamenAction()
	{
		$id_cons = $this->params()->fromPost('id_cons');
		$examens = $this->params()->fromPost('examens');
		$notes = $this->params()->fromPost('notes');
	

		$this->demandeExamensTable()->saveDemandesExamensMorphologiques($id_cons, $examens, $notes);
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode (  ));
	}
	
	//************************************************************************************
	//************************************************************************************
	//************************************************************************************
	public function demandeExamenBiologiqueAction()
	{
		$id_cons = $this->params()->fromPost('id_cons');
		$examensBio = $this->params()->fromPost('examensBio');
		$notesBio = $this->params()->fromPost('notesBio');
	
	
		$this->demandeExamensTable()->saveDemandesExamensBiologiques($id_cons, $examensBio, $notesBio);
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode (  ));
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/****************************************************************************************/
	/****************************************************************************************/
	/****************************************************************************************/
	/* ======== POUR LA GESTION DES HOSPITALISATIONS =========*/
	/* ======== POUR LA GESTION DES HOSPITALISATIONS =========*/
	/* ======== POUR LA GESTION DES HOSPITALISATIONS =========*/

    public function enCoursAction() {
		$this->layout()->setTemplate('layout/consultation');
		
// 		$listeMedicamentsPrescrits = $this->getOrdonnanceTable()->getMedicamentsParIdOrdonnance(8);
// 		foreach ($listeMedicamentsPrescrits as $list) {
// 			var_dump($list['Intitule'] ); exit();
// 		}

		//$output = $this->getDemandeHospitalisationTable()->getListePatientEncoursHospitalisation(8);

		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		$id_medecin = $user['id_personne'];
		
 		$formSoin = new SoinForm();
 		
		$hopital = $this->getTransfererPatientServiceTable ()->fetchHopital ();
		$formSoin->get ( 'hopital_accueil' )->setValueOptions ( $hopital );
		//RECUPERATION DE L'HOPITAL DU SERVICE
		$transfertPatientHopital = $this->getTransfererPatientServiceTable ()->getHopitalPatientTransfert($IdDuService);
		$idHopital = $transfertPatientHopital['ID_HOPITAL'];
		//RECUPERATION DE LA LISTE DES SERVICES DE L'HOPITAL OU SE TROUVE LE SERVICE OU LE MEDECIN TRAVAILLE
		$serviceHopital = $this->getTransfererPatientServiceTable ()->fetchServiceWithHopitalNotServiceActual($idHopital, $IdDuService);
		//LISTE DES SERVICES DE L'HOPITAL
		//var_dump($serviceHopital); exit();
		$formSoin->get ( 'service_accueil' )->setValueOptions ($serviceHopital);
		
		$date = new \DateTime();
		$aujourdhui = $date->format('dmy');
		$data = array (
				'hopital_accueil' => $idHopital,
				'date_cons' => $aujourdhui,
		);
		
		$formSoin->populateValues($data);
		if($this->getRequest()->isPost()) {

			$data = $this->getRequest()->getPost();
			
		    $id_sh = $this->getSoinHospitalisationTable()->saveSoinhospitalisation($data, $id_medecin);
		    $this->getSoinHospitalisationTable()->saveHeure($data,$id_sh);
			//$test = 'En cours de dÃ©veloppement';
			$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		    return $this->getResponse ()->setContent ( Json::encode () );
		}
		
		$listeMedicament = $this->getConsultationTable()->listeDeTousLesMedicaments();
		
		$listeMedicamentOrd = $this->getConsultationTable()->listeDeTousLesMedicaments();
		$listeFormeOrd = $this->getConsultationTable()->formesMedicaments();
		$listetypeQuantiteMedicamentOrd = $this->getConsultationTable()->typeQuantiteMedicaments();
		
		return array(
				'form' => $formSoin,
				'liste_med' => $listeMedicament,
				
				'liste_medOrdonnance' => $listeMedicamentOrd,
				'listeFormeOrdonnance' => $listeFormeOrd,
				'listetypeQuantiteMedicamentOrdonnance'  => $listetypeQuantiteMedicamentOrd,
		);
	}
	
	public function listePatientEncoursAjaxAction() {
		$user = $this->layout()->user;
		$IdService = $user['IdService'];
		
		$output = $this->getDemandeHospitalisationTable()->getListePatientEncoursHospitalisation($IdService);
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function getPath(){
		$this->path = $this->getServiceLocator()->get('Request')->getBasePath();
		return $this->path;
	}
	
	public function infoPatientAction() {
		$this->getDateHelper();
		$id_personne = $this->params()->fromPost('id_personne',0);
		$id_cons = $this->params()->fromPost('id_cons',0);
		$encours = $this->params()->fromPost('encours',0);
		$terminer = $this->params()->fromPost('terminer',0);
		$id_demande_hospi = $this->params()->fromPost('id_demande_hospi',0);
		
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);
		
		$demande = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdcons($id_cons);
		
		$date = $this->controlDate->convertDate( $unPatient['DATE_NAISSANCE'] );
		
		$html  = "<div style='width:100%;'>";
			
		$html .= "<div style='width: 18%; height: 180px; float:left;'>";
		$html .= "<div id='photo' style='float:left; margin-left:40px; margin-top:10px; margin-right:30px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "' ></div>";
		$html .= "</div>";
			
		$html .= "<div style='width: 65%; height: 200px; float:left;'>";
		$html .= "<table style='margin-top:10px; float:left; width: 100%;'>";
		$html .= "<tr style='width: 100%;'>";
		
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unPatient['NOM'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Lieu de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['LIEU_NAISSANCE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; actuelle:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['NATIONALITE_ACTUELLE'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'></td>";
		
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['PRENOM'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['TELEPHONE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; d'origine:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['NATIONALITE_ORIGINE']. "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Email:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unPatient['EMAIL'] . "</p></td>";
		
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['ADRESSE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Profession:</a><br><p style=' font-weight:bold; font-size:17px;'>" .  $unPatient['PROFESSION'] . "</p></td>";
		
		$html .= "<td style='width: 30%; height: 50px;'></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .="</div>";
			
		$html .= "<div style='width: 17%; height: 200px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		$html .= "<div id='titre_info_deces'> 
				     <span id='titre_info_demande41' style='margin-left: -10px; cursor:pointer;'>
				        <img src='../img/light/plus.png' /> D&eacute;tails des infos sur la demande
				     </span>
				  </div>
		          <div id='barre'></div>";
		
		$html .= "<div id='info_demande41'>";
		$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
		$html .= "<tr style='width: 95%;'>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Consultation:</a><br><p style='font-weight:bold; font-size:17px;'>" . $id_cons . "</p></td>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->controlDate->convertDateTime($demande['date_demande_hospi']) . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date fin pr&eacute;vue:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->controlDate->convertDate($demande['date_fin_prevue_hospi']) . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>M&eacute;decin demandeur:</a><br><p style=' font-weight:bold; font-size:17px;'>" .$demande['PrenomMedecin'].' '.$demande['NomMedecin']. "</p></td>";
		$html .= "</tr>";
		$html .= "</table>";
		
		$html .="<table style='margin-top:0px; margin-left:195px; width: 70%;'>";
		$html .="<tr style='width: 70%'>";
		$html .="<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 20%; '><a style='text-decoration:underline; font-size:13px;'>Motif de la demande:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'>". $demande['motif_demande_hospi'] ."</p></td>";
		$html .="<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 20%; '><a style='text-decoration:underline; font-size:13px;'>Note:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'> </p></td>";
		$html .="</tr>";
		$html .="</table>";
		$html .= "</div>";
		
		/***
		 * UTILISER UNIQUEMENT DANS LA VUE DE LA LISTE DES PATIENTS EN COURS D'HOSPITALISATION
		*/
		if($encours == 111) {
			$this->getDateHelper();
			$hospitalisation = $this->getHospitalisationTable()->getHospitalisationWithCodedh($id_demande_hospi);
			$lit_hospitalisation = $this->getHospitalisationlitTable()->getHospitalisationlit($hospitalisation->id_hosp);
			$lit = $this->getLitTable()->getLit($lit_hospitalisation->id_materiel);
			$salle = $this->getSalleTable()->getSalle($lit->id_salle);
			$batiment = $this->getBatimentTable()->getBatiment($salle->id_batiment);
				
			$html .= "<div id='titre_info_deces'>
					   <span id='titre_info_hospitalisation21' style='margin-left:-10px; cursor:pointer;'>
				          <img src='../img/light/plus.png' /> Infos sur l'hospitalisation
				       </span>
					  </div>
		              
					  <div id='barre'></div>";
			
			$html .= "<div id='info_hospitalisation21'>";
			$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
			$html .= "<tr style='width: 80%;'>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date d&eacute;but:</a><br><p style='font-weight:bold; font-size:17px;'>" . $this->controlDate->convertDateTime($hospitalisation->date_debut) . "</p></td>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Batiment:</a><br><p style=' font-weight:bold; font-size:17px;'>".$batiment->intitule."</p></td>";
			$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Salle:</a><br><p style=' font-weight:bold; font-size:17px;'>".$salle->numero_salle."</p></td>";
			$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Lit:</a><br><p style=' font-weight:bold; font-size:17px;'>".$lit->intitule."</p></td>";
			$html .= "</tr>";
			$html .= "</table>";
			$html .= "</div>";
		}
		
		if($terminer == 0) {
			$html .="<div style='width: 100%; height: 100px;'>
	    		     <div style='margin-left:40px; color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:left;'>
                        <img  src='".$this->path."/images_icons/fleur1.jpg' />
                     </div>";
			$html .="<div class='block' id='thoughtbot' style='vertical-align: bottom; padding-left:60%; margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button type='submit' id='terminer'>Terminer</button></div>
                     </div>";
		}
		/***
		 * UTILISER UNIQUEMENT DANS LA PAGE POUR LA LIBERATION DU PATIENT EN COURS D'HOSPITALISATION
		*/
		else if($terminer == 111) {
			$html .="<div style='width: 100%; height: 270px;'>";
				
			$html .= "<div id='titre_info_deces' >Infos sur la lib&eacute;ration du patient </div>
		              <div id='barre'></div>";
				
			$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
			$formLiberation = new LibererPatientForm();
			$data = array('id_demande_hospi' => $id_demande_hospi);
			$formLiberation->populateValues($data);
				
			$formRow = new FormRow();
			$formTextArea = new FormTextarea();
			$formHidden = new FormHidden();
				
			$html .="<form id='Formulaire_Liberer_Patient'  method='post' action='".$chemin."/consultation/liberer-patient'>";
			$html .=$formHidden($formLiberation->get('id_demande_hospi'));
			$html .=$formHidden($formLiberation->get('temoin_transfert'));
			$html .=$formHidden($formLiberation->get('id_cons'));
			$html .="<div style='width: 80%; margin-left: 195px;'>";
			$html .="<table id='form_patient' style='width: 100%; '>
					 <tr class='comment-form-patient' style='width: 100%'>
					   <td id='note_soin'  style='width: 45%; '>". $formRow($formLiberation->get('resumer_medical')).$formTextArea($formLiberation->get('resumer_medical'))."</td>
					   <td id='note_soin'  style='width: 45%; '>". $formRow($formLiberation->get('motif_sorti')).$formTextArea($formLiberation->get('motif_sorti'))."</td>
					   <td  style='width: 10%;'><a href='javascript:vider_liberation()'><img id='test' style=' margin-left: 25%;' src='../images_icons/118.png' title='vider tout'></a></td>
					 </tr>
					 </table>";
			$html .="</div>";
				
			$html .="<div style=' margin-left:40px; color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:left;'>
                        <img  src='".$this->path."/images_icons/fleur1.jpg' />
                     </div>";
				
			$html .="<div style='width: 10%; padding-left: 30%; float:left;'>";
			$html .="<div class='block' id='thoughtbot' style=' float:left; width: 30%; vertical-align: bottom;  margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button id='terminerLiberer'>Annuler</button></div>";
            $html .="</div>";
			
            $html .="<div class='block' id='thoughtbot' style=' float:left; width: 30%; vertical-align: bottom;  margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button type='submit' id='liberer'>Lib&eacute;rer</button></div>";
			$html .="</form>";
		
			$html .="<script>
					  function vider_liberation(){
	                   $('#resumer_medical').val('');
	                   $('#motif_sorti').val('');
		              }
					  //$('#resumer_medical, #motif_sorti').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
					  $('#resumer_medical, #motif_sorti').css({'font-size':'17px'});
					</script>
					";
		}
		$html .="</div>";
		
		$html .="<script>
				  listepatient();
				  initAnimationVue();
				  animationPliantDepliant21();
		          animationPliantDepliant41();
				  
				  var clickUneSeuleFois = 0;
				  $('#prescriptionOrdonnance').click(function(){ 
			        $( '#confirmationDeLaLiberation' ).dialog( 'close' ); 
			        PrescriptionOrdonnancePopup();
			        $('#PrescriptionOrdonnancePopupInterface').dialog('open');
			        if(clickUneSeuleFois == 0){ 
				       $('#ajouter_medicament').trigger('click'); 
				       $('#impressionPdf').toggle(false); 
				       $('#id_personneForOrdonnance').val(".$id_personne.");
				       $('#id_consForOrdonnance').val('".$id_cons."');
				       		
				       clickUneSeuleFois = 1;
	                }
			        return false;
		          });
				
				 </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function listeSoinsPourDetailInfoLiberationPatient($id_hosp) {

		$liste_soins = $this->getSoinHospitalisationTable()->getAllSoinhospitalisation($id_hosp);
		$html = "";
		$this->getDateHelper();
		
		$today = new \DateTime();
		$aujourdhui = $today->format('Y-m-d');
		
		$html .="<div style='margin-right: 10px; float:right; font-family: Times New Roman; font-size: 15px; color: green;'>
				   <i style='cursor:pointer;' id='afficherTerminer'> Termin&eacute; </i> |
				   <i style='cursor:pointer;' id='afficherEncours'> Encours </i> |
				   <i style='cursor:pointer;' id='afficherAvenir'> A venir </i>
				</div>";
			
		$html .="<table class='table table-bordered tab_list_mini'  style='margin-top:10px; margin-bottom:20px; width:100%;' id='listeSoin'>";
			
		$html .="<thead style='width: 100%;'>
				  <tr style='height:40px; width:100%; cursor:pointer;'>
					<th style='width: 23%;'>M&eacute;dicament</th>
					<th style='width: 21%;'>Voie d'administration</th>
					<th style='width: 21%;'>Dosage & Fr&eacute;quence</th>
					<th style='width: 17%;'>Heure suivante</th>
				    <th style='width: 12%;'>Options</th>
				    <th style='width: 6%;'>Etat</th>
				  </tr>
			     </thead>";
			
		$html .="<tbody style='width: 100%;'>";
		
		rsort($liste_soins);
		foreach ($liste_soins as $cle => $Liste){
			//Récupération de l'heure suivante pour l'application du soin par l'infirmier
			$heureSuivante = $this->getSoinHospitalisationTable()->getHeureSuivantePourAujourdhui($Liste['id_sh']);
		
			$idHeure = null;
			$heureSuiv = null;
			if($heureSuivante){
				$heureActuelleH = $today->format('H');
				$heureSuivanteH = substr($heureSuivante['heure'], 0, 2);
		
				if($heureSuivanteH-$heureActuelleH == 1){
					$heureActuelleM = $today->format('i');
					$heureSuivanteM = 59;
					$diff = $heureSuivanteM - $heureActuelleM;
						
					if($diff <= 15){
						$heureSuiv = "<khass id='alertHeureApplicationSoinUrgent".$Liste['id_sh']."' style='color: red; font-weight: bold; font-size: 20px; color: red;'>".$heureSuivante['heure']."
								      </khass>
								      <!-- i  id='clickOK' style='padding-left: 20px; color: green; font-family: Venus Rising; font-size: 18px; cursor:pointer;' > OK </i-->
								      <audio id='audioPlayer' src='../images_icons/alarme.mp3' ></audio>";
						$play = true;
					}else {
						$heureSuiv = "<khass id='alertHeureApplicationSoin' style='color: red; font-weight: bold; font-size: 20px; color: red;'>".$heureSuivante['heure']."</khass>";
					}
						
				}else {
					$heureSuiv = "<khass style='color: red; font-weight: bold; font-size: 20px;'>".$heureSuivante['heure']."</khass>";
				}
				$idHeure = $heureSuivante['id_heure'];
			}
				
				
			$html .="<tr style='width: 100%;' id='".$Liste['id_sh']."'>";
			$html .="<td style='width: 23%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['medicament']."</div></td>";
			$html .="<td style='width: 21%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['voie_administration']."</div></td>";
			$html .="<td style='width: 21%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['dosage']." - ".$Liste['frequence']."</div></td>";
		
			if($heureSuiv == null){
				$JourSuivant = $this->getSoinHospitalisationTable()->getDateApresDateDonnee($Liste['id_sh'], $aujourdhui);
				$HeuresPourAujourdhui = $this->getSoinHospitalisationTable()->getHeuresPourAujourdhui($Liste['id_sh']);
				if($JourSuivant && $HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:16px;'>
							     ".$this->controlDate->convertDate($JourSuivant['date'])." - ".$JourSuivant['heure']."
							     <khassSpan style='font-size: 10px;' > soin_encours </khassSpan>
							   </div>
		
							 </td>";
				}elseif($JourSuivant && !$HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:16px;'>
							     ".$this->controlDate->convertDate($JourSuivant['date'])." - ".$JourSuivant['heure']."
							     <khassSpan style='font-size: 10px;' > soin_avenir </khassSpan>
							   </div>
							 </td>";
				}elseif(!$JourSuivant && $HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:17px;'>
							      Termin&eacute;
							      <khassSpan style='font-size: 10px;' > soin_encours </khassSpan>
							   </div>
		
							</td>";
				}elseif(!$JourSuivant && !$HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:17px;'>
							      Termin&eacute;
							      <khassSpan style='font-size: 10px;' > soin_terminer </khassSpan>
							   </div>
							</td>";
				}
		
			}else{
				$html .="<td style='width: 17%;'>
						   <div id='inform' style='float:left; font-weight:bold; font-size:17px;'>
						     ".$heureSuiv."
						     <khassSpan style='font-size: 10px;' > soin_encours </khassSpan>
						   </div>
						 </td>";
			}
				
			if($Liste['appliquer'] == 0) {
				$html .="<td style='width: 12%;'> <a href='javascript:vuesoin(".$Liste['id_sh'].") '>
					       <img class='visualiser".$Liste['id_sh']."' style='display: inline;' src='../images_icons/voird.png'  title='d&eacute;tails' />
					  </a>&nbsp";
		
				$html .="<span>
				    	 <img style='color: white; opacity: 0.15;' src='../images_icons/modifier.png'/>
					     </span>&nbsp;";
				$html .="<span> <img  style='color: white; opacity: 0.15;' src='../images_icons/sup.png' /> </span>
				         </td>";
		
					
				$html .="<td style='width: 6%;'>
					       <img class='etat_oui".$Liste['id_sh']."' style='margin-left: 20%;' src='../images_icons/non.png' title='pas totalement appliqu&eacute;' />
					     &nbsp;
				         </td>";
			}else {
		
				$html .="<td style='width: 12%;'> <a href='javascript:vuesoinApp(".$Liste['id_sh'].") '>
					       <img class='visualiser".$Liste['id_sh']."' style='display: inline;' src='../images_icons/voird.png' title='d&eacute;tails' />
					  </a>&nbsp";
		
				$html .="<a>
					    	<img class='modifier".$Liste['id_sh']."' style='color: white; opacity: 0.15;' src='../images_icons/modifier.png' />
					     </a>&nbsp;
		
				         <a >
					    	<img class='supprimer".$Liste['id_sh']."' style='color: white; opacity: 0.15;' src='../images_icons/sup.png' />
					     </a>
				         </td>";
					
				$html .="<td style='width: 6%;'>
					       <img class='etat_non".$Liste['id_sh']."' style='margin-left: 20%;' src='../images_icons/oui.png' title='totalement appliqu&eacute;' />
					     &nbsp;
				         </td>";
		
			}
		
			$html .="</tr>";
		
			$html .="<script>
					  $('.visualiser".$Liste['id_sh']." ').mouseenter(function(){
	                    var tooltips = $( '.visualiser".$Liste['id_sh']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.visualiser".$Liste['id_sh']."').mouseleave(function(){
	                    var tooltips = $( '.visualiser".$Liste['id_sh']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /************************/
	                  /************************/
	                  /************************/
                      $('.modifier".$Liste['id_sh']." ').mouseenter(function(){
	                    var tooltips = $( '.modifier".$Liste['id_sh']." ' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
				      $('.modifier".$Liste['id_sh']." ').mouseleave(function(){
	                    var tooltips = $( '.modifier".$Liste['id_sh']." ' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.supprimer".$Liste['id_sh']." ').mouseenter(function(){
	                    var tooltips = $( '.supprimer".$Liste['id_sh']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.supprimer".$Liste['id_sh']."').mouseleave(function(){
	                    var tooltips = $( '.supprimer".$Liste['id_sh']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
		
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_oui".$Liste['id_sh']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_oui".$Liste['id_sh']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_oui".$Liste['id_sh']."').mouseleave(function(){
	                    var tooltips = $( '.etat_oui".$Liste['id_sh']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_non".$Liste['id_sh']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_non".$Liste['id_sh']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_non".$Liste['id_sh']."').mouseleave(function(){
	                    var tooltips = $( '.etat_non".$Liste['id_sh']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	           
	                  function FaireClignoterPourAlerte".$Liste['id_sh']." (){
                          $('#alertHeureApplicationSoinUrgent".$Liste['id_sh']."').fadeOut(250).fadeIn(200);
                      }
		
                      $(function(){
                          setInterval('FaireClignoterPourAlerte".$Liste['id_sh']." ()',500);
                      });
		
			        </script>";
		}
		$html .="</tbody>";
		$html .="</table>";
		
		$html .="<style>
				  #listeDataTable{
	                /*margin-left: 185px;*/
                  }
		
				  div .dataTables_paginate
                  {
				    /*margin-right: 20px;*/
                  }
		
				  #listeSoin tbody tr{
				    background: #fbfbfb;
				  }
		
				  #listeSoin tbody tr:hover{
				    background: #fefefe;
				  }
				 </style>";
		$html .="<script> $('#listeSoin khassSpan').toggle(false); listepatient (); listeDesSoins(); $('#afficherTerminer').trigger('click'); </script>";
		
		return $html;
		
	}
	
	public function detailInfoLiberationPatientAction() {
		$this->getDateHelper();
		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
		$id_personne = $this->params()->fromPost('id_personne',0);
		$id_cons = $this->params()->fromPost('id_cons',0);
		$encours = $this->params()->fromPost('encours',0);
		$terminer = $this->params()->fromPost('terminer',0);
		$id_demande_hospi = $this->params()->fromPost('id_demande_hospi',0);
		
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);
		
		$demande = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdcons($id_cons);
		
		$date = $this->controlDate->convertDate( $unPatient['DATE_NAISSANCE'] );
		
		$html  = "<div style='width:100%;'>";
			
		$html .= "<div style='width: 18%; height: 180px; float:left;'>";
		$html .= "<div id='photo' style='float:left; margin-left:40px; margin-top:10px; margin-right:30px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "' ></div>";
		$html .= "</div>";
			
		$html .= "<div style='width: 65%; height: 180px; float:left;'>";
		$html .= "<table style='margin-top:10px; float:left; width: 100%;'>";
		$html .= "<tr style='width: 100%;'>";
		
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unPatient['NOM'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Lieu de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['LIEU_NAISSANCE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; actuelle:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['NATIONALITE_ACTUELLE'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'></td>";
		
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['PRENOM'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['TELEPHONE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; d'origine:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['NATIONALITE_ORIGINE']. "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Email:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unPatient['EMAIL'] . "</p></td>";
		
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['ADRESSE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Profession:</a><br><p style=' font-weight:bold; font-size:17px;'>" .  $unPatient['PROFESSION'] . "</p></td>";
		
		$html .= "<td style='width: 30%; height: 50px;'></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .="</div>";
			
		$html .= "<div style='width: 17%; height: 200px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		$html .= "<div id='titre_info_deces'>
				     <span id='titre_info_demande' style='margin-left: -10px; cursor:pointer;'>
				        <img src='".$chemin."/img/light/plus.png' /> D&eacute;tails des infos sur la demande
				     </span>
				  </div>
		          <div id='barre'></div>";
		
		$html .= "<div id='info_demande'>";
		$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
		$html .= "<tr style='width: 80%;'>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Consultation:</a><br><p style='font-weight:bold; font-size:17px;'>" . $id_cons . "</p></td>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->controlDate->convertDateTime($demande['Datedemandehospi']) . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date fin pr&eacute;vue:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->controlDate->convertDate($demande['date_fin_prevue_hospi']) . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>M&eacute;decin demandeur:</a><br><p style=' font-weight:bold; font-size:17px;'>" .$demande['PrenomMedecin'].' '.$demande['NomMedecin']. "</p></td>";
		$html .= "</tr>";
		$html .= "</table>";
		
		$html .="<table style='margin-top:0px; margin-left:195px; width: 70%;'>";
		$html .="<tr style='width: 70%'>";
		$html .="<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 20%; '><a style='text-decoration:underline; font-size:13px;'>Motif de la demande:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'>". $demande['motif_demande_hospi'] ."</p></td>";
		$html .="<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 20%; '><a style='text-decoration:underline; font-size:13px;'>Note:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'> </p></td>";
		$html .="</tr>";
		$html .="</table>";
		$html .= "</div>";
		
		/***
		 * UTILISER UNIQUEMENT DANS LA VUE DE LA LISTE DES PATIENTS EN COURS D'HOSPITALISATION
		*/
		if($encours == 111) {
			$hospitalisation = $this->getHospitalisationTable()->getHospitalisationWithCodedh($id_demande_hospi);
			$lit_hospitalisation = $this->getHospitalisationlitTable()->getHospitalisationlit($hospitalisation->id_hosp);
			$lit = $this->getLitTable()->getLit($lit_hospitalisation->id_materiel);
			$salle = $this->getSalleTable()->getSalle($lit->id_salle);
			$batiment = $this->getBatimentTable()->getBatiment($salle->id_batiment);
		
			$html .= "<div id='titre_info_deces'>
					   <span id='titre_info_hospitalisation' style='margin-left:-10px; cursor:pointer;'>
				          <img src='".$chemin."/img/light/plus.png' /> Infos sur l'hospitalisation
				       </span>
					  </div>
		              <div id='barre'></div>";
				
			$html .= "<div id='info_hospitalisation'>";
			$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
			$html .= "<tr style='width: 80%;'>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date d&eacute;but:</a><br><p style='font-weight:bold; font-size:17px;'>" . $this->controlDate->convertDateTime($hospitalisation->date_debut) . "</p></td>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Batiment:</a><br><p style=' font-weight:bold; font-size:17px;'>".$batiment->intitule."</p></td>";
			$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Salle:</a><br><p style=' font-weight:bold; font-size:17px;'>".$salle->numero_salle."</p></td>";
			$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Lit:</a><br><p style=' font-weight:bold; font-size:17px;'>".$lit->intitule."</p></td>";
			$html .= "</tr>";
			$html .= "</table>";
			$html .= "</div>";
		}
		
		$html .= "<div id='titre_info_deces'>
				    <span id='titre_info_liste' style='margin-left:-10px; cursor:pointer;'>
				      <img src='".$chemin."/img/light/plus.png' /> Liste des soins
				    </span>
				  </div>
		          <div id='barre'></div>";
		
		$hospitalisation = $this->getHospitalisationTable()->getHospitalisationWithCodedh($id_demande_hospi);
		$html .= "<div id='info_liste'>";
		$html .= $this->listeSoinsPourDetailInfoLiberationPatient($hospitalisation->id_hosp);
		$html .= "</div>";
		
		$html .= "<div id='titre_info_deces'>
				   <span id='titre_info_liberation' style='margin-left:-10px; cursor:pointer;'>
				      <img src='".$chemin."/img/light/plus.png' /> Infos sur la lib&eacute;ration du patient
				   </span>
				  </div>
		          <div id='barre'></div>";
		
		//Pour les infos sur le transfert du patient hospitalisé
		//Pour les infos sur le transfert du patient hospitalisé
		//Pour les infos sur le transfert du patient hospitalisé
		$infoTransfert = $this->getTransfererPatientServiceTable()->getPatientMedecinDonnees($id_cons);
		
		//POUR LES MEDICAMENTS
		//POUR LES MEDICAMENTS
		//POUR LES MEDICAMENTS
		// INSTANTIATION DE L'ORDONNANCE
		$infoOrdonnance = $this->getOrdonnanceTable()->getOrdonnanceHospi($id_cons);
		
		if($infoOrdonnance) {
			$idOrdonnance = $infoOrdonnance->id_document;
			//LISTE DES MEDICAMENTS PRESCRITS
			$listeMedicamentsPrescrits = $this->getOrdonnanceTable()->getMedicamentsParIdOrdonnance($idOrdonnance);
			$nbMedPrescrit = $listeMedicamentsPrescrits->count();
		}else{
			$nbMedPrescrit = 0;
			$listeMedicamentsPrescrits = null;
		}

		//------------------------------------------------------
		//------------------------------------------------------
		//------------------------------------------------------
		$html .= "<div id='info_liberation'>";
		$html .= "<table style='margin-top:0px; margin-left:195px; width: 80%;'>";
		$html .= "<tr style='width: 80%'>";

		if($infoTransfert){
			$html .= "<td style='padding-top: 10px; width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:14px;'>Date du transfert:</a><br><p style='font-weight:bold; font-size:17px;'>" . $this->controlDate->convertDateTime($hospitalisation->date_fin) . "</p></td>";
			$html .= "<td style='padding-top: 10px; width: 32%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:14px;'>Pr&eacute;nom & nom du m&eacute;decin:</a><br><p style='font-weight:bold; font-size:17px;'>".$infoTransfert['PrenomMedecin']." ".$infoTransfert['NomMedecin']."</p></td>";
			$html .= "<td style='padding-top: 10px; width: 43%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:14px;'>Service d'accueil:</a><br><p style='font-weight:bold; font-size:17px;'>".$infoTransfert['NomService']."</p></td>";
		} else {
			$html .= "<td style='padding-top: 10px; width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:14px;'>Date de la lib&eacute;ration:</a><br><p style='font-weight:bold; font-size:17px;'>" . $this->controlDate->convertDateTime($hospitalisation->date_fin) . "</p></td>";
			
			if($infoOrdonnance){
				$html .= "<td style='padding-top: 10px; width: 25%; height: 50px; vertical-align: top;'><img id='afficherOrdonnance' style='cursor:pointer;' src='".$chemin."/images_icons/pdf.png' /> <span style='font-weight:bold; font-size:17px; font-family: Times  New Roman; color: #065d10;'> Ordonnance </span></td>";
				$html .= "<td style='padding-top: 10px; width: 25%; height: 50px; vertical-align: top;'></td>";
				$html .= "<td style='padding-top: 10px; width: 25%; height: 50px; vertical-align: top;'></td>";
			}

		}
		
		$html .= "</tr>";
		$html .= "</table>";
		
		$html .= "<table style='margin-top:0px; margin-left:195px; width: 80%;'>";
		$html .= "<tr style='width: 80%'>";
		$html .= "<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 35%; '><a style='text-decoration:underline; font-size:14px;'>R&eacute;sum&eacute; m&eacute;dical:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'>" . $hospitalisation->resumer_medical . "</p></td>";
		$html .= "<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 35%; '><a style='text-decoration:underline; font-size:14px;'>Motif de la sortie:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'>". $hospitalisation->motif_sorti ."</p></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .= "</div>";
		
		if($terminer == 0) {
			$html .="<div style='width: 100%; height: 100px;'>
	    		     <div style='margin-left:40px; color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:left;'>
                        <img  src='".$chemin."/images_icons/fleur1.jpg' />
                     </div>";
			$html .="<div class='block' id='thoughtbot' style='vertical-align: bottom; padding-left:60%; margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button type='submit' id='terminerdetailhospi'>Terminer</button></div>
                     </div>";
		}
		
		$html .="<script>
				  listepatient();
				  initAnimation();
				  animationPliantDepliant();
				  animationPliantDepliant2();
				  animationPliantDepliant3();
		          animationPliantDepliant4();
				
				  $('#nbMedecamentPourVisualisation').val(".$nbMedPrescrit.");
				  		
				  //$('#medicament_01').val('test');
				  var i = 1;
				 </script>";
		$html .="<style> #info_liste{ margin-left:195px; width: 80%;}   </style>";
		
		
		
		//Remplissage automatique des champs
		if($nbMedPrescrit != 0) {
		   foreach($listeMedicamentsPrescrits as $Medicaments){
		        $html .="<script> setTimeout(function(){ $('#medicament_0'+i).val('".$Medicaments['Intitule']."'); }, 2000);  </script>";
		        $html .="<script> setTimeout(function(){ $('#forme_'+i).val('".$Medicaments['FORME']."'); }, 2000);</script>";
		        $html .="<script> setTimeout(function(){ $('#nb_medicament_'+i).val('".substr($Medicaments['QUANTITE'], 0, strpos($Medicaments['QUANTITE'], ' '))."'); }, 2000);  </script>";
		        $html .="<script> setTimeout(function(){ $('#quantite_'+i).val('".substr($Medicaments['QUANTITE'], strpos($Medicaments['QUANTITE'], ' ')+1)."'); i++; }, 2000);  </script>";
		   }
		   
		   $html .="<script>
		   			  setTimeout(function(){ 
		   		        $('#listeMedicaments input, .form-duree_ input, .ordonnance input').attr('disabled', true).css({'background':'#f8f8f8'}); 
		   		        $('#controls_medicament div').toggle(false);
		   		        $('#iconeMedicament_supp_vider a img').toggle(false);
	                    $('#bouton_Medicament_modifier_demande').toggle(false);
	                    $('#bouton_Medicament_valider_demande').toggle(false);
	                    $('#increm_decrem img').toggle(false);
		              }, 2000); 
		   		    </script>";
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	public function raffraichirListeSoinsPrescrit($id_hosp){
	
		$liste_soins = $this->getSoinHospitalisationTable()->getAllSoinhospitalisation($id_hosp);
		$html = "";
		$this->getDateHelper();
		
		$today = new \DateTime();
		$aujourdhui = $today->format('Y-m-d');
		
		$html .="<div style='margin-right: 10px; float:right; font-family: Times New Roman; font-size: 15px; color: green;'>
				   <i style='cursor:pointer;' id='afficherTerminer'> Termin&eacute; </i> |
				   <i style='cursor:pointer;' id='afficherEncours'> Encours </i> |
				   <i style='cursor:pointer;' id='afficherAvenir'> A venir </i>
				</div>";
			
		$html .="<table class='table table-bordered tab_list_mini'  style='margin-top:10px; margin-bottom:20px; width:100%;' id='listeSoin'>";
			
		$html .="<thead style='width: 100%;'>
				  <tr style='height:40px; width:100%; cursor:pointer;'>
					<th style='width: 23%;'>M&eacute;dicament</th>
					<th style='width: 21%;'>Voie d'administration</th>
					<th style='width: 21%;'>Dosage <span style='font-weight: normal;'>X</span> Fr&eacute;quence</th>
					<th style='width: 17%;'>Heure suivante</th>
				    <th style='width: 12%;'>Options</th>
				    <th style='width: 6%;'>Etat</th>
				  </tr>
			     </thead>";
			
		$html .="<tbody style='width: 100%;'>";
	
		rsort($liste_soins);
		foreach ($liste_soins as $cle => $Liste){
			//Récupération de l'heure suivante pour l'application du soin par l'infirmier
			$heureSuivante = $this->getSoinHospitalisationTable()->getHeureSuivantePourAujourdhui($Liste['id_sh']);
			$heurePrecedente = $this->getSoinHospitalisationTable()->getHeurePrecedentePourAujourdhui($Liste['id_sh']);
				
			$temoin = 0;
			$idHeure = null;
			$heureSuiv = null;
			if($heureSuivante || $heurePrecedente){
				$heurePrecedenteH = substr($heurePrecedente['heure'], 0, 2);
				
				$heureActuelleH = $today->format('H');
				$heureSuivanteH = substr($heureSuivante['heure'], 0, 2);
				
				if($heureSuivanteH-$heureActuelleH == 1){
					$heureActuelleM = $today->format('i');
					$heureSuivanteM = 59;
					$diff = $heureSuivanteM - $heureActuelleM;
					
					if($diff <= 10){
						$heureSuiv = "<khass id='alertHeureApplicationSoinUrgent".$Liste['id_sh']."' style='color: orange; font-weight: bold; font-size: 20px;'>".$heureSuivante['heure']."
								      </khass>";
						              $play = true;
					}else if ($diff <= 30) {
						$heureSuiv = "<khass id='alertHeureApplicationSoin".$Liste['id_sh']."' style='color: orange; font-weight: bold; font-size: 20px;'>".$heureSuivante['heure']."</khass>";
					}else {
						$heureSuiv = "<khass style='color: orange; font-weight: bold; font-size: 20px;'>".$heureSuivante['heure']."</khass>";
					}
					
				}else 
				    if ($heurePrecedenteH == $heureActuelleH){
				    	$heureActuelleM = $today->format('i');
				    	if($heureActuelleM <= 30){
				    		$heureSuiv ="<khass id='alertHeureApplicationSoinUrgent2".$Liste['id_sh']."' style='color: red; font-weight: bold; font-size: 20px;'>".$heurePrecedente['heure']."
								         </khass>
								         <audio id='audioPlayer' src='../images_icons/alarme.mp3' ></audio>";
				    		$temoin = 1;
				    		$play = true;
				    	}else {
				    		if($heureSuivante['heure']){
				    			$heureSuiv = "<khass style='color: orange; font-weight: bold; font-size: 20px;'>".$heureSuivante['heure']."</khass>";
				    		}				    	}
				    }
					else {
						if($heureSuivante['heure']){
							$heureSuiv = "<khass style='color: orange; font-weight: bold; font-size: 20px;'>".$heureSuivante['heure']."</khass>";
						}
					}
			}
			
			
			$html .="<tr style='width: 100%;' id='".$Liste['id_sh']."'>";
			$html .="<td style='width: 23%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['medicament']."</div></td>";
			$html .="<td style='width: 21%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['voie_administration']."</div></td>";
			$html .="<td style='width: 21%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['dosage']." <span style='font-weight: normal; font-family: arial; color: green; font-size: 13px;'>X</span> ".$Liste['frequence']."</div></td>";
	
			if($heureSuiv == null){
				$JourSuivant = $this->getSoinHospitalisationTable()->getDateApresDateDonnee($Liste['id_sh'], $aujourdhui);
				$HeuresPourAujourdhui = $this->getSoinHospitalisationTable()->getHeuresPourAujourdhui($Liste['id_sh']);
				if($JourSuivant && $HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:16px;'>
							     ".$this->controlDate->convertDate($JourSuivant['date'])." - ".$JourSuivant['heure']."
							     <khassSpan style='font-size: 10px;' > soin_encours </khassSpan>
							   </div>
							   
							 </td>";
				}elseif($JourSuivant && !$HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:16px;'>
							     ".$this->controlDate->convertDate($JourSuivant['date'])." - ".$JourSuivant['heure']."
							     <khassSpan style='font-size: 10px;' > soin_avenir </khassSpan>
							   </div>
							 </td>";
				}elseif(!$JourSuivant && $HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:17px;'>
							      Termin&eacute;
							      <khassSpan style='font-size: 10px;' > soin_encours </khassSpan>
							   </div>
							   
							</td>";
				}elseif(!$JourSuivant && !$HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:17px;'>
							      Termin&eacute;
							      <khassSpan style='font-size: 10px;' > soin_terminer </khassSpan>
							   </div>
							</td>";
				}

			}else{
				$html .="<td style='width: 17%;'>
						   <div id='inform' style='float:left; font-weight:bold; font-size:17px;'>
						     ".$heureSuiv."
						     <khassSpan style='font-size: 10px;' > soin_encours </khassSpan>
						   </div>
						 </td>";
			}
			
			if($Liste['appliquer'] == 0) {
				$html .="<td style='width: 12%;'> <a href='javascript:vuesoin(".$Liste['id_sh'].") '>
					       <img class='visualiser".$Liste['id_sh']."' style='display: inline;' src='../images_icons/voird.png'  title='d&eacute;tails' />
					  </a>&nbsp";
	
				//Si au moin pour une heure un soin a ete applique impossible de Supprimer le soin
				$ListeHeureSoinApplique = $this->getSoinHospitalisationTable()->getHeureAppliquer($Liste['id_sh']);
				if($ListeHeureSoinApplique != 0){
					$html .="<span>
					    	  <img style='color: white; opacity: 0.15;' src='../images_icons/modifier.png'/>
					         </span>&nbsp;";
					$html .="<span> <img  style='color: white; opacity: 0.15;' src='../images_icons/sup.png' /> </span>
				             </td>";
				} else {
					$html .="<a href='javascript:modifiersoin(".$Liste['id_sh'].",".$Liste['id_hosp'].")'>
					    	  <img class='modifier".$Liste['id_sh']."'  src='../images_icons/modifier.png' title='modifier'/>
					         </a>&nbsp;";
					$html .="<a href='javascript:supprimersoin(".$Liste['id_sh'].",".$Liste['id_hosp'].")'>
					    	  <img class='supprimer".$Liste['id_sh']."'  src='../images_icons/sup.png' title='annuler' />
					         </a>
				             </td>";
				}
				
					
				$html .="<td style='width: 6%;'>
					       <img class='etat_oui".$Liste['id_sh']."' style='margin-left: 20%;' src='../images_icons/non.png' title='pas totalement appliqu&eacute;' />
					     &nbsp;
				         </td>";
			}else {
	
				$html .="<td style='width: 12%;'> <a href='javascript:vuesoinApp(".$Liste['id_sh'].") '>
					       <img class='visualiser".$Liste['id_sh']."' style='display: inline;' src='../images_icons/voird.png' title='d&eacute;tails' />
					  </a>&nbsp";
				
				$html .="<a>
					    	<img class='modifier".$Liste['id_sh']."' style='color: white; opacity: 0.15;' src='../images_icons/modifier.png' />
					     </a>&nbsp;
	
				         <a >
					    	<img class='supprimer".$Liste['id_sh']."' style='color: white; opacity: 0.15;' src='../images_icons/sup.png' />
					     </a>
				         </td>";
					
				$html .="<td style='width: 6%;'>
					       <img class='etat_non".$Liste['id_sh']."' style='margin-left: 20%;' src='../images_icons/oui.png' title='totalement appliqu&eacute;' />
					     &nbsp;
				         </td>";
	
			}
	
			$html .="</tr>";
	
			$html .="<script>
					  $('.visualiser".$Liste['id_sh']." ').mouseenter(function(){
	                    var tooltips = $( '.visualiser".$Liste['id_sh']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.visualiser".$Liste['id_sh']."').mouseleave(function(){
	                    var tooltips = $( '.visualiser".$Liste['id_sh']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /************************/
	                  /************************/
	                  /************************/
                      $('.modifier".$Liste['id_sh']." ').mouseenter(function(){
	                    var tooltips = $( '.modifier".$Liste['id_sh']." ' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
				      $('.modifier".$Liste['id_sh']." ').mouseleave(function(){
	                    var tooltips = $( '.modifier".$Liste['id_sh']." ' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.supprimer".$Liste['id_sh']." ').mouseenter(function(){
	                    var tooltips = $( '.supprimer".$Liste['id_sh']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.supprimer".$Liste['id_sh']."').mouseleave(function(){
	                    var tooltips = $( '.supprimer".$Liste['id_sh']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_oui".$Liste['id_sh']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_oui".$Liste['id_sh']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_oui".$Liste['id_sh']."').mouseleave(function(){
	                    var tooltips = $( '.etat_oui".$Liste['id_sh']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_non".$Liste['id_sh']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_non".$Liste['id_sh']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_non".$Liste['id_sh']."').mouseleave(function(){
	                    var tooltips = $( '.etat_non".$Liste['id_sh']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                    		
	                  function FaireClignoterPourAlerteUrgent".$Liste['id_sh']." (){
                          $('#alertHeureApplicationSoinUrgent".$Liste['id_sh']."').fadeOut(250).fadeIn(200);
                      }
                      $(function(){
                          setInterval('FaireClignoterPourAlerteUrgent".$Liste['id_sh']." ()',500);
                      });

                          		
                      function FaireClignoterPourAlerteUrgent2".$Liste['id_sh']." (){
                          $('#alertHeureApplicationSoinUrgent2".$Liste['id_sh']."').fadeOut(100).fadeIn(50);
                      }
                      $(function(){
                          setInterval('FaireClignoterPourAlerteUrgent2".$Liste['id_sh']." ()',500);
                      });
                          		
                          		
                      function FaireClignoterPourAlerte".$Liste['id_sh']." (){
                          $('#alertHeureApplicationSoin".$Liste['id_sh']."').fadeOut(900).fadeIn(800);
                      }
                      $(function(){
                          setInterval('FaireClignoterPourAlerte".$Liste['id_sh']." ()',4200);
                      });
                          		
			        </script>";
		}
		$html .="</tbody>";
		$html .="</table>";
	
		$html .="<style>
				  #listeDataTable{
	                /*margin-left: 185px;*/
                  }
	
				  div .dataTables_paginate
                  {
				    /*margin-right: 20px;*/
                  }
	
				  #listeSoin tbody tr{
				    background: #fbfbfb;
				  }
	
				  #listeSoin tbody tr:hover{
				    background: #fefefe;
				  }
				 </style>";
		$html .="<script> $('#listeSoin khassSpan').toggle(false); listepatient (); listeDesSoins(); </script>";
	
		return $html;
	
	}
	
	public function infoPatientHospiAction(){

		$this->getDateHelper();
		$id_personne = $this->params()->fromPost('id_personne',0);
		$administrerSoin = $this->params()->fromPost('administrerSoin',0);
		
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);
		
		$date = $this->controlDate->convertDate( $unPatient['DATE_NAISSANCE'] );
		
		$html  = "<div style='width:100%;'>";
			
		$html .= "<div style='width: 18%; height: 180px; float:left;'>";
		$html .= "<div id='photo' style='float:left; margin-left:40px; margin-top:10px; margin-right:30px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "' ></div>";
		$html .= "</div>";
			
		$html .= "<div style='width: 65%; height: 200px; float:left;'>";
		$html .= "<table style='margin-top:10px; float:left; width: 100%;'>";
		$html .= "<tr style='width: 100%;'>";
		
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unPatient['NOM'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Lieu de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['LIEU_NAISSANCE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; actuelle:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['NATIONALITE_ACTUELLE'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'></td>";
		
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['PRENOM'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['TELEPHONE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; d'origine:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['NATIONALITE_ORIGINE']. "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Email:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unPatient['EMAIL'] . "</p></td>";
		
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['ADRESSE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Profession:</a><br><p style=' font-weight:bold; font-size:17px;'>" .  $unPatient['PROFESSION'] . "</p></td>";
		
		$html .= "<td style='width: 30%; height: 50px;'></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .="</div>";
			
		$html .= "<div style='width: 17%; height: 200px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		if($administrerSoin != 111) {
			$html .= "<div id='titre_info_deces'>Attribution d'un lit</div>
		              <div id='barre'></div>";
		
			$html .= "<script>$('#salle, #division, #lit').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'17px'});</script>";
		}else if($administrerSoin == 111){
			$html .= "<script>$('#salle, #division, #lit').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'17px'});</script>";
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	public function listeSoinsPrescritsAction() {
		$id_hosp = $this->params()->fromPost('id_hosp', 0);
	
		$html = "<div id='titre_info_admis'> 
				  <span id='titre_info_liste_soin' style='margin-left:-5px; cursor:pointer; margin-top: 100px;'>
				    <img src='../img/light/minus.png' /> Liste des soins</div>
				  </span>
		        <div id='barre_admis'></div>";
		$html .="<div id='Liste_soins_deja_prescrit'>";
		$html .= $this->raffraichirListeSoinsPrescrit($id_hosp);
		$html .="</div>";
		
		$html .="<script> 
				  depliantPlus6();
				 </script>";
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	public function supprimerSoinAction() {
		$id_sh = $this->params()->fromPost('id_sh', 0);
		$this->getSoinHospitalisationTable()->supprimerHospitalisation($id_sh);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode () );
	}
	
	public function modifierSoinAction() {

		$id_sh = $this->params()->fromPost('id_sh', 0);
		
		$this->getDateHelper();
		$soin = $this->getSoinHospitalisationTable()->getSoinhospitalisationWithId_sh($id_sh);
		$heure = $this->getSoinHospitalisationTable()->getHeuresGroup($id_sh);
		
		$lesHeures = "";
		if($heure){
			for ($i = 0; $i<count($heure); $i++){
			if($i == count($heure)-1) {
					$lesHeures.= $heure[$i];
				} else {
					$lesHeures.= $heure[$i].'  -  ';
				}
			}
		}
		
		$form = new SoinmodificationForm();
		if($soin){
				
			$data = array(
					'medicament_m' => $soin->medicament,
 					'voie_administration_m' => $soin->voie_administration,
					'frequence_m' => $soin->frequence,
					'dosage_m' => $soin->dosage,
 					'date_application_m' => $this->controlDate->convertDate($soin->date_debut_application),
					'motif_m' => $soin->motif,
					'note_m' => $soin->note,
					'duree_m' => $soin->duree,
			);
				
			$form->populateValues($data);
		}
		
		$formRow = new FormRow();
		$formText = new FormText();
		$formSelect = new FormSelect();
		$formTextArea = new FormTextarea();
		
		$listeMedicament = $this->getConsommableTable()->listeDeTousLesMedicaments();
		
		$html ="<table id='form_patient' style='width: 100%;'>
		
		           <tr class='comment-form-patient' style='width: 100%;'>
		             <td style='width: 25%;'> ".$formRow($form->get('medicament_m')).$formText($form->get('medicament_m'))."</td>
		             <td style='width: 25%;'>".$formRow($form->get('voie_administration_m')).$formText($form->get('voie_administration_m'))."</td>
		             <td style='width: 25%;'>".$formRow($form->get('frequence_m')).$formText($form->get('frequence_m'))."</td>
		             <td style='width: 25%;'>".$formRow($form->get('dosage_m')).$formText($form->get('dosage_m'))."</td>
		           </tr>
		             		
		           <tr class='comment-form-patient' style='width: 100%;'>
		             <td style='width: 25%;'> ".$formRow($form->get('date_application_m')).$formText($form->get('date_application_m'))."</td>
		             <td style='width: 25%;'> ".$formRow($form->get('duree_m')).$formText($form->get('duree_m'))."</td>
             		 <td colspan='2' style='width: 25%;'>".$formRow($form->get('heure_recommandee_m')).$formText($form->get('heure_recommandee_m'))."</td>
		           </tr>
		         </table>
		
		         <table id='form_patient' style='width: 100%;'>
		           <tr class='comment-form-patient'>
		             <td id='note_soin' style='width: 40%; '>". $formRow($form->get('motif_m')).$formTextArea($form->get('motif_m'))."</td>
		             <td id='note_soin' style='width: 40%; '>". $formRow($form->get('note_m')).$formTextArea($form->get('note_m'))."</td>
		             <td  style='width: 10%;' id='ajouter'></td>
		             <td  style='width: 10%;'></td>
		           </tr>
		         </table>";
		$html .="<script>
				    //$('#medicament_m, #voie_administration_m, #frequence_m, #dosage_m, #date_application_m, #heure_recommandee_m, #motif_m, #note_m').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'18px'});
				    $('#duree_m, #heure_recommandee_m').attr('disabled', true).css({'background':'f9f9f9'});
				    $('#heure_recommandee_m').val('".$lesHeures."');
				    $(function() {
    	              $('.SlectBox_m').SumoSelect({ csvDispCount: 6 });
				    });
				    var myArrayMedicament = [''];
			        var j = 0;";
                foreach($listeMedicament as $Liste) {
                	$html .="myArrayMedicament[j++] = '". $Liste['INTITULE']."';"; 
                }
		$html .=" $('#medicament_m' ).autocomplete({
    		         source: myArrayMedicament
                     });
				     listepatient();
				 </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	public function getLesHeuresDuSoin($id_sh, $date){
	
		$heure = $this->getSoinHospitalisationTable()->getHeuresPourUneDate($id_sh, $date);
			
		$heureSuivante = $this->getSoinHospitalisationTable()->getHeureSuivantePourUneDate($id_sh, $date);
			
		$dateTime = new \DateTime();
		$aujoudhui = $dateTime->format('Y-m-d');
	
		if($date < $aujoudhui){
			$heureSuivante = null;
		}
	
		$lesHeures = "";
			
		//S'il y'a une heure suivante et bien-sur des heures pour appliquer des soins
		if($heure && $heureSuivante){
			for ($i = 0; $i<count($heure); $i++){
				$appliquer = $this->getSoinHospitalisationTable()->getHeureAppliqueePourUneDate($id_sh, $heure[$i], $date)['applique'];
					
				if($i == count($heure)-1) {
					if($heureSuivante['heure'] == $heure[$i]){
						$lesHeures.= '<span id="clignoterHeure" style="font-weight: bold; color: orange;">'.$heure[$i].'</span>';
					}else{
							
						if($heure[$i] < $heureSuivante['heure'] &&  $appliquer == 0){
							$lesHeures.= '<span style="font-weight: bold; color: red;">'.$heure[$i].'</span> ';
						} else if ($heure[$i] > $heureSuivante['heure']){
							$lesHeures.= '<span style="color: #ccc;">'.$heure[$i].'</span>';
						  } else {
						  	$lesHeures.= $heure[$i];
						  }
							
					}
				} else {
					if($heureSuivante['heure'] == $heure[$i]){
						$lesHeures.= '<span id="clignoterHeure" style="font-weight: bold; color: orange;">'.$heure[$i].'</span>  -  ';
					}else{
							
						if($heure[$i] < $heureSuivante['heure'] &&  $appliquer == 0){
							$lesHeures.= '<span style="font-weight: bold; color: red;">'.$heure[$i].'</span>  -  ';
						} else if ($heure[$i] > $heureSuivante['heure']){
							$lesHeures.= '<span style="color: #ccc;">'.$heure[$i].' - </span>';
						  } else {
						  	$lesHeures.= $heure[$i].' - ';
						  }
							
					}
				}
			}
		}
			
		//S'il n'y a plus une heure suivante c'est a dire toutes les heures sont passées
		if($heure && !$heureSuivante && $date == $aujoudhui){
			for ($i = 0; $i<count($heure); $i++){
				$appliquer = $this->getSoinHospitalisationTable()->getHeureAppliqueePourUneDate($id_sh, $heure[$i], $date)['applique'];
					
				if($i == count($heure)-1) {
	
					if($appliquer == 0){
						$lesHeures.= '<span style="font-weight: bold; color: red;">'.$heure[$i].'</span> ';
					} else {
						$lesHeures.= $heure[$i];
					}
	
				} else {
	
					if($appliquer == 0){
						$lesHeures.= '<span style="font-weight: bold; color: red;">'.$heure[$i].'</span>  -  ';
					} else {
						$lesHeures.= $heure[$i].'  -  ';
					}
	
				}
					
			}
		}
		
		//S'il n'y a plus une heure suivante c'est a dire toutes les heures sont passées
		if($heure && !$heureSuivante && $date != $aujoudhui){
			for ($i = 0; $i<count($heure); $i++){
				$appliquer = $this->getSoinHospitalisationTable()->getHeureAppliqueePourUneDate($id_sh, $heure[$i], $date)['applique'];
					
				if($i == count($heure)-1) {
		
					if($appliquer == 0){
						$lesHeures.= '<span style="font-weight: bold; color: orange; text-decoration:underline;">'.$heure[$i].'</span> ';
					} else {
						$lesHeures.= $heure[$i];
					}
		
				} else {
		
					if($appliquer == 0){
						$lesHeures.= '<span style="font-weight: bold; color: orange; text-decoration:underline;">'.$heure[$i].'</span>  -  ';
					} else {
						$lesHeures.= $heure[$i].'  -  ';
					}
		
				}
					
			}
		}
	
		return $lesHeures;
	}
	
	public function getHeuresAVenir($id_sh, $date){
		$heure = $this->getSoinHospitalisationTable()->getHeuresPourUneDate($id_sh, $date);
		$lesHeures ="";
		for ($i = 0; $i<count($heure); $i++){
			if($i == count($heure)-1) {
				$lesHeures.= '<span style="color: #ccc;">'.$heure[$i].'</span>';
			} else {
				$lesHeures.= '<span style="color: #ccc;">'.$heure[$i].' - </span>';
			}
		}
	
		return $lesHeures;
	}
	
	public function vueSoinAppliquerAction() {

		$this->getDateHelper();
		$id_sh = $this->params()->fromPost('id_sh', 0);
		$soinHosp = $this->getSoinHospitalisationTable()->getSoinhospitalisationWithId_sh($id_sh);
			
		$today = new \DateTime();
		$aujourdhui = $today->format('Y-m-d');
		$hier = date("Y-m-d", strtotime('-1 day'));
			
		$lesHeures = $this->getLesHeuresDuSoin($id_sh, $aujourdhui);
		$finDuSoin = date("Y-m-d", strtotime($soinHosp->date_debut_application.'+'.($soinHosp->duree-1).' day' ));
		
		$dateAvenir = null;
		if(!$lesHeures){
			$dateAvenir = $soinHosp->date_debut_application;
			$lesHeures  = $this->getHeuresAVenir($id_sh, $soinHosp->date_debut_application);
		}
			
		$html  ="<table style='width: 99%;'>";
			
		$html .="<tr style='width: 99%;'>
					   <td colspan='3' style='width: 99%;'>
					     <div id='titre_info_admis' style='margin-top: 0px;'>Prescription du soin : <i style='font-size: 15px;'>".$this->controlDate->convertDateTime($soinHosp->date_enregistrement)."</i></div><div id='barre_admis'></div>
					   </td>
					 </tr>";
			
		$html .="<tr style='width: 99%; '>";
		$html .="<td style='width: 36%; padding-top: 15px; padding-right: 15px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>M&eacute;dicament</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->medicament." </p></td>";
		$html .="<td style='width: 33%; padding-top: 15px; padding-right: 15px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Voie d'administration</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->voie_administration." </p></td>";
		$html .="<td style='width: 30%; padding-top: 15px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Dosage & Fr&eacute;quence</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->dosage." - ".$soinHosp->frequence."</p></td>";
		$html .="</tr>";
			
		$html .="<tr style='width: 99%;'>";
 		$html .="<td style='vertical-align:top; padding-right: 15px; padding-top: 10px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Date de d&eacute;but & Dur&eacute;e & Fin</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$this->controlDate->convertDate($soinHosp->date_debut_application)." - ".$soinHosp->duree." jr(s) - ".$this->controlDate->convertDate($finDuSoin)."</p></td>";
		$html .="<td colspan='2' style='width: 60%; vertical-align:top; padding-top: 10px;'>
				 <span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Heures recommand&eacute;es</span><br><p id='zoneChampInfo' class='lesHeuresRecAppDuSoin'  style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$lesHeures." </p>
				 </td>
				 </tr>";
			
		$html .="</table>";
			
		$html .="<table style='width: 99%;'>";
		$html .="<tr style='width: 95%;'>";
		$html .="<td style='width: 50%; padding-top: 10px; padding-right:25px;'><span style='text-decoration:underline; font-weight:bold; font-size:16px; color: #065d10; font-family: Times  New Roman;'>Motif</span><br><p id='label_Champ_NoteInformation' style='background:#f8faf8; font-size:17px; padding-left: 10px;'> ".$soinHosp->motif." </p></td>";
		$html .="<td style='width: 50%; padding-top: 10px;'><span style='text-decoration:underline; font-weight:bold; font-size:16px; color: #065d10; font-family: Times  New Roman;'>Note</span><br><p id='label_Champ_NoteInformation' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->note." </p></td>";
		$html .="<td style='width: 0%;'> </td>";
		$html .= "</tr>";
		
			
		$html .="<tr style='width: 99%;'>
				    <td colspan='2' style='width: 99%;'>
					  <div id='titre_info_admis'>
 				         Informations sur l'application du soin
 				         <span style='padding-left: 30px;'>
 				           <img class='transfert_gauche' style='height: 14px; width: 18px; cursor:pointer;' src='".$this->getPath()."/images_icons/transfert_gauche.png' >
 				           <img class='transfert_droite2' style='height: 14px; width: 18px;' src='".$this->getPath()."/images_icons/transfert_droite2.png' >
 				         </span>
		
 				         <span class='laDateDesSoins' style='font-size: 15px; padding-left: 15px;'> Aujourd'hui - ".$this->controlDate->convertDate($aujourdhui)."</span>
 				      </div>
 				 
 				      <div id='barre_admis'></div>
					</td>
			     </tr>";
			
		if($soinHosp){
			
			//if($soinHosp->appliquer == 0) {
		
				$listeTouteDate = $this->getSoinHospitalisationTable()->getToutesDateDuSoin($id_sh);
				$html .="<script> var j = 0; </script>";
				$i = 0;
				foreach ($listeTouteDate as $listeDate){
		
					$html .="<table id='".$listeDate['date']."' style='width: 99%; margin-top: 10px;'>";
		
					$listeHeure = $this->getSoinHospitalisationTable()->getToutesHeures($id_sh, $listeDate['date']);
		
					if($listeHeure){
						foreach ($listeHeure as $listeH) {
							if($listeH['applique'] == 1){
		
								//RECUPERATION DES INFORMATIONS DE L'INFIRMIER AYANT APPLIQUER LES DONNEES
								$infosInfirmier = $this->getSoinHospitalisationTable()->getInfosInfirmiers($listeH['id_personne_infirmier']);
								$PrenomInfirmier = " Prenom  ";
								$NomInfirmier = " Nom ";
								if($infosInfirmier){
									$PrenomInfirmier = $infosInfirmier['PRENOM'];
									$NomInfirmier = $infosInfirmier['NOM'];
								}
		
								$html .="<tr style='width: 99%;'>";
								$html .="<td style='width: 100%; vertical-align:top;'><span id='labelHeureLABEL' style='font-weight:bold; font-size:19px; padding-left: 5px; color: #065d10; font-family: Times  New Roman;'>".$listeH['heure']."</span>
								        <div class='infoUtilisateur".$listeH['id_heure']."' style='float: right; padding-top: 10px; padding-right: 10px; cursor:pointer'> <img src='../images_icons/info_infirmier.png' title='Infirmier: ".$PrenomInfirmier." ".$NomInfirmier." ".$this->controlDate->convertDateTime($listeH['date_application'])." ' /> </div>
								        <br><p id='zoneTexte' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$listeH['note']." </p>
								     </td>";
								$html .="</tr>";
		
								$html .="<script>
								         $('.infoUtilisateur".$listeH['id_heure']."').mouseenter(function(){
								           var tooltips = $( '.infoUtilisateur".$listeH['id_heure']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
								           tooltips.tooltip( 'open' );
       					                 });
	                                     $('.infoUtilisateur".$listeH['id_heure']."').mouseleave(function(){
	                                       var tooltips = $( '.infoUtilisateur".$listeH['id_heure']."' ).tooltip();
	                                       tooltips.tooltip( 'close' );
	                                     });
	                                     </script>";
							}
						}
					}
		
					$html .="<script>
 							  if(".$listeDate['date']." != ".$aujourdhui."){
                            	   $('#".$listeDate['date']."').toggle(false);
                        	  } else {
                            	   j = ".$i."; //la position de la date d aujourdhui dans le tableau
 				                }
		
 							 </script>";
					$html .="</table>";
					$i++;
				}
			//}
		
			$html .="<script> var tableau = ['']; var tableauDate = ['']; var indice=0; var position = j; encours = 1; var tableauHeures = ['']; </script>";
			//LISTE DES DATE EN TABLEAU JS
			$listeTouteDate = $this->getSoinHospitalisationTable()->getToutesDateDuSoinPourUneDate($id_sh, $aujourdhui);
			$lastDate = null;
			foreach ($listeTouteDate as $listeDate){
				$html .="<script> tableau[indice] = '".$listeDate['date']."'; </script>";
				$html .="<script> tableauDate[indice] = '".$this->controlDate->convertDate($listeDate['date'])."'; </script>";
				$html .="<script> tableauHeures[indice++] = '".$this->getLesHeuresDuSoin($id_sh, $listeDate['date'])."'; </script>";
				$lastDate = $listeDate['date'];
			}
				
			$html .="<script>
 					 if( j == 0 ){
 					     j = position = tableau.length-1;
 					     $('#'+tableau[j]).toggle(true);
 					     $('.laDateDesSoins').text(tableauDate[j]);
 					     encours = 0; // les date pour l appmication du soin sont depassees
 					     if(tableau[position] == '".$hier."') {
                   		    infoPlus = 'Hier - '+tableauDate[position];
                       	    $('.laDateDesSoins').text(infoPlus);
 		                 }else if(tableau[position] == '".$aujourdhui."') {
 					     	infoPlus = 'Aujourd\'hui - '+tableauDate[position];
                       	    $('.laDateDesSoins').text(infoPlus);
 					     }
 		                 		
 					     $('.lesHeuresRecAppDuSoin').html(tableauHeures[j]);
 		                 //Si la date est superieur a la date actuelle alors
 		                 if('".$dateAvenir."' > '".$aujourdhui."'){
 		                  	$('.lesHeuresRecAppDuSoin').html('".$lesHeures."');
 		                 }
 		             }
		
 					 var infoPlus;
 					 function gauche(){
                      $('.transfert_gauche').click(function(){ if(position > j) { position = j;}
 					    if(position > 0) {
		
 					      //Active licone suivant
 					      $('.transfert_droite2').replaceWith(
			                    '<img class=\'transfert_droite\' style=\'height: 14px; width: 18px; cursor:pointer;\' src=\'".$this->getPath()."/images_icons/transfert_droite.png\' >'
			                  );
 					      droite();
 					      $('#'+tableau[position]).fadeOut(function(){
			                    if(position > 0) { $('#'+tableau[position]).toggle(false); }
 					            $('#'+tableau[--position]).fadeIn();
 					            $('.lesHeuresRecAppDuSoin').html(tableauHeures[position]);
			          
			                    if(encours == 1){
			                      if(position == j){ infoPlus = 'Aujourd\'hui - '+tableauDate[position]; }
 					                else if(position == j-1) { infoPlus = 'Hier - '+tableauDate[position];}
 					                     else { infoPlus = tableauDate[position]; }
 					               $('.laDateDesSoins').text(infoPlus);
			                    }
			                    else if(encours == 0){
 		                       		      infoPlus = tableauDate[position];
			                       	      $('.laDateDesSoins').text(infoPlus);
 		                             }
 		                  });
		
			              if(position-1 == 0){
			                $('.transfert_gauche').replaceWith(
			                   '<img class=\'transfert_gauche2\' style=\'height: 14px; width: 18px;\' src=\'".$this->getPath()."/images_icons/transfert_gauche2.png\' >'
			                );
			              }
		
 					    }else {
			                   $('.transfert_gauche').replaceWith(
			                    '<img class=\'transfert_gauche2\' style=\'height: 14px; width: 18px;\' src=\'".$this->getPath()."/images_icons/transfert_gauche2.png\' >'
			                  );
			                   return false;
 		                 }
	                    stopPropagation();
		               });
			           return false;
	                 }
			          
			         function droite(){
	                   $('.transfert_droite').click(function(){ if(position < 0) { position = 0;}
	                    if(position <j) {
			          
			               //Active l icone precedent
			               $('.transfert_gauche2').replaceWith(
			                    '<img class=\'transfert_gauche\' style=\'height: 14px; width: 18px; cursor:pointer;\' src=\'".$this->getPath()."/images_icons/transfert_gauche.png\' >'
			                  );
			               gauche();
		
	                      $('#'+tableau[position]).fadeOut(function(){
			                    if(position < j ) { $('#'+tableau[position]).toggle(false); }
	                    		$('#'+tableau[++position]).fadeIn();
 					            $('.lesHeuresRecAppDuSoin').html(tableauHeures[position]);
			          
			                    if(encours == 1){
			                       if(position == j){ infoPlus = 'Aujourd\'hui - '+tableauDate[position]; }
                                     else if(position == j-1) { infoPlus = 'Hier - '+tableauDate[position];}
 					                      else { infoPlus = tableauDate[position]; }
 					               $('.laDateDesSoins').text(infoPlus);
			                    }
			         
			                    //Une fois les dates d application du soin sont depassees
			                    if(encours == 0){
			                       if(tableau[position] == '".$hier."') {
 		                       		  infoPlus = 'Hier - '+tableauDate[position];
			                       	  $('.laDateDesSoins').text(infoPlus);
 		                           } else{
			                       		infoPlus = tableauDate[position];
			                       	    $('.laDateDesSoins').text(infoPlus);
 		                             }
			                    }
 		                  });
			          
			              if(position+1 == j){
			                 $('.transfert_droite').replaceWith(
			                    '<img class=\'transfert_droite2\' style=\'height: 14px; width: 18px;\' src=\'".$this->getPath()."/images_icons/transfert_droite2.png\' >'
			                  );
			               }
 					    }else {
			                   $('.transfert_droite').replaceWith(
			                    '<img class=\'transfert_droite2\' style=\'height: 14px; width: 18px;\' src=\'".$this->getPath()."/images_icons/transfert_droite2.png\' >'
			                  );
			                   return false;
 		                 }
			          
	                    stopPropagation();
		               });
			           return false;
			          
			         }
			          
			         gauche();
			         droite();
 					 </script>";
		
		}
			
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
		
		
	}
	
	public function libererPatientAction() {
		$user = $this->layout()->user;
		$IdService = $user['IdService'];
		$IdMedecin = $user['id_personne'];
		$id_demande_hospi = $this->params()->fromPost('id_demande_hospi', 0);
		$resumer_medical = $this->params()->fromPost('resumer_medical', 0);
		$motif_sorti = $this->params()->fromPost('motif_sorti', 0);
		//POUR LE TRANSFERT DU PATIENT HOSPITALISE
		$id_cons = $this->params()->fromPost('id_cons', 0);
		$temoin_transfert = (int)$this->params()->fromPost('temoin_transfert', 0); //C'est la valeur du service car le patient est transferer dans un service -- 1282
		
		//POUR L'IMPRESSION DE L'ORDONNANCE LORS DE LA LIBERATION DU PATIENT
		$temoin_ordonnance = (int)$this->params()->fromPost('temoin_ordonnance', 0);
		
		
		$this->getHospitalisationTable()->libererPatient($id_demande_hospi, $resumer_medical, $motif_sorti, $IdMedecin);
		
		/**
		 * LIBERATION DU LIT
		 */
		
		/** La libération du lit ici maintenant effectué par le major du service hospitalisation
		 ** La libération du lit ici maintenant effectué par le major du service hospitalisation
		
		$ligne_hosp = $this->getHospitalisationTable()->getHospitalisationWithCodedh($id_demande_hospi);
		if($ligne_hosp){
			$id_hosp = $ligne_hosp->id_hosp;
			$ligne_lit_hosp = $this->getHospitalisationlitTable()->getHospitalisationlit($id_hosp);
			if($ligne_lit_hosp){
				$id_materiel = $ligne_lit_hosp->id_materiel;
				$this->getLitTable()->libererLit($id_materiel);
			}
		}
		
		 **/
		
		
		//S'il s'agit d'un transfert notifier cela dans la table transferer_patient_service 
		if($temoin_transfert != 0){
			$info_transfert = array();
			$info_transfert['ID_CONS'] = $id_cons;
			$info_transfert['MOTIF_TRANSFERT'] = $motif_sorti;
			$info_transfert['ID_MEDECIN'] = $IdMedecin;
			$info_transfert['ID_SERVICE'] = $temoin_transfert; 
			$this->getTransfererPatientServiceTable()->insererTransfertPatientService($info_transfert);
		} 
	

		if($temoin_ordonnance == 1){
			//POUR LES ORDONNANCES
			//POUR LES ORDONNANCES
			//POUR LES ORDONNANCES
			$donnees = array('id_cons' => $id_cons, 'duree_traitement' => '');

			$tab = array();
			$j = 1;
			for($i = 1 ; $i < 10 ; $i++ ){
				if($this->params()->fromPost("medicament_0".$i)){
					$tab[$j++] = $this->getOrdonConsommableTable()->getMedicamentByName($this->params()->fromPost("medicament_0".$i))['ID_MATERIEL'];
					$tab[$j++] = $this->params()->fromPost("forme_".$i);
					$tab[$j++] = $this->params()->fromPost("nb_medicament_".$i);
					$tab[$j++] = $this->params()->fromPost("quantite_".$i);
				}
			}
			
			/*Mettre a jour la duree du traitement de l'ordonnance*/
			$idOrdonnance = $this->getOrdonnanceTable()->updateOrdonnanceForHospi($tab, $donnees);
			
			/*Mettre a jour les medicaments*/
			$resultat = $this->getOrdonConsommableTable()->updateOrdonConsommable($tab, $idOrdonnance);
			
			/*si aucun médicament n'est ajouté ($resultat = false) on supprime l'ordonnance*/
			if($resultat == false){ $this->getOrdonnanceTable()->deleteOrdonnance($idOrdonnance);}
		}
		
		return $this->redirect()->toRoute('consultation', array('action' =>'en-cours'));
	}
	
	public function listeSoinsVisualisationHospAction(){
		$id_hosp = $this->params()->fromPost('id_hosp', 0);
		$html = $this->raffraichirListeSoinsPrescrit($id_hosp);
		$html .="<style> #info_liste{ margin-left:195px; width: 80%;} </style>";
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	/**
	 * ========================================================
	 * ========================================================
	 * *** ====================================================
	 * ========================================================
	 * ========================================================
	 * *** ====================================================
	 * ========================================================
	 * ========================================================
	 * *** ====================================================
	 * ========================================================
	 * ========================================================
	 * *** ====================================================
	 * ========================================================
	 * ========================================================
	 */
	
	/***** LECTEUR MP3 ---- LECTEUR MP3  *****/
	/***** LECTEUR MP3 ---- LECTEUR MP3  *****/
	/***** LECTEUR MP3 ---- LECTEUR MP3  *****/
	public function lecteurMp3Action($ListeDesSons){
		$html ='<script>
				 var tab = []; 
		        </script>';
		$i = 0;
		foreach ($ListeDesSons as $Liste) {
        $html .='<script>
        		 tab['.$i++.'] = {
	                     "title":"'. $Liste['titre'] .'<span class=\"supprimerSon'.$i.'\" >  </span>",
		                 "mp3":"/simens/public/js/plugins/jPlayer-2.9.2/examples/'. $Liste['nom'] .'",
		         };
		                 		
		         setTimeout(function() {
	                $(function () {
		              $(".supprimerSon'.$i.'").click(function () { return false; });
		              $(".supprimerSon'.$i.'").dblclick(function () { supprimerAudioMp3('.$i.'); return false; });
	                });
                 }, 1000);
        		 </script>';
		}
		
        $html .='<script> 
        		$(function(){
	              new jPlayerPlaylist({
		          jPlayer: "#jquery_jplayer_2",
		          cssSelectorAncestor: "#jp_container_2"
	            }, tab , {
		        swfPath: "/simens/public/js/plugins/jPlayer-2.9.2/dist/jplayer",
		        supplied: "mp3",
		        wmode: "window",
		        useStateClassSkin: true,
		        autoBlur: false,
		        smoothPlayBar: true,
		        keyEnabled: true,
		        remainingDuration: true,
		        toggleDuration: true
	            });
                });
        		scriptAjoutMp3();
                </script>';
       
		$html .='
				<form id="my_form" method="post" action="/simens/public/consultation/ajouter-mp3" enctype="multipart/form-data">
                <div id="jquery_jplayer_2" class="jp-jplayer" style="margin-bottom: 30px;"></div>
                <div id="jp_container_2" class="jp-audio" role="application" aria-label="media player"  style="margin-bottom: 30px;">
	            <div class="jp-type-playlist">
		         <div class="jp-gui jp-interface">
			       <div class="jp-controls">
				      <button class="jp-previous" role="button" tabindex="0">previous</button>
				      <button class="jp-play" role="button" tabindex="0">play</button>
				      <button class="jp-next" role="button" tabindex="0">next</button>
				      <button class="jp-stop" role="button" tabindex="0">stop</button>
			       </div>
			   <div class="jp-progress">
				<div class="jp-seek-bar">
					<div class="jp-play-bar"></div>
				</div>
			   </div>
			   <div class="jp-volume-controls">
				<button class="jp-mute" role="button" tabindex="0">mute</button>
				<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
				<div class="jp-volume-bar">
					<div class="jp-volume-bar-value"></div>
				</div>
			   </div>
			   <div class="jp-time-holder">
				<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
				<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
			   </div>
			   <div class="jp-toggles">
				<button class="jp-repeat" role="button" tabindex="0">repeat</button>
				<!-- button class="jp-shuffle" role="button" tabindex="0">shuffle</button-->
				<div class="jp-ajouter" id="ajouter">
				  <input type="file" name="fichier" id="fichier">
				</div>
			   </div>
		       </div>
		       <div class="jp-playlist">
			      <ul>
				     <li>&nbsp;</li>
			      </ul>
		       </div>
	           </div>
               </div>
               </form>';
		return $html;
	}
	
	public function lecteurMp3InstrumentalAction($ListeDesSons){
		$html ='<script>
				 var tab = [];
		        </script>';
		$i = 0;
		foreach ($ListeDesSons as $Liste) {
			$html .='<script>
        		 tab['.$i++.'] = {
	                     "title":"'. $Liste['titre'] .'<span class=\"supprimerSonIns'.$i.'\" >  </span>",
		                 "mp3":"/simens/public/js/plugins/jPlayer-2.9.2/examples/'. $Liste['nom'] .'",
		         };
		         
		         setTimeout(function() {
	                $(function () {
		              $(".supprimerSonIns'.$i.'").click(function () { return false; });
		              $(".supprimerSonIns'.$i.'").dblclick(function () { supprimerAudioMp3('.$i.'); return false; });
	                });
                 }, 1000);
        		 </script>';
		}
	
		$html .='<script>
        		$(function(){
	              new jPlayerPlaylist({
		          jPlayer: "#jquery_jplayer",
		          cssSelectorAncestor: "#jp_container"
	            }, tab , {
		        swfPath: "/simens/public/js/plugins/jPlayer-2.9.2/dist/jplayer",
		        supplied: "mp3",
		        wmode: "window",
		        useStateClassSkin: true,
		        autoBlur: false,
		        smoothPlayBar: true,
		        keyEnabled: true,
		        remainingDuration: true,
		        toggleDuration: true
	            });
                });
        		scriptAjoutMp3_Instrumental();
                </script>';
		 
		$html .='
				<form id="my_form2" method="post" action="/simens/public/consultation/ajouter-mp3" enctype="multipart/form-data">
                <div id="jquery_jplayer" class="jp-jplayer" style="margin-bottom: 30px;"></div>
                <div id="jp_container" class="jp-audio" role="application" aria-label="media player"  style="margin-bottom: 30px;">
	            <div class="jp-type-playlist">
		         <div class="jp-gui jp-interface">
			       <div class="jp-controls">
				      <button class="jp-previous" role="button" tabindex="0">previous</button>
				      <button class="jp-play" role="button" tabindex="0">play</button>
				      <button class="jp-next" role="button" tabindex="0">next</button>
				      <button class="jp-stop" role="button" tabindex="0">stop</button>
			       </div>
			   <div class="jp-progress">
				<div class="jp-seek-bar">
					<div class="jp-play-bar"></div>
				</div>
			   </div>
			   <div class="jp-volume-controls">
				<button class="jp-mute" role="button" tabindex="0">mute</button>
				<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
				<div class="jp-volume-bar">
					<div class="jp-volume-bar-value"></div>
				</div>
			   </div>
			   <div class="jp-time-holder">
				<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
				<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
			   </div>
			   <div class="jp-toggles">
				<button class="jp-repeat" role="button" tabindex="0">repeat</button>
				<!-- button class="jp-shuffle" role="button" tabindex="0">shuffle</button-->
				<div class="jp-ajouter" id="ajouter">
				  <input type="file" name="fichier" id="fichier">
				</div>
			   </div>
		       </div>
		       <div class="jp-playlist">
			      <ul>
				     <li>&nbsp;</li>
			      </ul>
		       </div>
	           </div>
               </div>
               </form>';
		return $html;
	}
	
	public function afficherMp3Action(){
		$id_cons = $this->params()->fromPost('id_cons', 0);
		$type = (int)$this->params()->fromPost('type', 0);
		
		$ListeDesSons = $this->getConsultationTable ()->getMp3($id_cons, $type);
		
		$html = null;
		if($type == 1){
			$html = $this->lecteurMp3Action($ListeDesSons);
		}else {
			$html = $this->lecteurMp3InstrumentalAction($ListeDesSons);
		}
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	public function supprimerMp3Action(){
		$id = $this->params()->fromPost('id', 0);
		$id_cons = $this->params()->fromPost('id_cons', 0);
		$type = $this->params()->fromPost('type', 0);
		
		$this->getConsultationTable ()->supprimerMp3($id, $id_cons, $type);

		$ListeDesSons = $this->getConsultationTable ()->getMp3($id_cons, $type);
		
		$html = null;
		if($type == 1){
			$html = $this->lecteurMp3Action($ListeDesSons);
		}else {
			$html = $this->lecteurMp3InstrumentalAction($ListeDesSons);
		}
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	public function ajouterMp3Action(){
		
		$type = $_FILES['fichier']['type'];
		$nom_file = $_FILES['fichier']['name'];
		$tmp = $_FILES['fichier']['tmp_name'];
		
		if($type == 'audio/mp3'){ 
			$result = move_uploaded_file($tmp, 'C:\wamp\www\simens\public\js\plugins\jPlayer-2.9.2\examples\\'.$nom_file);
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($nom_file) );
	}
	
	public function insererBdMp3Action(){
		$id_cons = $this->params()->fromPost('id_cons', 0);
		$nom_file = $this->params()->fromPost('nom_file', 0);
		$type = $this->params()->fromPost('type', 0);
		
		$this->getConsultationTable ()->insererMp3($nom_file, $nom_file, $id_cons, $type);
		$ListeDesSons = $this->getConsultationTable ()->getMp3($id_cons, $type);
		
		$html = null;
		if($type == 1){
			$html = $this->lecteurMp3Action($ListeDesSons);
		}else {
			$html = $this->lecteurMp3InstrumentalAction($ListeDesSons);
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	
	/***** LECTEUR VIDEO ---- LECTEUR VIDEO  *****/
	/***** LECTEUR VIDEO ---- LECTEUR VIDEO  *****/
	/***** LECTEUR VIDEO ---- LECTEUR VIDEO  *****/
	public function lecteurVideoAction(){

		$html ='<script>
				 var tab = [];
				
				 $(document).ready(function(){

	               new jPlayerPlaylist({
		             jPlayer: "#jquery_jplayer_1",
		             cssSelectorAncestor: "#jp_container_1"
	               }, [                                  
		           {
				     title:"Head Brain Autopsy 3D",
				     webmv: "/simens/public/js/plugins/jPlayer-2.9.2/examples/videos/Head Brain Autopsy 3D CT imaging.webm",
         		   },
		           {
		     	     title:"scanner de la Main",
			         webmv: "/simens/public/js/plugins/jPlayer-2.9.2/examples/videos/scanner de la Main  VRT couleur.webm",
		           },
		           {
			         title:"Big Buck Bunny Trailer",
			         m4v: "/simens/public/js/plugins/jPlayer-2.9.2/examples/videos/Big_Buck_Bunny_Trailer.m4v",
				     poster:"/simens/public/js/plugins/jPlayer-2.9.2/examples/videos/Big_Buck_Bunny_Trailer_480x270.png"
     		       }
		
	               ], {
		               swfPath: "../../dist/jplayer",
		               supplied: "webmv, ogv, m4v",
		               useStateClassSkin: true,
		               autoBlur: false,
		               smoothPlayBar: true,
		               keyEnabled: true
	               });

                 });
		        </script>';
		 
		$html .='
				
				<div id="jp_container_1" class="jp-video jp-video-270p" role="application" aria-label="media player" style="margin: auto;">
	<div class="jp-type-playlist">
		<div id="jquery_jplayer_1" class="jp-jplayer"></div>
		<div class="jp-gui">
			<div class="jp-video-play">
				<button class="jp-video-play-icon" role="button" tabindex="0">play</button>
			</div>
			<div class="jp-interface">
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
				<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
				<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
				<div class="jp-controls-holder">
					<div class="jp-controls">
						<button class="jp-previous" role="button" tabindex="0">previous</button>
						<button class="jp-play" role="button" tabindex="0">play</button>
						<button class="jp-next" role="button" tabindex="0">next</button>
						<button class="jp-stop" role="button" tabindex="0">stop</button>
					</div>
					<div class="jp-volume-controls">
						<button class="jp-mute" role="button" tabindex="0">mute</button>
						<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
						<div class="jp-volume-bar">
							<div class="jp-volume-bar-value"></div>
						</div>
					</div>
					<div class="jp-toggles">
						<button class="jp-full-screen" role="button" tabindex="0">full screen</button>
					</div>
				</div>
				<div class="jp-details">
					<div class="jp-title" aria-label="title">&nbsp;</div>
				</div>
			</div>
		</div>
		<div class="jp-playlist">
			<ul>
				<li>&nbsp;</li>
			</ul>
		</div>
	</div>
</div>
				
				';
		return $html;
	}
	
	public function afficherVideoAction(){
		//$id_cons = $this->params()->fromPost('id_cons', 0);
		//$type = (int)$this->params()->fromPost('type', 0);
	
		//$ListeDesSons = $this->getConsultationTable ()->getMp3($id_cons, $type);
	
		$html = $this->lecteurVideoAction();
	
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	//ENREGISTREMENT DES EXAMENS DU JOUR
	//ENREGISTREMENT DES EXAMENS DU JOUR
	//ENREGISTREMENT DES EXAMENS DU JOUR
	//ENREGISTREMENT DES EXAMENS DU JOUR
	//ENREGISTREMENT DES EXAMENS DU JOUR
	//ENREGISTREMENT DES EXAMENS DU JOUR
	//ENREGISTREMENT DES EXAMENS DU JOUR
	public function listeExamenDuJourAction(){
		$user = $this->layout()->user;
		$IdService = $user['IdService'];
		$id_hosp = $this->params()->fromPost('id_hosp', 0);
		
		$this->getDateHelper();
		$listeExamens = $this->getConsultationTable()->getExamenDuJour($id_hosp);
		
		$html ="";
		
		$html .='<table id="ListingExamens" class="table table-bordered tab_list_mini" >
		           <thead>
		             <tr id="nomMaj" style="height: 40px; width:100%;">
		                <th style="width: 20%; cursor: pointer;">D<minus>ate</minus></th>
		                <th style="width: 20%; cursor: pointer;">H<minus>eure</minus></th>
		                <th style="width: 50%; cursor: pointer;">P<minus>r&eacute;nom & nom m&eacute;decin</minus></th>
		                <th style="width: 10%;" >O<minus>ptions</minus></th>
		             </tr>
		           </thead>
		
		         <tbody id="donnees" class="liste_patient">';
		
		foreach ($listeExamens as $liste) {
			$html .='<tr id="ExamenDuJourLigne'.$liste['ID_EXAMEN_JOUR'].'"> 
					  <td> '.$this->controlDate->convertDate($liste['DATEONLY']).' </td>
					  <td> '.$this->controlDate->getTime($liste['DATE']).' </td>
					  <td> '.$liste['PrenomMedecin'].' '.$liste['NomMedecin'].' </td>
					  <td> 
					  	 <a href="javascript:visualiserExamenDuJour('.$liste['ID_EXAMEN_JOUR'].')"><img style="margin-left: 5%;" src="../images_icons/voird.png" title="visualiser"></a>
					     <a href="javascript:supprimerExamenDuJour('.$liste['ID_EXAMEN_JOUR'].')"><img style="margin-left: 20%;" src="../images_icons/sup.png" title="supprimer"></a>
			  		  </td>
					</tr>';  
			
		}
		
		$html .='</tbody>
				 </table>';
		
		$html .="<script> 
				  initialisationScriptDataTable();
				</script>";
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	public function enregistrerExamenDuJourAction(){
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		$idMedecin = $user['id_personne'];
		
		$date = new \DateTime();
		$codeExamen = 'e-j-'.$date->format('dmy-His');
		
		$formData = $this->getRequest ()->getPost ();
		
		$this->getConsultationTable ()->addConsultationExamenDuJour ($codeExamen, $formData, $IdDuService, $idMedecin);
		$this->getConsultationTable ()->addExamenDuJour($codeExamen, $formData->id_hosp);
		$this->getMotifAdmissionTable ()->addMotifAdmissionPourExamenJour ( $formData, $codeExamen );
		
		//Recuperer les donnees sur les bandelettes urinaires
		//Recuperer les donnees sur les bandelettes urinaires
		$bandelettes = array(
				'id_cons' => $codeExamen,
				'albumine' => $formData->albumine,
				'sucre' => $formData->sucre,
				'corpscetonique' => $formData->corpscetonique,
				'croixalbumine' => $formData->croixalbumine,
				'croixsucre' => $formData->croixsucre,
				'croixcorpscetonique' => $formData->croixcorpscetonique,
		);
		$this->getConsultationTable ()->addBandelette($bandelettes);
		
		//POUR LES EXAMENS PHYSIQUES
		//POUR LES EXAMENS PHYSIQUES
		$info_donnees_examen_physique = array(
				'id_cons' => $codeExamen,
				'donnee1' => $formData->examen_donnee1,
				'donnee2' => $formData->examen_donnee2,
				'donnee3' => $formData->examen_donnee3,
				'donnee4' => $formData->examen_donnee4,
				'donnee5' => $formData->examen_donnee5
		);
		$this->getDonneesExamensPhysiquesTable()->addExamenPhysiqueExamenJour($info_donnees_examen_physique);
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($formData->croixalbumine) );
	}
	
	public function supprimerExamenJourAction(){
		$id_examen_jour = $this->params()->fromPost('id_examen_jour', 0);
		$id_hosp = $this->getConsultationTable()->supprimerExamenDuJour($id_examen_jour);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($id_hosp) );
	}
	
	public function vueExamenJourAction(){
		$this->layout()->setTemplate('layout/consultation');
		$id_examen_jour = $this->params()->fromPost('id_examen_jour', 0);
		
		$ExamenJour = $this->getConsultationTable()->getExamenDuJourParIdExamenJour($id_examen_jour);
		$id_patient = $ExamenJour['ID_PATIENT'];
		$id_hosp = $ExamenJour['ID_HOSP'];
		$id_cons = $ExamenJour['ID_CONS'];
		
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		
		//POUR LES CONSTANTES
		//POUR LES CONSTANTES
		$ConstantesExamen = $this->getConsultationTable()->getConsultationExamenJour($id_cons);
		$Constantes = array(
				'poids' => $ConstantesExamen['POIDS'],
				'taille' => $ConstantesExamen['TAILLE'],
				'temperature' => $ConstantesExamen['TEMPERATURE'],
				'pressionarterielle' => $ConstantesExamen['PRESSION_ARTERIELLE'],
				'pouls' => $ConstantesExamen['POULS'],
				'frequence_respiratoire' => $ConstantesExamen['FREQUENCE_RESPIRATOIRE'],
				'glycemie_capillaire' => $ConstantesExamen['GLYCEMIE_CAPILLAIRE'],
		);
		
		//POUR LES MOTIFS D'ADMISSION
		//POUR LES MOTIFS D'ADMISSION
		$motif_admission = $this->getMotifAdmissionTable ()->getMotifAdmission ( $id_cons );
		$nbMotif = $this->getMotifAdmissionTable ()->nbMotifs ( $id_cons );
		
		//POUR LES MOTIFS D'ADMISSION
		$DonneesMotifs = array();
		$k = 1;
		foreach ( $motif_admission as $Motifs ) {
			$DonneesMotifs ['motif_admission' . $k] = $Motifs ['Libelle_motif'];
			$k ++;
		}
		
		//Pour recuper les bandelettes
		//Pour recuper les bandelettes
		$bandelettes = $this->getConsultationTable ()->getBandelette($id_cons);
		
		//POUR LES EXAMEN PHYSIQUES
		//POUR LES EXAMEN PHYSIQUES
		$examen_physique = $this->getDonneesExamensPhysiquesTable()->getExamensPhysiques($id_cons);
		$DonneesExamenPhysique = array();
		$kPhysique = 1;
		foreach ($examen_physique as $Examen) {
			$DonneesExamenPhysique ['examen_donnee'.$kPhysique] = $Examen['libelle_examen'];
			$kPhysique++;
		}
		
		
		//var_dump($bandelettes); exit();

		$formSoin = new SoinForm();
			
		$hopital = $this->getTransfererPatientServiceTable ()->fetchHopital ();
		$formSoin->get ( 'hopital_accueil' )->setValueOptions ( $hopital );
		//RECUPERATION DE L'HOPITAL DU SERVICE
		$transfertPatientHopital = $this->getTransfererPatientServiceTable ()->getHopitalPatientTransfert($IdDuService);
		$idHopital = $transfertPatientHopital['ID_HOPITAL'];
		//RECUPERATION DE LA LISTE DES SERVICES DE L'HOPITAL OU SE TROUVE LE SERVICE OU LE MEDECIN TRAVAILLE
		$serviceHopital = $this->getTransfererPatientServiceTable ()->fetchServiceWithHopitalNotServiceActual($idHopital, $IdDuService);
		//LISTE DES SERVICES DE L'HOPITAL
		$formSoin->get ( 'service_accueil' )->setValueOptions ($serviceHopital);
		
		$date = new \DateTime();
		$aujourdhui = $date->format('dmy');
		$data = array (
				'hopital_accueil' => $idHopital,
				'date_cons' => $aujourdhui,
		);
		$formSoin->populateValues(array_merge($data, $Constantes, $DonneesMotifs, $bandelettes, $DonneesExamenPhysique));
		
		$listeMedicament = $this->getConsultationTable()->listeDeTousLesMedicaments();
		
		return array(
				'form' => $formSoin,
				'liste_med' => $listeMedicament,
				'id_patient' => $id_patient,
				'id_hosp' => $id_hosp,
				'nbMotifs' => $nbMotif,
				'temoin' => $bandelettes['temoin'],
				'nbDonneesExamenPhysique' => $kPhysique,
		);
	}
	
}
