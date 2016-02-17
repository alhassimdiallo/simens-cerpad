<?php 
namespace Hospitalisation\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Facturation\View\Helper\DateHelper;
use Hospitalisation\Form\HospitaliserForm;
use Hospitalisation\Form\SoinForm;
use Hospitalisation;
use Zend\Form\View\Helper\FormRow;
use Zend\Form\View\Helper\FormText;
use Zend\Form\View\Helper\FormSelect;
use Zend\Form\View\Helper\FormTextarea;
use Hospitalisation\Form\SoinmodificationForm;
use Hospitalisation\Form\LibererPatientForm;
use Zend\Form\View\Helper\FormHidden;
use Hospitalisation\Form\AppliquerSoinForm;
use Hospitalisation\Form\AppliquerExamenForm;
use Zend\Server\Method\Prototype;
use Hospitalisation\Model\ResultatExamen;
use Hospitalisation\Form\VpaForm;
use Zend\Form\Element\Radio;
use Zend\Form\View\Helper\FormRadio;
use Hospitalisation\Form\LibererPatientMajorForm;
use Hospitalisation\Form\HospitaliserPourGestionLitsForm;

class HospitalisationController extends AbstractActionController {
	
	protected $dateHelper;
	protected $path;
	
	protected $demandeHospitalisationTable;
	protected $patientTable;
	protected $batimentTable;
	protected $hospitalisationTable;
	protected $hospitalisationlitTable;
	protected $litTable;
	protected $salleTable;
	protected $soinhospitalisationTable;
	protected $soinsTable;
	protected $demandeTable;
	protected $examenTable;
	Protected $resultatExamenTable;
	protected $resultatVpaTable;
	
	public function getDemandeHospitalisationTable() {
		if (! $this->demandeHospitalisationTable) {
			$sm = $this->getServiceLocator ();
			$this->demandeHospitalisationTable = $sm->get ( 'Hospitalisation\Model\DemandehospitalisationTable' );
		}
		return $this->demandeHospitalisationTable;
	}
	
	public function getPatientTable() {
		if (! $this->patientTable) {
			$sm = $this->getServiceLocator ();
			$this->patientTable = $sm->get ( 'Hospitalisation\Model\PatientTable' );
		}
		return $this->patientTable;
	}
	
	public function getBatimentTable() {
		if (! $this->batimentTable) {
			$sm = $this->getServiceLocator ();
			$this->batimentTable = $sm->get ( 'Hospitalisation\Model\BatimentTable' );
		}
		return $this->batimentTable;
	}
	
	public function getHospitalisationTable() {
		if (! $this->hospitalisationTable) {
			$sm = $this->getServiceLocator ();
			$this->hospitalisationTable = $sm->get ( 'Hospitalisation\Model\HospitalisationTable' );
		}
		return $this->hospitalisationTable;
	}
	
	public function getHospitalisationlitTable() {
		if (! $this->hospitalisationlitTable) {
			$sm = $this->getServiceLocator ();
			$this->hospitalisationlitTable = $sm->get ( 'Hospitalisation\Model\HospitalisationlitTable' );
		}
		return $this->hospitalisationlitTable;
	}
	
	public function getLitTable() {
		if (! $this->litTable) {
			$sm = $this->getServiceLocator ();
			$this->litTable = $sm->get ( 'Hospitalisation\Model\LitTable' );
		}
		return $this->litTable;
	}
	
	public function getSalleTable() {
		if (! $this->salleTable) {
			$sm = $this->getServiceLocator ();
			$this->salleTable = $sm->get ( 'Hospitalisation\Model\SalleTable' );
		}
		return $this->salleTable;
	}
	
	public function getSoinHospitalisationTable() {
		if (! $this->soinhospitalisationTable) {
			$sm = $this->getServiceLocator ();
			$this->soinhospitalisationTable = $sm->get ( 'Hospitalisation\Model\SoinhospitalisationTable' );
		}
		return $this->soinhospitalisationTable;
	}
	
	public function getSoinsTable() {
		if (! $this->soinsTable) {
			$sm = $this->getServiceLocator ();
			$this->soinsTable = $sm->get ( 'Hospitalisation\Model\SoinsTable' );
		}
		return $this->soinsTable;
	}
	
    public function getDemandeTable() {
		if (! $this->demandeTable) {
			$sm = $this->getServiceLocator ();
			$this->demandeTable = $sm->get ( 'Hospitalisation\Model\DemandeTable' );
		}
		return $this->demandeTable;
	}
	
	public function getExamenTable() {
		if (! $this->examenTable) {
			$sm = $this->getServiceLocator ();
			$this->examenTable = $sm->get ( 'Hospitalisation\Model\ExamenTable' );
		}
		return $this->examenTable;
	}
	
	public function getResultatExamenTable() {
		if (! $this->resultatExamenTable) {
			$sm = $this->getServiceLocator ();
			$this->resultatExamenTable = $sm->get ( 'Hospitalisation\Model\ResultatExamenTable' );
		}
		return $this->resultatExamenTable;
	}
	
	public function getResultatVpa() {
		if (! $this->resultatVpaTable) {
			$sm = $this->getServiceLocator ();
			$this->resultatVpaTable = $sm->get ( 'Hospitalisation\Model\ResultatVisitePreanesthesiqueTable' );
		}
		return $this->resultatVpaTable;
	}
	/*/*********************************************************************************************************************************
	 * ==============================================================================================================================
	 * ******************************************************************************************************************************
	 * ******************************************************************************************************************************
	 * ==============================================================================================================================
	 */
	
	Public function getDateHelper(){
		$this->dateHelper = new DateHelper();
	}
	
	public function getPath(){
		$this->path = $this->getServiceLocator()->get('Request')->getBasePath();
		return $this->path;
	}
	
	public function  indexAction(){
		$this->layout()->setTemplate('layout/Hospitalisation');
		$view = new ViewModel(array(
				'message' => '<text style="margin-left: 15%; font-size: 40px; font-weight: bold; color: green; font-family: Times New Roman;"> Bienvenue au module hospitalisation </text> </br></br> 
				              <text style="margin-left: 30%; font-size: 25px; color: green; font-family: Times New Roman;"> Utilisez le menu a gauche</text>',
		));
		$view->setTemplate('hospitalisation/hospitalisation/index');
		return $view;
	}
	
	public function sallesAction()
	{
		$id_batiment = (int)$this->params()->fromPost ('id_batiment');
	
		if ($this->getRequest()->isPost()){
			$liste_select = "";
			
			foreach($this->getBatimentTable()->listeSalleDisponible($id_batiment) as $listeSalles){
				$liste_select.= "<option value=".$listeSalles['IdSalle'].">".$listeSalles['NumeroSalle']."</option>";
			}
			
			$liste_select.="<script> $('#salle').val(''); $('#lit').html('');</script>";
		}
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $liste_select));
	}
	
	public function litsAction()
	{
		$id_salle = (int)$this->params()->fromPost ('id_salle');
	
		if ($this->getRequest()->isPost()){
			$liste_select = "";
				
			foreach($this->getBatimentTable()->listeLitDisponible($id_salle) as $listeLits){
				$liste_select.= "<option value=".$listeLits['IdLit'].">".$listeLits['NomLit']."</option>";
			}
			
			$liste_select.="<script> $('#lit').val('');</script>";
		}
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $liste_select));
	}
	
	public function listePatientAjaxAction() {
		$output = $this->getDemandeHospitalisationTable()->getListeDemandeHospitalisation();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeAction() {
		$this->layout()->setTemplate('layout/Hospitalisation');
		
		$output = $this->getDemandeHospitalisationTable()->getListeDemandeHospitalisation();
		
		
		$formHospitalisation = new HospitaliserForm();
		$formHospitalisation->get('division')->setvalueOptions($this->getBatimentTable()->listeBatiments());
		
		if($this->getRequest()->isPost()) {
			$id_lit = $this->params()->fromPost('lit',0);
			$code_demande = $this->params()->fromPost('code_demande',0);
			
			$id_hosp = $this->getHospitalisationTable()->saveHospitalisation($code_demande);
			$this->getHospitalisationlitTable()->saveHospitalisationlit($id_hosp, $id_lit);
			
			$this->getDemandeHospitalisationTable()->validerDemandeHospitalisation($code_demande);
			
			$this->getLitTable()->updateLit($id_lit);
			
			return $this->redirect()->toRoute('hospitalisation' , array('action' => 'liste'));
		}
		
		return array(
			'form' => $formHospitalisation
		);
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
		
		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
		
		$html  = "<div style='width:100%;'>";
			
		$html .= "<div style='width: 18%; height: 180px; float:left;'>";
		$html .= "<div id='photo' style='float:left; margin-left:40px; margin-top:10px; margin-right:30px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "' ></div>";
		$html .= "</div>";
			
		$html .= "<div style='width: 65%; height: 180px; float:left;'>";
		$html .= "<table style='margin-top:10px; float:left; width: 100%;'>";
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 23%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unPatient['NOM'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Lieu de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['LIEU_NAISSANCE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; actuelle:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['NATIONALITE_ACTUELLE'] . "</p></td>";
		$html .= "<td style='width: 27%; height: 50px;'></td>";
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 23%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['PRENOM'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['TELEPHONE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; d'origine:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['NATIONALITE_ORIGINE']. "</p></td>";
		$html .= "<td style='width: 27%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Email:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unPatient['EMAIL'] . "</p></td>";
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 23%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['ADRESSE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Profession:</a><br><p style=' font-weight:bold; font-size:17px;'>" .  $unPatient['PROFESSION'] . "</p></td>";
		$html .= "<td style='width: 27%; height: 50px;'></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .="</div>";
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		$html .= "<div id='titre_info_deces'>D&eacute;tails des infos sur la demande </div>
		          <div id='barre'></div>";
		
		$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
		$html .= "<tr style='width: 80%;'>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Consultation:</a><br><p style='font-weight:bold; font-size:17px;'>" . $id_cons . "</p></td>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDateTime($demande['date_demande_hospi']) . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date fin pr&eacute;vue:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDate($demande['date_fin_prevue_hospi']) . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>M&eacute;decin demandeur:</a><br><p style=' font-weight:bold; font-size:17px;'>" .$demande['PrenomMedecin'].' '.$demande['NomMedecin']. "</p></td>";
		$html .= "</tr>";
		$html .= "</table>";
		
		$html .="<table style='margin-top:0px; margin-left:195px; width: 70%;'>";
		$html .="<tr style='width: 70%'>";
		$html .="<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 20%; '><a style='text-decoration:underline; font-size:13px;'>Motif de la demande:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'>". $demande['motif_demande_hospi'] ."</p></td>";
		$html .="<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 20%; '><a style='text-decoration:underline; font-size:13px;'>Note:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'> </p></td>";
		$html .="</tr>";
		$html .="</table>";
		
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
			
			$html .= "<div id='titre_info_deces'>Infos sur l'hospitalisation </div>
		          <div id='barre'></div>";
			$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
			$html .= "<tr style='width: 80%;'>";
 			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date d&eacute;but:</a><br><p style='font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDateTime($hospitalisation->date_debut) . "</p></td>";
 			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Batiment:</a><br><p style=' font-weight:bold; font-size:17px;'>".$batiment->intitule."</p></td>";
 			$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Salle:</a><br><p style=' font-weight:bold; font-size:17px;'>".$salle->numero_salle."</p></td>";
 			$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Lit:</a><br><p style=' font-weight:bold; font-size:17px;'>".$lit->intitule."</p></td>";
			$html .= "</tr>";
			$html .= "</table>";
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
			
			$html .= "<div id='titre_info_deces'>Info lib&eacute;ration du patient </div>
		              <div id='barre'></div>";
			
			$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
			$formLiberation = new LibererPatientForm();
			$data = array('id_demande_hospi' => $id_demande_hospi);
			$formLiberation->populateValues($data);
			
			$formRow = new FormRow();
			$formTextArea = new FormTextarea();
			$formHidden = new FormHidden();
			
			$html .="<form  method='post' action='".$chemin."/hospitalisation/liberer-patient'>";
			$html .=$formHidden($formLiberation->get('id_demande_hospi'));
			$html .="<div style='width: 80%; margin-left: 195px;'>";
			$html .="<table id='form_patient' style='width: 100%; '>
					 <tr class='comment-form-patient' style='width: 100%'>
					   <td id='note_soin'  style='width: 45%; '>". $formRow($formLiberation->get('resumer_medical')).$formTextArea($formLiberation->get('resumer_medical'))."</td>
					   <td id='note_soin'  style='width: 45%; '>". $formRow($formLiberation->get('motif_sorti')).$formTextArea($formLiberation->get('motif_sorti'))."</td>
					   <td  style='width: 10%;'><a href='javascript:vider_liberation()'><img id='test' style=' margin-left: 25%;' src='/simens/public/images_icons/118.png' title='vider tout'></a></td>
					 </tr>
					</table>";
			$html .="</div>";
			
			$html .="<div style=' margin-left:40px; color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:left;'>
                        <img  src='".$this->path."/images_icons/fleur1.jpg' />
                     </div>";
			
			$html .="<div style='width: 10%; padding-left: 30%; float:left;'>";
			$html .="<div class='block' id='thoughtbot' style=' float:left; width: 30%; vertical-align: bottom;  margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button type='submit' id='liberer'>Lib&eacute;rer</button></div>
                     </div>";
			$html .="<div class='block' id='thoughtbot' style=' float:left; width: 30%; vertical-align: bottom;  margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button id='terminerLiberer'>Annuler</button></div>
                     </div>";
			$html .="</div>";
			$html .="</form>";
				
			$html .="<script>  
					  function vider_liberation(){
	                   $('#resumer_medical').val('');
	                   $('#motif_sorti').val('');
		              }
					  $('#resumer_medical, #motif_sorti').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
					</script>
					";
		}
		$html .="</div>";
		
		$html .="<script> 
				  listepatient(); 
				 </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function infoPatientHospiAction(){
		$this->getDateHelper();
		$id_personne = $this->params()->fromPost('id_personne',0);
		$administrerSoin = $this->params()->fromPost('administrerSoin',0);
		$id_cons = $this->params()->fromPost('id_cons',0);
		$hospitaliser = $this->params()->fromPost('hospitaliser',0);
		
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);
		
		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
		
		$html  = "<div style='width:100%;'>";
			
		$html .= "<div style='width: 18%; height: 180px; float:left;'>";
		$html .= "<div id='photo' style='float:left; margin-left:40px; margin-top:10px; margin-right:30px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "' ></div>";
		$html .= "</div>";
			
		$html .= "<div style='width: 65%; height: 180px; float:left;'>";
		$html .= "<table style='margin-top:10px; float:left; width: 100%;'>";
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 23%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unPatient['NOM'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Lieu de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['LIEU_NAISSANCE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; actuelle:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['NATIONALITE_ACTUELLE'] . "</p></td>";
		$html .= "<td style='width: 27%; height: 50px;'></td>";
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 23%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['PRENOM'] . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['TELEPHONE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; d'origine:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['NATIONALITE_ORIGINE']. "</p></td>";
		$html .= "<td style='width: 27%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Email:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unPatient['EMAIL'] . "</p></td>";
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 23%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unPatient['ADRESSE'] . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Profession:</a><br><p style=' font-weight:bold; font-size:17px;'>" .  $unPatient['PROFESSION'] . "</p></td>";
		$html .= "<td style='width: 27%; height: 50px;'></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .="</div>";
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		if($hospitaliser == 1){
			$demande = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdcons($id_cons);
			$html .= "<div id='titre_info_deces'>Infos sur la demande d'hospitalisation </div>
		          <div id='barre'></div>";
			
			$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
			$html .= "<tr style='width: 80%;'>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Consultation:</a><br><p style='font-weight:bold; font-size:17px;'>" . $id_cons . "</p></td>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDateTime($demande['date_demande_hospi']) . "</p></td>";
			$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date fin pr&eacute;vue:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDate($demande['date_fin_prevue_hospi']) . "</p></td>";
			$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>M&eacute;decin demandeur:</a><br><p style=' font-weight:bold; font-size:17px;'>" .$demande['PrenomMedecin'].' '.$demande['NomMedecin']. "</p></td>";
			$html .= "</tr>";
			$html .= "</table>";
		}
		
		if($administrerSoin != 111) {
		$html .= "<div id='titre_info_deces'>Attribution d'un lit</div>
		          <div id='barre'></div>";
		
		$html .= "<script>$('#salle, #division, #lit').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'17px'});</script>";
		}else if($administrerSoin == 111){
			$html .= "<div id='titre_info_deces'>Ajout d'un soin</div>
		          <div id='barre'></div>";
			
			$html .= "<script>$('#salle, #division, #lit').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'17px'});</script>";
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function getLesHeuresDuSoin($id_sh, $date, $id_hosp=null){
		
		$heure = $this->getSoinHospitalisationTable()->getHeuresPourUneDate($id_sh, $date);
			
		$heureSuivante = $this->getSoinHospitalisationTable()->getHeureSuivantePourUneDate($id_sh, $date);
			
		$dateTime = new \DateTime();
		$aujoudhui = $dateTime->format('Y-m-d');
		
		if($date < $aujoudhui){
		   $heureSuivante = null; 
		}
		
		$lesHeures = "";
			
		//S'il y'a une heure suivante et bien-sur des heures pour appliquer des soins et la date soit celle d'aujourdhui
		if($heure && $heureSuivante && $date == $aujoudhui){
			for ($i = 0; $i<count($heure); $i++){
				$appliquer = $this->getSoinHospitalisationTable()->getHeureAppliqueePourUneDate($id_sh, $heure[$i], $date);
					
				if($i == count($heure)-1) {
					if($heureSuivante['heure'] == $heure[$i]){
						$lesHeures.= '<span id="clignoterHeure" style="font-weight: bold; color: orange;">'.$heure[$i].'</span>';
					}else{
							
						if($heure[$i] < $heureSuivante['heure'] &&  $appliquer['applique'] == 0){
							$lesHeures.= '<a style="text-decoration: none;" href="javascript:appliquerSoinPasse('.$id_sh.','.$id_hosp.','.$appliquer['id_heure'].')"> <span style="font-weight: bold; color: red; cursor:pointer;">'.$heure[$i].'</span></a> ';
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
							
						if($heure[$i] < $heureSuivante['heure'] &&  $appliquer['applique'] == 0){
							$lesHeures.= '<a style="text-decoration: none;" href="javascript:appliquerSoinPasse('.$id_sh.','.$id_hosp.','.$appliquer['id_heure'].')"> <span style="font-weight: bold; color: red; cursor:pointer;">'.$heure[$i].'</span></a>  -  ';
						} else if ($heure[$i] > $heureSuivante['heure']){
							$lesHeures.= '<span style="color: #ccc;">'.$heure[$i].' - </span>';
						  } else {
						  	$lesHeures.= $heure[$i].' - ';
						  }
							
					}
				}
			}
		}
			
		//S'il n'y a plus une heure suivante c'est a dire toutes les heures sont passées et la date soit celle d'aujourdhui
		if($heure && !$heureSuivante && $date == $aujoudhui){
			for ($i = 0; $i<count($heure); $i++){
				$appliquer = $this->getSoinHospitalisationTable()->getHeureAppliqueePourUneDate($id_sh, $heure[$i], $date);
					
				if($i == count($heure)-1) {
			
					if($appliquer['applique'] == 0){
						$lesHeures.= '<a style="text-decoration: none;" href="javascript:appliquerSoinPasse('.$id_sh.','.$id_hosp.','.$appliquer['id_heure'].')"> <span style="font-weight: bold; color: red; ">'.$heure[$i].'</span></a> ';
					} else {
						$lesHeures.= $heure[$i];
					}
			
				} else {
			
					if($appliquer['applique'] == 0){
						$lesHeures.= '<a style="text-decoration: none;" href="javascript:appliquerSoinPasse('.$id_sh.','.$id_hosp.','.$appliquer['id_heure'].')"> <span style="font-weight: bold; color: red; ">'.$heure[$i].'</span></a>  -  ';
					} else {
						$lesHeures.= $heure[$i].'  -  ';
					}
			
				}
					
			}
		}
		
		//S'il n'y a plus une heure suivante c'est a dire toutes les heures sont passées et les date passées
		if($heure && !$heureSuivante && $date != $aujoudhui){
			for ($i = 0; $i<count($heure); $i++){
				$appliquer = $this->getSoinHospitalisationTable()->getHeureAppliqueePourUneDate($id_sh, $heure[$i], $date);
					
				if($i == count($heure)-1) {
						
					if($appliquer['applique'] == 0){
						$lesHeures.= '<span style="font-weight: bold; color: orange; text-decoration:underline;">'.$heure[$i].'</span> ';
					} else {
						$lesHeures.= $heure[$i];
					}
						
				} else {
						
					if($appliquer['applique'] == 0){
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
 		
 		$lesHeures = $this->getLesHeuresDuSoin($id_sh, $aujourdhui, $soinHosp->id_hosp);
 		$finDuSoin = date("Y-m-d", strtotime($soinHosp->date_debut_application.'+'.($soinHosp->duree-1).' day' ));
 		
 		$dateAvenir = null;
 		if(!$lesHeures){
 			$dateAvenir = $soinHosp->date_debut_application;
 			$lesHeures  = $this->getHeuresAVenir($id_sh, $soinHosp->date_debut_application);
 		}
 		
 		$html  ="<table style='width: 99%;'>";

 		$html .="<tr style='width: 99%;'>
					   <td colspan='3' style='width: 99%;'>
					     <div id='titre_info_admis' style='margin-top: 0px;'>Prescription du soin : <i style='font-size: 15px;'>".$this->dateHelper->convertDateTime($soinHosp->date_enregistrement)."</i></div><div id='barre_admis'></div>
					   </td>
					 </tr>";
 		
 		$html .="<tr style='width: 99%; '>";
 		$html .="<td style='width: 36%; padding-top: 15px; padding-right: 15px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>M&eacute;dicament</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->medicament." </p></td>";
 		$html .="<td style='width: 33%; padding-top: 15px; padding-right: 15px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Voie d'administration</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->voie_administration." </p></td>";
 		$html .="<td style='width: 30%; padding-top: 15px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Dosage & Fr&eacute;quence</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->dosage." - ".$soinHosp->frequence."</p></td>";
 		$html .="</tr>";
 		
 		$html .="<tr style='width: 99%;'>";
 		$html .="<td style='vertical-align:top; padding-right: 15px; padding-top: 10px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Date de d&eacute;but & Dur&eacute;e & Fin</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$this->dateHelper->convertDate($soinHosp->date_debut_application)." - ".$soinHosp->duree." jr(s) - ".$this->dateHelper->convertDate($finDuSoin)."</p></td>";
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
 				           		
 				         <span class='laDateDesSoins' style='font-size: 15px; padding-left: 15px;'> Aujourd'hui - ".$this->dateHelper->convertDate($aujourdhui)."</span>
 				      </div>
 				      
 				      <div id='barre_admis'></div>
					</td>
			     </tr>";
 		
 		if($soinHosp){
 				
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
								        <div class='infoUtilisateur".$listeH['id_heure']."' style='float: right; padding-top: 10px; padding-right: 10px; cursor:pointer'> <img src='../images_icons/info_infirmier.png' title='Infirmier: ".$PrenomInfirmier." ".$NomInfirmier." ".$this->dateHelper->convertDateTime($listeH['date_application'])." ' /> </div>
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
 			
 			$html .="<script> var tableau = ['']; var tableauDate = ['']; var indice=0; var position = j; encours = 1; var tableauHeures = ['']; </script>";
 			//LISTE DES DATE EN TABLEAU JS
 			$listeTouteDate = $this->getSoinHospitalisationTable()->getToutesDateDuSoinPourUneDate($id_sh, $aujourdhui);
 			$lastDate = null;                                 
 			foreach ($listeTouteDate as $listeDate){          
 				$html .="<script> tableau[indice] = '".$listeDate['date']."'; </script>";
 				$html .="<script> tableauDate[indice] = '".$this->dateHelper->convertDate($listeDate['date'])."'; </script>";
 				$html .="<script> tableauHeures[indice++] = '".$this->getLesHeuresDuSoin($id_sh, $listeDate['date'], $soinHosp->id_hosp)."'; </script>";
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
	
	/*/*********************************************************************************************************************************
	 * ==============================================================================================================================
	* ******************************************************************************************************************************
	* ******************************************************************************************************************************
	* ==============================================================================================================================
	*/
	public function listePatientSuiviAjaxAction() {
		$output = $this->getDemandeHospitalisationTable()->getListePatientSuiviHospitalisation();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function suiviPatientAction() {
		$this->layout()->setTemplate('layout/Hospitalisation');
		
//		$Liste = $this->getSoinHospitalisationTable()->getListeDesSoinsDuJourAAppliquer();
//		var_dump($Liste); exit();
// 		$today = new \DateTime();
// 		$aujourdhui = $today->format('Y-m-d');
// 	    $Date = $this->getSoinHospitalisationTable()->getDateApresDateDonnee(7, $aujourdhui);
// 		
// 		$HeuresPourAujourdhui = $this->getSoinHospitalisationTable()->getHeuresPourAujourdhui(9);
// 		if($HeuresPourAujourdhui){ $h = "c bon"; }
// 		else{
// 			$h = "c pas bon";
// 		}

// 		$hd=explode(":","19:55"); //actu
// 		$hf=explode(":","21:00"); //fin
// 		$hd[0]=(int)($hd[0]);$hd[1]=(int)($hd[1]);
// 		$hf[0]=(int)($hf[0]);$hf[1]=(int)($hf[1]);
// 		if($hf[1]<$hd[1]){$hf[0]=$hf[0]-1;$hf[1]=$hf[1]+60;}
// 		if($hf[0]<$hd[0]){$hf[0]=$hf[0]+24;}
// 		$result = (($hf[0]-$hd[0]).":".($hf[1]-$hd[1]));
		
//         var_dump($result); exit();
		
		
		$formAppliquerSoin = new AppliquerSoinForm();
		
		return array(
				'form' => $formAppliquerSoin
		);
	}
	
	public function listeSoinsAAppliquer($id_hosp){
		$liste_soins = $this->getSoinHospitalisationTable()->getAllSoinhospitalisation($id_hosp);
		$html = "";
		$this->getDateHelper();
			
		$today = new \DateTime();
		$aujourdhui = $today->format('Y-m-d');
		
		$html .="<div style='margin-right: 40px; float:right; font-family: Times New Roman; font-size: 15px; color: green;'> 
				   <i style='cursor:pointer;' id='afficherTerminer'> Termin&eacute; </i> | 
				   <i style='cursor:pointer;' id='afficherEncours'> Encours </i> |
				   <i style='cursor:pointer;' id='afficherAvenir'> A venir </i>
				</div>";
		
		$html .="<div id='listeDeTousLesSoinsDuPatient'>
				 <table class='table table-bordered tab_list_mini'  style='margin-top:10px; margin-bottom:20px; margin-left:195px; width:80%;' id='listeSoin'>";
			
		$html .="<thead style='width: 100%;'>
				  <tr style='height:40px; width:100%; cursor:pointer; '>
					<th style='width: 24%;'>M<minus>&eacute;dicament</minus></th>
					<th style='width: 21%;'>V<minus>oie d'administration</minus></th>
					<th style='width: 22%;'>D<minus>osage </minus>  <text style='font-weight: normal; font-family: arial; color: green; font-size: 15px;'> X </text> F<minus>r&eacute;quence </minus></th>
					<th style='width: 17%;'>H<minus>eure suivante </minus></th>
				    <th style='width: 10%;'>O<minus>ptions</minus></th>
				    <th style='width: 6%;'>E<minus>tat</minus></th>
				  </tr>
			     </thead>";
			
		$html .="<tbody style='width: 100%;'>";
	
		$play = false;
		sort($liste_soins);
		foreach ($liste_soins as $cle => $Liste){
			//Récupération de l'heure suivante pour l'application du soin
			$heureSuivante = $this->getSoinHospitalisationTable()->getHeureSuivantePourAujourdhui($Liste['id_sh']);
			$heurePrecedente = $this->getSoinHospitalisationTable()->getHeurePrecedentePourAujourdhui($Liste['id_sh']);
			
			$temoin = 0;
			$idHeure = null;
			$heureSuiv = null;
			$application = false;
			if($heureSuivante || $heurePrecedente){
				$heurePrecedenteH = substr($heurePrecedente['heure'], 0, 2);
				
				$heureActuelleH = $today->format('H');
				$heureSuivanteH = substr($heureSuivante['heure'], 0, 2);
				
				if($heureSuivanteH - $heureActuelleH == 1){
					$heureActuelleM = $today->format('i');
					$heureSuivanteM = 60;
					$diff = $heureSuivanteM - $heureActuelleM;
					
					if($diff <= 10){
						$heureSuiv = "<khass id='alertHeureApplicationSoinUrgent".$Liste['id_sh']."' style='color: orange; font-weight: bold; font-size: 20px;'>".$heureSuivante['heure']."
								      </khass>"; 
						$temoin = 0;
						$application = true;
						$idHeure = $heureSuivante['id_heure'];
					}else if ($diff <= 30) {
						$heureSuiv = "<khass id='alertHeureApplicationSoin' style='color: orange; font-weight: bold; font-size: 20px;'>".$heureSuivante['heure']."</khass>";
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
				    		$application = true;
				    		$idHeure = $heurePrecedente['id_heure'];
				    	}else {
				    		if($heureSuivante['heure']){
				    			$heureSuiv = "<khass style='color: orange; font-weight: bold; font-size: 20px;'>".$heureSuivante['heure']."</khass>";
				    		}

				    	}
				    }
					else {
						if($heureSuivante['heure']){
							$heureSuiv = "<khass style='color: orange; font-weight: bold; font-size: 20px;'>".$heureSuivante['heure']."</khass>";
						}

					}
			}
			
			$html .="<tr style='width: 100%;' id='".$Liste['id_sh']."'>";
			$html .="<td style='width: 24%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['medicament']."</div></td>";
			$html .="<td style='width: 21%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['voie_administration']."</div></td>";
			$html .="<td style='width: 22%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['dosage']." <text style='font-weight: normal; font-family: arial; color: green; font-size: 13px;'> X </text> ".$Liste['frequence']."</div></td>";
			if($heureSuiv == null){
				$JourSuivant = $this->getSoinHospitalisationTable()->getDateApresDateDonnee($Liste['id_sh'], $aujourdhui);
				$HeuresPourAujourdhui = $this->getSoinHospitalisationTable()->getHeuresPourAujourdhui($Liste['id_sh']);
				if($JourSuivant && $HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:16px;'>
							     ".$this->dateHelper->convertDate($JourSuivant['date'])." - ".$JourSuivant['heure']."
							     <span style='font-size: 10px;' > soin_encours </span>
							   </div>
							   
							 </td>";
				}elseif($JourSuivant && !$HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:16px;'>
							     ".$this->dateHelper->convertDate($JourSuivant['date'])." - ".$JourSuivant['heure']."
							     <span style='font-size: 10px;' > soin_avenir </span>
							   </div>
							 </td>";
				}elseif(!$JourSuivant && $HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:17px;'>
							      Termin&eacute;
							      <span style='font-size: 10px;' > soin_encours </span>
							   </div>
							   
							</td>";
				}elseif(!$JourSuivant && !$HeuresPourAujourdhui){
					$html .="<td style='width: 17%;'>
							   <div id='inform' style='float:left; font-size:17px;'>
							      Termin&eacute;
							      <span style='font-size: 10px;' > soin_terminer </span>
							   </div>
							</td>";
				}

			}else{
				$html .="<td style='width: 17%;'>
						   <div id='inform' style='float:left; font-weight:bold; font-size:17px;'>
						     ".$heureSuiv."
						     <span style='font-size: 10px;' > soin_encours </span>
						   </div>
						 </td>";
			}
	
			$html .="<td style='width: 10%;'> <a href='javascript:vuesoin(".$Liste['id_sh'].") '>
					       <img class='visualiser".$Liste['id_sh']."' style='display: inline;' src='../images_icons/voird.png' title='d&eacute;tails' />
					  </a>&nbsp";
			
			if($heureSuiv && $application){
				$html .="<a href='javascript:appliquerSoin(".$Liste['id_sh'].",".$Liste['id_hosp'].",".$idHeure.",".$temoin.")'>
					    	<img class='modifier".$Liste['id_sh']."'  src='../img/dark/blu-ray.png' title='appliquer le soin'/>
					     </a>&nbsp;
				
				         </td>";
			}else {
				$html .="<a>
					    	<img class='modifier".$Liste['id_sh']."' style='color: white; opacity: 0.15;' src='../img/dark/blu-ray.png' title=''/>
					     </a>&nbsp;
				
				         </td>";
			}
			
			if($Liste['appliquer'] == 0) {
				$html .="<td style='width: 6%;'>
					       <img class='etat_oui".$Liste['id_sh']."' style='margin-left: 20%;' src='/simens/public/images_icons/non.png' title='pas totalement appliqu&eacute;' />
					     &nbsp;
				         </td>";
			}else {
				$html .="<td style='width: 6%;'>
					       <img class='etat_non".$Liste['id_sh']."' style='margin-left: 20%;' src='/simens/public/images_icons/oui.png' title='totalement appliqu&eacute;' />
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
	                  var intervalAlerte; 
	                  function FaireClignoterPourAlerte".$Liste['id_sh']." (){
                          $('#alertHeureApplicationSoinUrgent".$Liste['id_sh']."').fadeOut(250).fadeIn(200);
                      }

                      $(function(){
                          intervalAlerte = setInterval('FaireClignoterPourAlerte".$Liste['id_sh']." ()',500);
                      });
                          		

                      var intervalAlerteUrgent2;     		
                      function FaireClignoterPourAlerteUrgent2".$Liste['id_sh']." (){
                          $('#alertHeureApplicationSoinUrgent2".$Liste['id_sh']."').fadeOut(200).fadeIn(50);
                      }
                      $(function(){
                          intervalAlerteUrgent2 = setInterval('FaireClignoterPourAlerteUrgent2".$Liste['id_sh']." ()',500);
                      });
			        </script>";
		}
		$html .="</tbody>";
		$html .="</table></div>";
	
		if($play == true){
			$html .="<script>
					  var player = document.querySelector('#audioPlayer');
					  setTimeout(function(){
					    player.play();
					  },1000);
					
					  $('#play').val(1);
					  $('#listeSoin thead').click(function(){ player.play(); });
					</script>";
		}else {
			$html .="<script>
					  $('#play').val(0);
					</script>";
		}
		
		$html .="<style>
				  #listeDeTousLesSoinsDuPatient .listeDataTable{
	                margin-left: 185px;
                  }
	
				  #listeDeTousLesSoinsDuPatient div .dataTables_paginate
                  {
				    margin-right: 20px;
                  }
				
				  #listeSoin tbody tr{
				    background: #fbfbfb;
				  }
				
				  #listeSoin tbody tr:hover{
				    background: #fefefe;
				  }
				  
				 </style>";

		
		$html .="<script>
				  $('#listeSoin span').toggle(false);
				  listepatient(); listeDesSoins(); 
				  $('#id_hosp').val(".$id_hosp.");
				 </script>";
		
	
		return $html;
	}
	
	public function heureSuivanteAction() {
		$id_hosp = $this->params()->fromPost('id_hosp',0);
		$id_sh = $this->params()->fromPost('id_sh',0);
		$id_heure = $this->params()->fromPost('id_heure',0);
		
		$heureSuivante = $this->getSoinHospitalisationTable()->getHeure($id_heure, $id_sh);
		
			
		$heureSuivPopup = null;
		if($heureSuivante){
			$heureSuivPopup = $heureSuivante['heure'];
		}
		
		$html = $heureSuivPopup;
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function heurePasseeAction() {
		$id_hosp = $this->params()->fromPost('id_hosp',0);
		$id_sh = $this->params()->fromPost('id_sh',0);
		$id_heure = $this->params()->fromPost('id_heure',0);
	
		$heureSuivante = $this->getSoinHospitalisationTable()->getHeure($id_heure, $id_sh);
			
		$heureSuivPopup = null;
		if($heureSuivante){
			$heureSuivPopup = '<span style="color: red;">'.$heureSuivante['heure'].'</span>';
		}
	
		$html = $heureSuivPopup;
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function administrerSoinPatientAction() {
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
	
		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
	
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
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
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
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDateTime($demande['Datedemandehospi']) . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date fin pr&eacute;vue:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDate($demande['date_fin_prevue_hospi']) . "</p></td>";
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
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date d&eacute;but:</a><br><p style='font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDateTime($hospitalisation->date_debut) . "</p></td>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Batiment:</a><br><p style=' font-weight:bold; font-size:17px;'>".$batiment->intitule."</p></td>";
			$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Salle:</a><br><p style=' font-weight:bold; font-size:17px;'>".$salle->numero_salle."</p></td>";
			$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Lit:</a><br><p style=' font-weight:bold; font-size:17px;'>".$lit->intitule."</p></td>";
			$html .= "</tr>";
			$html .= "</table>";
			$html .= "</div>";
		}
	
		$html .= "<div id='titre_info_deces'>
				    <span id='titre_info_liste' style='margin-left:-10px; cursor:pointer;'>
				      <img src='".$chemin."/img/light/minus.png' /> Liste des soins
				    </span>
				  </div>
		          <div id='barre'></div>";
	
		$hospitalisation = $this->getHospitalisationTable()->getHospitalisationWithCodedh($id_demande_hospi);
		$html .= "<div id='info_liste' class='listeSoinAAppliquer' >";
		$html .= $this->listeSoinsAAppliquer($hospitalisation->id_hosp);
		$html .= "</div>";
	
		if($terminer == 0) {
			$html .="<div style='width: 100%; height: 100px;'>
	    		     <div style='margin-left:40px; color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:left;'>
                        <img  src='".$this->path."/images_icons/fleur1.jpg' />
                     </div>";
			$html .="<div class='block' id='thoughtbot' style='vertical-align: bottom; padding-left:60%; margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button type='submit' id='terminerdetailhospi'>Terminer</button></div>
                     </div>";
		}
	
		$html .="<script>
				  listepatient();
				  initAnimation();
				  animationPliantDepliant2();
				  animationPliantDepliant3();
		          animationPliantDepliant4();
				 </script>";
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function applicationSoinAction() {
		$id_heure = $this->params()->fromPost('id_heure', 0);
		$id_sh = $this->params()->fromPost('id_sh', 0);
		$note = $this->params()->fromPost('note', 0);
		
		$user = $this->layout()->user;
		$id_personne = $user['id_personne']; //L'infirmier qui a appliqué le soin au patient
		
		$this->getSoinHospitalisationTable()->saveHeureSoinAppliquer($id_heure, $id_sh, $note, $id_personne);
		
		$heureSuivante = $this->getSoinHospitalisationTable()->getHeureSuivante($id_sh);
		if(!$heureSuivante){ //S'il n y avait aucune heure suivante On met Appliquer a 1 (dans la table soinHospitalisation3)
			$this->getSoinHospitalisationTable()->appliquerSoin($id_sh, $note, $id_personne);			
		}

		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode (  ) );
	}
	
	
	public function raffraichirListeAction() {
		$id_hosp = $this->params()->fromPost('id_hosp',0);
		
		$html = $this->listeSoinsAAppliquer($id_hosp);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function listeSoinsDuJourAAppliquerAction() {

 		$liste_soins_a_appliquer = $this->getSoinHospitalisationTable()->getListeDesSoinsDuJourAAppliquer();
		$html = "";
		$this->getDateHelper();
			
		$today = new \DateTime();
		$aujourdhui = $today->format('Y-m-d');
		
		$html .="<table class='table table-bordered tab_list_mini'  style='margin-top:0px; margin-bottom:20px; width:100%;' id='listeSoinsDuJourAAppliquer'>";
			
		$html .="<thead style='width: 100%;'>
				  <tr style='height:45px; width:100%; cursor:pointer;'>
					<th style='width: 20%; font-size: 15px;'>P<minus>r&eacute;nom</minus> & N<minus>om </minus></th>
				    <th style='width: 10%; font-size: 15px;'>S<minus>alle</minus></th>
					<th style='width: 10%; font-size: 15px;'>L<minus>it</minus></th>
				    <th style='width: 22%; font-size: 15px;'>M<minus>&eacute;dicament</minus></th>
					<th style='width: 16%; font-size: 15px;'>D<minus>osage </minus>  <text style='font-weight: normal; font-family: arial; color: green; font-size: 15px;'> X </text> F<minus>r&eacute;quence </minus></th>
					<th style='width: 7%; font-size: 15px;'>H<minus>eure </minus></th>
				    <th style='width: 9%; font-size: 15px;'>O<minus>ptions</minus></th>
				    <th style='width: 6%; font-size: 15px;'>E<minus>tat</minus></th>
				  </tr>
			     </thead>";
			
		$html .="<tbody style='width: 100%;'>";
		
		$play = false;
		$temoin = 0;
		$couleurheurePopup = 0;
 		foreach ($liste_soins_a_appliquer as $Liste){

 			$heureSoinH = substr($Liste['heure'], 0, 2);
 			$heureActuelleH = $today->format('H');
 			$heureActuelleM = $today->format('i');
 			
 			$application = null;
 			if($heureSoinH == $heureActuelleH){
 				$application = true;
 				if($heureActuelleM <= 30){
 					$heureSuiv ="<khass id='alertHeureApplicationSoinUrgent2Liste".$Liste['id_heure']."' style='color: red; font-weight: bold; font-size: 20px;'>".$Liste['heure']."
								         </khass>
								         <audio class='audio' id='audioPlayer' src='../images_icons/alarme.mp3' ></audio>";
 					$temoin = 1;
 					$play = true;
 					$application = true;
 				}else {
 					$heureSuiv ="<khass style='color: red; font-weight: bold; font-size: 20px;'>".$Liste['heure']."
 							     </khass>";
 				}
 				$couleurheurePopup = 1;
 			} else 
 				if($heureSoinH - $heureActuelleH == 1){
 					$diff = 60 - $heureActuelleM;
 					
 					if($diff <= 10){
 						$heureSuiv = "<khass id='alertHeureApplicationSoinUrgentListe".$Liste['id_heure']."' style='color: orange; font-weight: bold; font-size: 20px;'>".$Liste['heure']."
								      </khass>";
 						$temoin = 0;
 						$couleurheurePopup = 2;
 					}else if ($diff <= 20) {
 						$heureSuiv = "<khass id='alertHeureApplicationSoinListe".$Liste['id_heure']."' style='color: orange; font-weight: bold; font-size: 20px;'>".$Liste['heure']."</khass>";
 						$couleurheurePopup = 2;
 					}else if ($diff <= 30){
 						$heureSuiv = "<khass style='color: orange; font-weight: bold; font-size: 20px;'>".$Liste['heure']."</khass>";
 						$couleurheurePopup = 2;
 					}else {
 						$heureSuiv = "<khass style='color: gray; font-weight: normal; font-size: 20px; opacity: 0.85;'>".$Liste['heure']."</khass>";
 						$couleurheurePopup = 3;
 					}
 				}else 
 					if($heureSoinH < $heureActuelleH){
 						$heureSuiv = "<khass style='color: red; font-weight: normal; font-size: 20px; opacity: 0.50;'>".$Liste['heure']."</khass>";
 						$couleurheurePopup = 1;
 					}else{
 						$heureSuiv = "<khass style='color: gray; font-weight: normal; font-size: 20px; opacity: 0.85;'>".$Liste['heure']."</khass>";
 						$couleurheurePopup = 3;
 					}
 			

 			
			$html .="<tr style='width: 100%;' id='".$Liste['id_heure']."'>";
			$html .="<td style='width: 20%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$Liste['PRENOM'].'  '.$Liste['NOM']."</div></td>";
			$html .="<td style='width: 10%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$Liste['numero_salle']."</div></td>";
			$html .="<td style='width: 10%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$Liste['intitule']."</div></td>";
			$html .="<td style='width: 22%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$Liste['medicament']."</div></td>";
			$html .="<td style='width: 16%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$Liste['dosage']."<text style='font-weight: normal; font-family: arial; color: green; font-size: 13px;'> X </text> ".$Liste['frequence']."</div></td>";
			$html .="<td style='width:  7%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$heureSuiv."</div></td>";
			$html .="<td style='width: 9%;'> <a href='javascript:vueSoinListe(".$Liste['id_sh'].",".$Liste['id_heure'].",".$couleurheurePopup.") '>
					       <img class='visualiser".$Liste['id_heure']."' src='../images_icons/voird.png' title='d&eacute;tails' />
					  </a>&nbsp";
			if($application){
				$html .="
						   <a href='javascript:appliquerSoinListe(".$Liste['id_sh'].",".$Liste['id_hosp'].",".$Liste['id_heure'].")'>
					    	  <img class='modifier".$Liste['id_heure']."'  src='../img/dark/blu-ray.png' title='appliquer le soin'/>
					       </a>&nbsp;
			
				         </td>";
			}else {
				$html .="
						   <a>
					    	  <img class='modifier".$Liste['id_heure']."' style='color: white; opacity: 0.15;' src='../img/dark/blu-ray.png' />
					       </a>&nbsp;
			
				         </td>";
			}
			
			if($Liste['applique'] == 0) {
				$html .="<td style='width: 6%;'>
					       <img class='etat_oui".$Liste['id_heure']."' style='margin-left: 20%;' src='/simens/public/images_icons/non.png' title='non encore appliqu&eacute;' />
					     &nbsp;
				         </td>";
			}else {
				$html .="<td style='width: 6%;'>
					       <img class='etat_non".$Liste['id_heure']."' style='margin-left: 20%;' src='/simens/public/images_icons/oui.png' title='appliqu&eacute;' />
					     &nbsp;
				         </td>";
			}

			
 			$html .="</tr>";
		
 			$html .="<script>
					  $('.visualiser".$Liste['id_heure']." ').mouseenter(function(){
	                    var tooltips = $( '.visualiser".$Liste['id_heure']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.visualiser".$Liste['id_heure']."').mouseleave(function(){
	                    var tooltips = $( '.visualiser".$Liste['id_heure']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /************************/
	                  /************************/
	                  /************************/
                      $('.modifier".$Liste['id_heure']." ').mouseenter(function(){
	                    var tooltips = $( '.modifier".$Liste['id_heure']." ' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
				      $('.modifier".$Liste['id_heure']." ').mouseleave(function(){
	                    var tooltips = $( '.modifier".$Liste['id_heure']." ' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.supprimer".$Liste['id_heure']." ').mouseenter(function(){
	                    var tooltips = $( '.supprimer".$Liste['id_heure']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.supprimer".$Liste['id_heure']."').mouseleave(function(){
	                    var tooltips = $( '.supprimer".$Liste['id_heure']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
		
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_oui".$Liste['id_heure']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_oui".$Liste['id_heure']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_oui".$Liste['id_heure']."').mouseleave(function(){
	                    var tooltips = $( '.etat_oui".$Liste['id_heure']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_non".$Liste['id_heure']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_non".$Liste['id_heure']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_non".$Liste['id_heure']."').mouseleave(function(){
	                    var tooltips = $( '.etat_non".$Liste['id_heure']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  
	                    		
	                    		
	                  var intervalAlerteListe;
	                  function FaireClignoterPourAlerteListe".$Liste['id_heure']." (){
                          $('#alertHeureApplicationSoinUrgentListe".$Liste['id_heure']."').fadeOut(250).fadeIn(200);
                      }
                      $(function(){
                          intervalAlerteListe = setInterval('FaireClignoterPourAlerteListe".$Liste['id_heure']." ()',500);
                      });
		
                          		
                          		
		
                      var intervalAlerteUrgent2Liste;
                      function FaireClignoterPourAlerteUrgent2Liste".$Liste['id_heure']." (){
                          $('#alertHeureApplicationSoinUrgent2Liste".$Liste['id_heure']."').fadeOut(200).fadeIn(50);
                      }
                      $(function(){
                          intervalAlerteUrgent2Liste = setInterval('FaireClignoterPourAlerteUrgent2Liste".$Liste['id_heure']." ()',500);
                      });
                          		
                       
                          		
                          		
                      var intervalalertHeureApplicationSoinListe;
                      function FaireClignoterPouralertHeureApplicationSoinListe".$Liste['id_heure']." (){
                          $('#alertHeureApplicationSoinListe".$Liste['id_heure']."').fadeOut(1500).fadeIn(1000); 
                      }
                      $(function(){
                          intervalalertHeureApplicationSoinListe = setInterval('FaireClignoterPouralertHeureApplicationSoinListe".$Liste['id_heure']." ()',500);
                      });
 			        </script>";
 		}
 		
 		$liste_soins_deja_appliquer = $this->getSoinHospitalisationTable()->getListeDesSoinsDuJourDejaAppliquer();
 		$couleurheurePopup = 0;
 		foreach ($liste_soins_deja_appliquer as $Liste) {
 			
 			$heureSuiv = "<khass style='color: green; font-size: 20px;'>".$Liste['heure']."</khass>";
 			
 			$html .="<tr style='width: 100%;' id='".$Liste['id_heure']."'>";
 			$html .="<td style='width: 20%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$Liste['PRENOM'].'  '.$Liste['NOM']."</div></td>";
 			$html .="<td style='width: 10%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$Liste['numero_salle']."</div></td>";
 			$html .="<td style='width: 10%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$Liste['intitule']."</div></td>";
 			$html .="<td style='width: 22%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$Liste['medicament']."</div></td>";
 			$html .="<td style='width: 16%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$Liste['dosage']."<text style='font-weight: normal; font-family: arial; color: green; font-size: 13px;'> X </text> ".$Liste['frequence']."</div></td>";
 			$html .="<td style='width:  7%;'><div id='inform' style='float:left; font-weight: normal; font-size:17px;'>".$heureSuiv."</div></td>";
 			$html .="<td style='width: 9%;'> <a href='javascript:vueSoinListe(".$Liste['id_sh'].",".$Liste['id_heure'].",".$couleurheurePopup.") '>
					      <img class='visualiser".$Liste['id_heure']."' src='../images_icons/voird.png' title='d&eacute;tails' />
					  </a>&nbsp";
 			
 			$html .="<a>
 					   <img style='color: white; opacity: 0;' src='../img/dark/blu-ray.png' />
 					 </a>&nbsp;
			         </td>";
 				
 			$html .="<td style='width: 6%;'>
 					  <img class='etat_non".$Liste['id_heure']."' style='margin-left: 20%;' src='/simens/public/images_icons/oui.png' title='appliqu&eacute;' />
 					  &nbsp;
				    </td>";
 			
 				
 			$html .="</tr>";
 			
 			$html .="<script>
					  $('.visualiser".$Liste['id_heure']." ').mouseenter(function(){
	                    var tooltips = $( '.visualiser".$Liste['id_heure']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.visualiser".$Liste['id_heure']."').mouseleave(function(){
	                    var tooltips = $( '.visualiser".$Liste['id_heure']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_oui".$Liste['id_heure']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_oui".$Liste['id_heure']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_oui".$Liste['id_heure']."').mouseleave(function(){
	                    var tooltips = $( '.etat_oui".$Liste['id_heure']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_non".$Liste['id_heure']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_non".$Liste['id_heure']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_non".$Liste['id_heure']."').mouseleave(function(){
	                    var tooltips = $( '.etat_non".$Liste['id_heure']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
 			        </script>";
 		}
 		
 		
 		
		$html .="</tbody>";
		$html .="</table>";
		
		if($play == true){
			$html .="<script>
					  var player = document.querySelector('#audioPlayer');
					  setTimeout(function(){
					  player.play();
					  },1000);
			
					  $('#play').val(1);
					  $('#listeSoin thead').click(function(){ player.play(); });
					</script>";
		}else {
			$html .="<script>
					  $('#play').val(0);
					</script>";
		}
		
		$html .="<style>
		
				  .dataTables_paginate
                  {
				    margin-right: 0px;
                  }
		
				  #listeSoinsDuJourAAppliquer tbody tr{
				    background: #fbfbfb;
				  }
		
				  #listeSoinsDuJourAAppliquer tbody tr:hover{
				    background: #fefefe;
				  }
				
				 </style>";
		
		
		$html .="<script>
				  listeDesSoinsDuJourAAppliquer();
				 </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function detailsInfosSoinAAppliquerAction() {
		$this->getDateHelper();
 		$id_sh = $this->params()->fromPost('id_sh', 0);
 		$soinHosp = $this->getSoinHospitalisationTable()->getSoinhospitalisationWithId_sh($id_sh);
 		
 		$id_heure = $this->params()->fromPost('id_heure', 0);
 		$couleurheurePopup = $this->params()->fromPost('couleurheurePopup', 0);
 		$HeureSoin = $this->getSoinHospitalisationTable()->getHeureSoin($id_heure, $id_sh);
 		$HeureDuSoinEnCouleur = "";
 		if($couleurheurePopup == 0){ $HeureDuSoinEnCouleur = "<span style='font-size: 20px; color: green;'>".$HeureSoin['heure']."</span>"; }
 		if($couleurheurePopup == 1){ $HeureDuSoinEnCouleur = "<span style='font-size: 20px; color: red; font-weight: bold;'>".$HeureSoin['heure']."</span>"; }
 		if($couleurheurePopup == 2){ $HeureDuSoinEnCouleur = "<span style='font-size: 20px; color: orange;'>".$HeureSoin['heure']."</span>"; }
 		if($couleurheurePopup == 3){ $HeureDuSoinEnCouleur = "<span style='font-size: 20px; color: gray;'>".$HeureSoin['heure']."</span>"; }
 		
 		$today = new \DateTime();
 		$aujourdhuiDateTime = $today->format('Y-m-d H:i:s');
 		$aujourdhui = $today->format('Y-m-d'); 

 		$finDuSoin = date("Y-m-d", strtotime($soinHosp->date_debut_application.'+'.($soinHosp->duree-1).' day' ));

 		$hospitalisation = $this->getHospitalisationTable()->getHospitalisation($soinHosp->id_hosp);
 		$DemHospitalisation = $this->getDemandeHospitalisationTable()->getDemandehospitalisationWithIdDemandeHospi($hospitalisation->code_demande_hospitalisation);
 		$DemHospitalisationCons = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdcons($DemHospitalisation->id_cons);
 		
 		$unPatient = $this->getPatientTable()->getInfoPatient($DemHospitalisationCons['id']);
 		$photo = $this->getPatientTable()->getPhoto($DemHospitalisationCons['id'] );
 		
 		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
 		
 		$html  = "<div style='width:99%; background: red;'>";
 			
 		$html .= "<div style='width: 18%; height: 180px; float:left;'>";
 		$html .= "<div id='photo' style='float:left;margin-top:10px; margin-right:30px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "' ></div>";
 		$html .= "</div>";
 			
 		$html .= "<div style='width: 80%; height: 180px; float:left; margin-bottom: 10px; margin-left: 10px;'>";
 		$html .= "<table style='margin-top:10px; float:left; width: 100%;'>";
 		$html .= "<tr style='width: 100%;'>";
 		$html .= "<td style='width: 30%; height: 50px; vertical-align: top; '><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='font-weight:bold; font-size:17px; margin-right: 15px;'>" . $unPatient['NOM'] . "</p></td>";
 		$html .= "<td style='width: 35%; height: 50px; vertical-align: top; '><a style='text-decoration:underline; font-size:12px;'>Lieu de naissance:</a><br><p style=' font-weight:bold; font-size:17px; margin-right: 15px;'>" . $unPatient['LIEU_NAISSANCE'] . "</p></td>";
 		$html .= "<td style='width: 35%; height: 50px; vertical-align: top; '><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; actuelle:</a><br><p style=' font-weight:bold; font-size:17px; margin-right: 15px;'>" . $unPatient['NATIONALITE_ACTUELLE'] . "</p></td>";
 		$html .= "</tr><tr style='width: 100%;'>";
 		$html .= "<td style='height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style=' font-weight:bold; font-size:17px; margin-right: 15px;'>" . $unPatient['PRENOM'] . "</p></td>";
 		$html .= "<td style='height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style=' font-weight:bold; font-size:17px; margin-right: 15px;'>" . $unPatient['TELEPHONE'] . "</p></td>";
 		$html .= "<td style='height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; d'origine:</a><br><p style=' font-weight:bold; font-size:17px; margin-right: 15px;'>" . $unPatient['NATIONALITE_ORIGINE']. "</p></td>";
 		$html .= "</tr><tr style='width: 100%;'>";
 		$html .= "<td style='height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style=' font-weight:bold; font-size:17px; margin-right: 15px;'>" . $date . "</p></td>";
 		$html .= "<td style='height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style=' font-weight:bold; font-size:17px; margin-right: 15px;'>" . $unPatient['ADRESSE'] . "</p></td>";
 		$html .= "<td style='height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Profession:</a><br><p style=' font-weight:bold; font-size:17px; margin-right: 15px;'>" .  $unPatient['PROFESSION'] . "</p></td>";
 		$html .= "</tr>";
 		$html .= "</table>";
 		$html .="</div>";
 		$html .="</div>";
 		
 		
 		$html .="<table style='width: 99%;'>";

 		$html .="<tr style='width: 99%;'>
					   <td colspan='3' style='width: 99%;'>
					     <div id='titre_info_admis' style='margin-top: 0px;'>Prescription du soin : <i style='font-size: 15px;'>".$this->dateHelper->convertDateTime($soinHosp->date_enregistrement)."</i></div><div id='barre_admis'></div>
					   </td>
					 </tr>";
 		
 		$html .="<tr style='width: 99%; '>";
 		$html .="<td style='width: 36%; padding-top: 15px; padding-right: 15px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>M&eacute;dicament</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->medicament." </p></td>";
 		$html .="<td style='width: 33%; padding-top: 15px; padding-right: 15px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Voie d'administration</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->voie_administration." </p></td>";
 		$html .="<td style='width: 30%; padding-top: 15px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Dosage <span style='font-weight: normal; font-family: arial; font-size:14px;'> X </span> Fr&eacute;quence</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->dosage."<span style='font-weight: normal; font-family: arial; font-size:14px;'> X </span>".$soinHosp->frequence."</p></td>";
 		$html .="</tr>";
 		
 		$html .="<tr style='width: 99%;'>";
 		$html .="<td style='vertical-align:top; padding-right: 15px; padding-top: 10px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Date de d&eacute;but - Dur&eacute;e - Fin</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$this->dateHelper->convertDate($soinHosp->date_debut_application)." - ".$soinHosp->duree." jr(s) - ".$this->dateHelper->convertDate($finDuSoin)."</p></td>";
 		$html .="<td style='vertical-align:top; padding-right: 15px; padding-top: 10px;'><span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Date et heure actuelle</span><br><p id='zoneChampInfo' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$this->dateHelper->convertDateTime($aujourdhuiDateTime)."</p></td>";
 		$html .="<td style='vertical-align:top; padding-top: 10px;'>
				     <span style='text-decoration:underline; font-weight:bold; font-size:15px; color: #065d10; font-family: Times  New Roman;'>Heure</span><br><p id='zoneChampInfo' class='lesHeuresRecAppDuSoin'  style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$HeureDuSoinEnCouleur." </p>
				 </td>
				 </tr>";
 		
 		$html .="</table>";
 		
 		$html .="<table style='width: 99%;'>";
 		$html .="<tr style='width: 95%;'>";
 		$html .="<td style='width: 50%; padding-top: 10px; padding-right:25px;'><span style='text-decoration:underline; font-weight:bold; font-size:16px; color: #065d10; font-family: Times  New Roman;'>Motif</span><br><p id='label_Champ_NoteInformation' style='background:#f8faf8; font-size:17px; padding-left: 10px;'> ".$soinHosp->motif." </p></td>";
 		$html .="<td style='width: 50%; padding-top: 10px;'><span style='text-decoration:underline; font-weight:bold; font-size:16px; color: #065d10; font-family: Times  New Roman;'>Note</span><br><p id='label_Champ_NoteInformation' style='background:#f8faf8; font-size:17px; padding-left: 5px;'> ".$soinHosp->note." </p></td>";
 		$html .="<td style='width: 0%;'> </td>";
 		$html .= "</tr></table>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function heureDuSoinAction() {
		$id_hosp = $this->params()->fromPost('id_hosp',0);
		$id_sh = $this->params()->fromPost('id_sh',0);
		$id_heure = $this->params()->fromPost('id_heure',0);
	
		$heureDuSoin = $this->getSoinHospitalisationTable()->getHeureSoin($id_heure, $id_sh);
	
		$heureSoinPopup = null;
		if($heureDuSoin){
			$heureSoinPopup = $heureDuSoin['heure'];
		}
	
		$html = "<span style='color: red;'>".$heureSoinPopup."</span>";
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	/*/*********************************************************************************************************************************
	 * ==============================================================================================================================
	* ******************************************************************************************************************************
	* ******************************************************************************************************************************
	* ==============================================================================================================================
	*/
	/*EXAMEN EXAMEN EXAMEN EXAMEN EXAMEN EXAMEN EXAMEN EXAMEN EXAMEN EXAMEN EXAMEN EXAMEN EXAMEN EXAMEN */
	/*****************************************************************************************************************************/
	/*****************************************************************************************************************************/
	
	public function listeDemandesExamensAjaxAction() {
		$output = $this->getDemandeTable()->getListeDemandesExamens();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeDemandesExamensAction() {
		$this->layout()->setTemplate('layout/Hospitalisation');
		
		$formAppliquerExamen = new AppliquerExamenForm();
		
		return array(
				'form' => $formAppliquerExamen
		);
	}
	
	
	public function listeExamensBiologiquesAction($id_cons) {
	
		$liste_examens_demandes = $this->getDemandeTable()->getDemandesExamensBiologiques($id_cons);
		$html = "";
		$this->getDateHelper();
			
		$html .="<table class='table table-bordered tab_list_mini'  style='margin-top:10px; margin-bottom:20px; margin-left:195px; width:80%;' id='listeDesExamens'>";
			
		$html .="<thead style='width: 100%;'>
				  <tr style='height:40px; width:100%; cursor:pointer; font-family: Times New Roman; font-weight: bold;'>
					<th style='width: 9%;'>Num&eacute;ro</th>
					<th style='width: 32%;'>Libelle examen</th>
					<th style='width: 40%;'>Note</th>
				    <th style='width: 13%;'>Options</th>
				    <th style='width: 6%;'>Etat</th>
				  </tr>
			     </thead>";
			
		$html .="<tbody style='width: 100%;'>";
		$cmp = 1;
		foreach ($liste_examens_demandes as $Liste){
			$html .="<tr style='width: 100%;' id='".$Liste['idDemande']."'>";
			$html .="<td style='width: 9%;'><div id='inform' style='margin-left: 25%; float:left; font-weight:bold; font-size:17px;'> " .$cmp++. ". </div></td>";
			$html .="<td style='width: 32%;'><div  style='color: green; font-family: Times New Roman; float:left; font-weight: bold; font-size:18px;'> " .$this->getExamenTable()->getExamen($Liste['idExamen'])->libelleExamen. " </div></td>";
			$html .="<td style='width: 40%;'><div  style='color: green; font-family: Times New Roman; float:left; font-weight: bold; font-size:18px;'> " .$Liste['noteDemande']. " </div></td>";
			$html .="<td style='width: 13%;'>
  					    <a href='javascript:vueExamenBio(".$Liste['idDemande'].") '>
  					       <img class='visualiser".$Liste['idDemande']."' style='margin-right: 9%; margin-left: 3%;' src='../images_icons/voird.png' title='d&eacute;tails' />
  					    </a>&nbsp";
	
			if($Liste['appliquer'] == 0) {
				$html .="<a href='javascript:appliquerExamenBio(".$Liste['idDemande'].")'>
 					    	<img class='modifier".$Liste['idDemande']."' style='margin-right: 16%;' src='../images_icons/aj.gif' title='Entrer les r&eacute;sultats'/>
 					     </a>";
					
				$html .="<a>
 					    	<img style='color: white; opacity: 0.09;' src='../images_icons/74biss.png'  />
 					     </a>
 				         </td>";
					
				$html .="<td style='width: 6%;'>
  					     <a>
  					        <img class='etat_non".$Liste['idDemande']."' style='margin-left: 25%;' src='../images_icons/non.png' title='examen non encore effectu&eacute;' />
  					     </a>
  					     </td>";
			}else {
					
				$resultat = $this->getResultatExamenTable()->getResultatExamen($Liste['idDemande']);
	
				if($resultat->envoyer == 1) {
					$html .="<a>
 					    	<img style='margin-right: 16%; color: white; opacity: 0.09;' src='../images_icons/pencil_16.png'/>
 					     </a>";
	
					if($Liste['responsable'] == 1) { /*Envoyer par le medecin*/
						$html .="<a>
 					    	<img class='envoyer".$Liste['idDemande']."' src='../images_icons/tick_16.png' title='examen valid&eacute; par le medecin'/>
 					     </a>
 				         </td>";
					} else
					{ /*Envoyer par le laborantin*/
						$html .="<a>
 					    	<img class='envoyer".$Liste['idDemande']."' src='../images_icons/tick_16.png' title='examen envoy&eacute;'/>
 					     </a>
 				         </td>";
					}
	
	
				} else {
					$html .="<a href='javascript:modifierExamenBio(".$Liste['idDemande'].")'>
 					    	<img class='modifier".$Liste['idDemande']."' style='margin-right: 16%;' src='../images_icons/pencil_16.png'  title='modifier r&eacute;sultat'/>
 					     </a>";
					$html .="<a href='javascript:envoyerBio(".$Liste['idDemande'].")'>
 					    	<img class='envoyer".$Liste['idDemande']."' src='../images_icons/74biss.png'  title='envoyer'/>
 					     </a>
 				         </td>";
				}
					
					
				$html .="<td style='width: 6%;'>
  					     <a>
  					        <img class='etat_oui".$Liste['idDemande']."' style='margin-left: 25%;' src='../images_icons/oui.png' title='examen d&eacute;ja effectu&eacute;' />
  					     </a>
  					     </td>";
			}
	
			$html .="</tr>";
	
			$html .="<script>
					  $('.visualiser".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.visualiser".$Liste['idDemande']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.visualiser".$Liste['idDemande']."').mouseleave(function(){
	                    var tooltips = $( '.visualiser".$Liste['idDemande']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /************************/
	                  /************************/
	                  /************************/
                      $('.modifier".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.modifier".$Liste['idDemande']." ' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
				      $('.modifier".$Liste['idDemande']." ').mouseleave(function(){
	                    var tooltips = $( '.modifier".$Liste['idDemande']." ' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.supprimer".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.supprimer".$Liste['idDemande']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.supprimer".$Liste['idDemande']."').mouseleave(function(){
	                    var tooltips = $( '.supprimer".$Liste['idDemande']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_oui".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_oui".$Liste['idDemande']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_oui".$Liste['idDemande']."').mouseleave(function(){
	                    var tooltips = $( '.etat_oui".$Liste['idDemande']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_non".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_non".$Liste['idDemande']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_non".$Liste['idDemande']."').mouseleave(function(){
	                    var tooltips = $( '.etat_non".$Liste['idDemande']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                   /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.envoyer".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.envoyer".$Liste['idDemande']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
			        </script>";
		}
		$html .="</tbody>";
		$html .="</table>";
	
		$html .="<style>
				  #listeDataTable{
	                margin-left: 185px;
                  }
	
				  div .dataTables_paginate
                  {
				    margin-right: 20px;
                  }
	
				  #listeDesExamens tbody tr{
				    background: #fbfbfb;
				  }
	
				  #listeDesExamens tbody tr:hover{
				    background: #fefefe;
				  }
	
				 </style>";
		$html .="<script> listepatient (); listeDesSoins(); $('#Examen_id_cons').val('".$id_cons."'); </script>";
	
		return $html;
	
	}
	
	
	public function listeExamensDemanderAction() {

		$this->getDateHelper();
		$id_personne = $this->params()->fromPost('id_personne',0);
		$id_cons = $this->params()->fromPost('id_cons',0);
		$terminer = $this->params()->fromPost('terminer',0); 
		$examensBio = $this->params()->fromPost('examensBio',0);
		
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);

		$demande = $this->getDemandeTable()->getDemandeWithIdcons($id_cons);
		
		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
	
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
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		$html .= "<div id='titre_info_deces'>Informations sur la demande d'examen </div>
		          <div id='barre'></div>";
		foreach ($demande as $donnees) {
			$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
			$html .= "<tr style='width: 80%;'>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Consultation:</a><br><p style='font-weight:bold; font-size:17px;'>" . $id_cons . "</p></td>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" .$this->dateHelper->convertDateTime($donnees['Datedemande']). "</p></td>";
			$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>M&eacute;decin demandeur:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $donnees['PrenomMedecin'] ." ".$donnees['NomMedecin'] . "</p></td>";
			$html .= "</tr>";
			$html .= "</table>";
		}

		$html .= "<div id='titre_info_deces'>Liste des examens demand&eacute;s </div>
		          <div id='barre'></div>
				
				 <div id='info_liste'>";
		if($examensBio == 1){
			$html .= $this->listeExamensBiologiquesAction($id_cons);
		}
		else /* POUR LES EXAMENS MORPHOLOGIQUES (Radiologie ... )*/
		   {
		   	$html .= $this->listeExamensAction($id_cons);
		   }
		$html .="</div>";
		
		if($terminer == 0) {
			$html .="<div style='width: 100%; height: 100px;'>
	    		     <div style='margin-left:40px; color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:left;'>
                        <img  src='".$this->path."/images_icons/fleur1.jpg' />
                     </div>";
			$html .="<div class='block' id='thoughtbot' style='vertical-align: bottom; padding-left:60%; margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button type='submit' id='terminer'>Terminer</button></div>
                     </div>";
		}
	
		$html .="</div>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function verifierSiResultatExisteAction() {
		$idDemande = $this->params()->fromPost('idDemande', 0);
		$demande = $this->getDemandeTable()->getDemande($idDemande);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($demande->appliquer) );
	}
	
	public function vueExamenAppliquerAction() {
		$this->getDateHelper();
		$idDemande = $this->params()->fromPost('idDemande', 0);
	
		$demande = $this->getDemandeTable()->getDemande($idDemande);
		
		$html  ="<table style='width: 95%;'>";
		$html .="<tr style='width: 95%;'>";
		$html .="<td style='width: 100%; vertical-align:top;'><a style='text-decoration:underline; font-size:12px;'>Libelle examen:</a><br><p style='font-weight:bold; font-size:17px;'> ". $this->getExamenTable()->getExamen($demande->idExamen)->libelleExamen ." </p></td>";
		$html .="</tr>";
		$html .="</table>";
	
		$html .="<table style='width: 95%; margin-bottom: 25px;'>";
		$html .="<tr style='width: 95%;'>";
		$html .="<td style='width: 90%; padding-top: 10px; padding-right:25px;'><a style='text-decoration:underline; font-size:13px;'>Motif:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $demande->noteDemande ." </p></td>";
		$html .="<td style='width: 10%;'> </td>";
		$html .= "</tr>";
		$html .="</table>";
		
		if($demande->appliquer == 1){
			$resultat = $this->getResultatExamenTable()->getResultatExamen($idDemande);
			$date = 'pas de date';
			if($this->dateHelper->convertDateTime($resultat->date_modification) == '00/00/0000 - 00:00:00'){
				$date = $this->dateHelper->convertDateTime($resultat->date_enregistrement);
			} else {
				$date = $this->dateHelper->convertDateTime($resultat->date_modification);
			}
			$html .= "<div id='titre_info_resultat_examen'>R&eacute;sultat de l'examen  <span style='position: absolute; right: 20px; font-size: 14px; font-weight: bold;'>". $date ." </span></div>
			          <div id='barre_resultat' ></div>";
			
			$html .="<table style='width: 100%; margin-top: 10px;'>";
			$html .="<tr style='width: 100%;'>";
			$html .="<td style='width: 50%; vertical-align:top;'><a style='text-decoration:underline; font-size:12px;'>Technique utilis&eacute;e:</a><br><p style='font-weight:bold; font-size:17px;'> ". $resultat->techniqueUtiliser ." </p></td>";
			$html .="<td id='visualisationImageResultats' style='width: 50%; vertical-align:top;'><img style='height: 50px;' src='../images_icons/jpg_file.png' title='Visualiser'/></td>";
			$html .="</tr>";
			$html .="</table>";
			
			$html .="<table style='width: 100%;'>";
			$html .="<tr style='width: 100%;'>";
			$html .="<td style='width: 50%; padding-top: 10px; padding-right:10px;'><a style='text-decoration:underline; font-size:13px;'>Note R&eacute;sultat:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $resultat->noteResultat ." </p></td>";
			$html .="<td style='width: 50%; padding-top: 10px; padding-right:10px;'><a style='text-decoration:underline; font-size:13px;'>Conclusion:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $resultat->conclusion ." </p></td>";
			$html .= "</tr>";
			$html .="</table>";
		}
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	public function appliquerExamenAction() {
		
	    $donnees = $this->getRequest()->getPost();
	    $user = $this->layout()->user;
	    $id_personne = $user['id_personne'];
	    
	    $this->getResultatExamenTable()->saveResultatsExamens($donnees, $id_personne);
	    $this->getDemandeTable()->demandeEffectuee($donnees->idDemande);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode () );
	}
	
	public function raffraichirListeExamensAction() {
		$id_cons = $this->params()->fromPost('id_cons');
		
		$html = $this->listeExamensAction($id_cons);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	public function raffraichirListeExamensBioAction() {
		$id_cons = $this->params()->fromPost('id_cons');
	
		$html = $this->listeExamensBiologiquesAction($id_cons);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	public function modifierExamenAction() {
		$idDemande = $this->params()->fromPost('idDemande');
		
		$demandeDem = $this->getDemandeTable()->getDemande($idDemande);
		
		$demande = $this->getResultatExamenTable()->getResultatExamen($idDemande);
		$html ="<script>
				   $('#technique_utilise').val('".$demande->techniqueUtiliser."');
				   $('#resultat').val('".$demande->noteResultat."');
				   $('#conclusion').val('".$demande->conclusion."');
				   $('#typeExamen_tmp').val(".$demandeDem->idExamen.");
				</script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	//POUR SAVOIR DE QUEL EXAMEN IL S'AGIT
	//POUR SAVOIR DE QUEL EXAMEN IL S'AGIT
	public function ajouterExamenAction() {
		$idDemande = $this->params()->fromPost('idDemande');
	
		$demandeDem = $this->getDemandeTable()->getDemande($idDemande);
	
		$html ="<script>
				   $('#typeExamen_tmp').val(".$demandeDem->idExamen.");
				</script>";
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	public function envoyerExamenAction() {
		$idDemande = $this->params()->fromPost('idDemande');
		$id_cons = $this->params()->fromPost('id_cons');
	
		$this->getResultatExamenTable()->examenEnvoyer($idDemande);
		$html = $this->listeExamensAction($id_cons);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	
	public function envoyerExamenBioAction() {
		$idDemande = $this->params()->fromPost('idDemande');
		$id_cons = $this->params()->fromPost('id_cons');
	
		$this->getResultatExamenTable()->examenEnvoyer($idDemande);
		$html = $this->listeExamensBiologiquesAction($id_cons);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	//POUR LA LISTE DES EXAMENS EFFECTUES PAR LE LABORANTIN (BIOLOGISTE)
	public function listeRechercheExamensEffectuesAjaxAction() {
		$output = $this->getDemandeTable()->getListeExamensEffectues();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeExamensEffectuesAction() {
		$this->layout()->setTemplate('layout/Hospitalisation');
		
		$formAppliquerExamen = new AppliquerExamenForm();
		
		return array(
				'form' => $formAppliquerExamen
		);
	}
	
	
	
	/* POUR LES EXAMENS MORPHOLOGIQUES ------ POUR LES EXAMENS MORPHOLOGIQUES ------ POUR LES EXAMENS MORPHOLOGIQUES */
	/* POUR LES EXAMENS MORPHOLOGIQUES ------ POUR LES EXAMENS MORPHOLOGIQUES ------ POUR LES EXAMENS MORPHOLOGIQUES */
	/* POUR LES EXAMENS MORPHOLOGIQUES ------ POUR LES EXAMENS MORPHOLOGIQUES ------ POUR LES EXAMENS MORPHOLOGIQUES */
	public function vueExamenAppliquerMorphoAction() {
		$this->getDateHelper();
		$idDemande = $this->params()->fromPost('idDemande', 0);
	
		$demande = $this->getDemandeTable()->getDemande($idDemande);
	
		$html  ="<table style='width: 95%;'>";
		$html .="<tr style='width: 95%;'>";
		$html .="<td style='width: 100%; vertical-align:top;'><a style='text-decoration:underline; font-size:12px;'>Libelle examen:</a><br><p style='font-weight:bold; font-size:17px;'> ". $this->getExamenTable()->getExamen($demande->idExamen)->libelleExamen ." </p></td>";
		$html .="</tr>";
		$html .="</table>";
	
		$html .="<table style='width: 95%; margin-bottom: 25px;'>";
		$html .="<tr style='width: 95%;'>";
		$html .="<td style='width: 90%; padding-top: 10px; padding-right:25px;'><a style='text-decoration:underline; font-size:13px;'>Motif:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $demande->noteDemande ." </p></td>";
		$html .="<td style='width: 10%;'> </td>";
		$html .= "</tr>";
		$html .="</table>";
	
		if($demande->appliquer == 1){
			$resultat = $this->getResultatExamenTable()->getResultatExamen($idDemande);
			$date = 'pas de date';
			if($this->dateHelper->convertDateTime($resultat->date_modification) == '00/00/0000 - 00:00:00'){
				$date = $this->dateHelper->convertDateTime($resultat->date_enregistrement);
			} else {
				$date = $this->dateHelper->convertDateTime($resultat->date_modification);
			}
			$html .= "<div id='titre_info_resultat_examen'>R&eacute;sultat de l'examen  <span style='position: absolute; right: 20px; font-size: 14px; font-weight: bold;'>". $date ." </span></div>
			          <div id='barre_resultat' ></div>";
				
			$html .="<table style='width: 100%; margin-top: 10px;'>";
			$html .="<tr style='width: 100%;'>";
			$html .="<td style='width: 100%; vertical-align:top;'><a style='text-decoration:underline; font-size:12px;'>Technique utilis&eacute;e:</a><br><p style='font-weight:bold; font-size:17px;'> ". $resultat->techniqueUtiliser ." </p></td>";
			$html .="</tr>";
			$html .="</table>";
				
			$html .="<table style='width: 100%;'>";
			$html .="<tr style='width: 100%;'>";
			$html .="<td style='width: 50%; padding-top: 10px; padding-right:10px;'><a style='text-decoration:underline; font-size:13px;'>Note R&eacute;sultat:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $resultat->noteResultat ." </p></td>";
			$html .="<td style='width: 50%; padding-top: 10px; padding-right:10px;'><a style='text-decoration:underline; font-size:13px;'>Conclusion:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $resultat->conclusion ." </p></td>";
			$html .= "</tr>";
			$html .="</table>";
		}
		
		$html .="<script> $('#typeExamen_tmp').val(".$demande->idExamen.");</script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ($html) );
	}
	
	public function listeExamensAction($id_cons, $idListe=null) {
	
		$liste_examens_demandes = $this->getDemandeTable()->getDemandesExamensMorphologiques($id_cons);
		$html = "";
		$this->getDateHelper();
			
		$html .="<table class='table table-bordered tab_list_mini'  style='margin-top:10px; margin-bottom:20px; margin-left:195px; width:80%;' id='listeDesExamens'>";
			
		$html .="<thead style='width: 100%;'>
				  <tr style='height:40px; width:100%; cursor:pointer; font-family: Times New Roman; font-weight: bold;'>
					<th style='width: 9%;'>Num&eacute;ro</th>
					<th style='width: 32%;'>Libelle examen</th>
					<th style='width: 40%;'>Note</th>
				    <th style='width: 13%;'>Options</th>
				    <th style='width: 6%;'>Etat</th>
				  </tr>
			     </thead>";
			
		$html .="<tbody style='width: 100%;'>";
		$cmp = 1;
		foreach ($liste_examens_demandes as $Liste){
			$html .="<tr style='width: 100%;' id='".$Liste['idDemande']."'>";
			$html .="<td style='width: 9%;'><div id='inform' style='margin-left: 25%; float:left; font-weight:bold; font-size:17px;'> " .$cmp++. ". </div></td>";
			$html .="<td style='width: 32%;'><div  style='color: green; font-family: Times New Roman; float:left; font-weight: bold; font-size:18px;'> " .$this->getExamenTable()->getExamen($Liste['idExamen'])->libelleExamen. " </div></td>";
			$html .="<td style='width: 40%;'><div  style='color: green; font-family: Times New Roman; float:left; font-weight: bold; font-size:18px;'> " .$Liste['noteDemande']. " </div></td>";
			$html .="<td style='width: 13%;'>
  					    <a href='javascript:vueExamenMorpho(".$Liste['idDemande'].") '>
  					       <img class='visualiser".$Liste['idDemande']."' style='margin-right: 9%; margin-left: 3%;' src='../images_icons/voird.png' alt='Constantes' title='d&eacute;tails' />
  					    </a>&nbsp";
	
			if($Liste['appliquer'] == 0) {
				
				if($idListe != 2){ //L'APPEL EST FAIT DANS LA LISTE 'RECHERCHE' POUR UNIQUEMENT LA VISUALISATION
				$html .="<a href='javascript:appliquerExamen(".$Liste['idDemande'].")'>
 				     	 <img class='modifier".$Liste['idDemande']."' style='margin-right: 16%;' src='../images_icons/aj.gif' alt='Constantes' title='Entrer les r&eacute;sultats'/>
 					     </a>";
				
				$html .="<a>
 					    	<img style='color: white; opacity: 0.09;' src='../images_icons/74biss.png' alt='Constantes' />
 					     </a>
 				         </td>";
					
				}
				
				$html .="<td style='width: 6%;'>
  					     <a>
  					        <img class='etat_non".$Liste['idDemande']."' style='margin-left: 25%;' src='../images_icons/non.png' alt='Constantes' title='examen non encore effectu&eacute;' />
  					     </a>
  					     </td>";
			}else {
				
				if($idListe != 2){ //L'APPEL EST FAIT DANS LA LISTE 'RECHERCHE' POUR UNIQUEMENT LA VISUALISATION
					
				$resultat = $this->getResultatExamenTable()->getResultatExamen($Liste['idDemande']);
	
				if($resultat->envoyer == 1) {
					
					$html .="<a>
 					  	     <img style='margin-right: 16%; color: white; opacity: 0.09;' src='../images_icons/pencil_16.png'/>
 					         </a>";
					
					if($Liste['responsable'] == 1) { /*Envoyer par le medecin*/
						$html .="<a>
 					    	<img class='envoyer".$Liste['idDemande']."' src='../images_icons/tick_16.png' title='examen valid&eacute; par le medecin'/>
 					     </a>
 				         </td>";
					} else
					{ /*Envoyer par le laborantin*/
						$html .="<a>
 					    	<img class='envoyer".$Liste['idDemande']."' src='../images_icons/tick_16.png' title='examen envoy&eacute;'/>
 					     </a>
 				         </td>";
					}
	
	
				} else {
					$html .="<a href='javascript:modifierExamen(".$Liste['idDemande'].")'>
 					    	<img class='modifier".$Liste['idDemande']."' style='margin-right: 16%;' src='../images_icons/pencil_16.png' alt='Constantes' title='modifier r&eacute;sultat'/>
 					     </a>";
					$html .="<a href='javascript:envoyer(".$Liste['idDemande'].")'>
 					    	<img class='envoyer".$Liste['idDemande']."' src='../images_icons/74biss.png' alt='Constantes' title='envoyer'/>
 					     </a>
 				         </td>";
				}
					
			
				}	
				
				$html .="<td style='width: 6%;'>
  					     <a>
  					        <img class='etat_oui".$Liste['idDemande']."' style='margin-left: 25%;' src='../images_icons/oui.png' alt='Constantes' title='examen d&eacute;ja effectu&eacute;' />
  					     </a>
  					     </td>";
			}
	
			$html .="</tr>";
	
			$html .="<script>
					  $('.visualiser".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.visualiser".$Liste['idDemande']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.visualiser".$Liste['idDemande']."').mouseleave(function(){
	                    var tooltips = $( '.visualiser".$Liste['idDemande']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /************************/
	                  /************************/
	                  /************************/
                      $('.modifier".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.modifier".$Liste['idDemande']." ' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
				      $('.modifier".$Liste['idDemande']." ').mouseleave(function(){
	                    var tooltips = $( '.modifier".$Liste['idDemande']." ' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.supprimer".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.supprimer".$Liste['idDemande']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.supprimer".$Liste['idDemande']."').mouseleave(function(){
	                    var tooltips = $( '.supprimer".$Liste['idDemande']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_oui".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_oui".$Liste['idDemande']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_oui".$Liste['idDemande']."').mouseleave(function(){
	                    var tooltips = $( '.etat_oui".$Liste['idDemande']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                  /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.etat_non".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.etat_non".$Liste['idDemande']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
	                  $('.etat_non".$Liste['idDemande']."').mouseleave(function(){
	                    var tooltips = $( '.etat_non".$Liste['idDemande']."' ).tooltip();
	                    tooltips.tooltip( 'close' );
	                  });
	                   /*************************/
	                  /*************************/
	                  /*************************/
	                  $('.envoyer".$Liste['idDemande']." ').mouseenter(function(){
	                    var tooltips = $( '.envoyer".$Liste['idDemande']."' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	                    tooltips.tooltip( 'open' );
	                  });
			        </script>";
		}
		$html .="</tbody>";
		$html .="</table>";
	
		$html .="<style>
				  #listeDataTable{
	                margin-left: 185px;
                  }
	
				  div .dataTables_paginate
                  {
				    margin-right: 20px;
                  }
	
				  #listeDesExamens tbody tr{
				    background: #fbfbfb;
				  }
	
				  #listeDesExamens tbody tr:hover{
				    background: #fefefe;
				  }
	
				 </style>";
		$html .="<script> listepatient (); listeDesSoins(); $('#Examen_id_cons').val('".$id_cons."'); </script>";
	
		return $html;
	
	}
	
	
	Public function listeExamensDemanderMorphoAction() {
		$this->getDateHelper();
		$id_personne = $this->params()->fromPost('id_personne',0);
		$id_cons = $this->params()->fromPost('id_cons',0);
		$terminer = $this->params()->fromPost('terminer',0);
		$examensBio = $this->params()->fromPost('examensBio',0);
		$idListe = $this->params()->fromPost('id',0); //POUR SAVOIR S'IL S'AGIT DE LA LISTE 'RECHERCHE' ou 'EN-COURS'
		
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);
		
		$demande = $this->getDemandeTable()->getDemandeWithIdcons($id_cons);
		
		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
	
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
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		$html .= "<div id='titre_info_deces'>Informations sur la demande d'examen </div>
		          <div id='barre'></div>";
		foreach ($demande as $donnees) {
			$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
			$html .= "<tr style='width: 80%;'>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Consultation:</a><br><p style='font-weight:bold; font-size:17px;'>" . $id_cons . "</p></td>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" .$this->dateHelper->convertDateTime($donnees['Datedemande']). "</p></td>";
			$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>M&eacute;decin demandeur:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $donnees['PrenomMedecin'] ." ".$donnees['NomMedecin'] . "</p></td>";
			$html .= "</tr>";
			$html .= "</table>";
		}
		
		$html .= "<div id='titre_info_deces'>Liste des examens demand&eacute;s </div>
		          <div id='barre'></div>
		
				 <div id='info_liste'>";
		if($examensBio == 1){
			$html .= $this->listeExamensBiologiquesAction($id_cons);
		}
		else /* POUR LES EXAMENS MORPHOLOGIQUES (Radiologie ... )*/
		{
			$html .= $this->listeExamensAction($id_cons, $idListe);
		}
		$html .="</div>";
		
		if($terminer == 0) {
			$html .="<div style='width: 100%; height: 100px;'>
	    		     <div style='margin-left:40px; color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:left;'>
                        <img  src='".$this->path."/images_icons/fleur1.jpg' />
                     </div>";
			$html .="<div class='block' id='thoughtbot' style='vertical-align: bottom; padding-left:60%; margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button type='submit' id='terminer'>Terminer</button></div>
                     </div>";
		}
		
		$html .="</div>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function listeDemandesExamensMorphoAjaxAction() {
		$output = $this->getDemandeTable()->getListeDemandesExamensMorpho();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeDemandesExamensMorphoAction() {
		$this->layout()->setTemplate('layout/Hospitalisation');
	
		$formAppliquerExamen = new AppliquerExamenForm();
	
		return array(
				'form' => $formAppliquerExamen
		);
	}
	
    public function listeExamensEffectuesMorphoAction() {
		$this->layout()->setTemplate('layout/Hospitalisation');

		//$output = $this->getDemandeTable()->getListeExamensMorphoEffectues();
		//var_dump($output); exit();
		$formAppliquerExamen = new AppliquerExamenForm();
		
		return array(
				'form' => $formAppliquerExamen
		);
	}
	
	//POUR LA LISTE DES EXAMENS DEJA EFFECTUES PAR LE RADIOLOGUE 
	public function listeRechercheExamensEffectuesMorphoAjaxAction() {
		$output = $this->getDemandeTable()->getListeExamensMorphoEffectues();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	
	
	//POUR LES VISITES PRE-ANESTHESIQUE
	//POUR LES VISITES PRE-ANESTHESIQUE
	//POUR LES VISITES PRE-ANESTHESIQUE
	public function listeDemandesVpaAjaxAction() {
		$output = $this->getDemandeTable()->getListeDemandesVpa();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeDemandesVpaAction() {
		$this->layout()->setTemplate('layout/Hospitalisation');
	}
	
	public function detailsDemandeVisiteAction() {

		$this->getDateHelper();
		$id_personne = $this->params()->fromPost('id_personne',0);
		$id_cons = $this->params()->fromPost('id_cons',0);
		$terminer = $this->params()->fromPost('terminer',0);
		$idVpa = $this->params()->fromPost('idVpa',0);
		
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);
		
		$demande = $this->getDemandeTable()->getDemandeVpaWidthIdcons($id_cons);
		
		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
	
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
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		$html .= "<div id='titre_info_deces'>Informations sur la demande de VPA </div>
		          <div id='barre'></div>";
		foreach ($demande as $donnees) {
			$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
			$html .= "<tr style='width: 80%;'>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Consultation:</a><br><p style='font-weight:bold; font-size:17px;'>" . $id_cons . "</p></td>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" .$this->dateHelper->convertDateTime($donnees['Datedemande']). "</p></td>";
			$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>M&eacute;decin demandeur:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $donnees['PrenomMedecin'] ." ".$donnees['NomMedecin'] . "</p></td>";
			$html .= "</tr>";
			$html .= "</table>";
			
			$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
			$html .= "<tr style='width: 80%;'>";
			$html .= "<td style='width: 25%; padding-top: 10px; padding-right:10px;'><a style='text-decoration:underline; font-size:13px;'>Diagnostic:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $donnees['DIAGNOSTIC'] ." </p></td>";
			$html .= "<td style='width: 25%; padding-top: 10px; padding-right:10px;'><a style='text-decoration:underline; font-size:13px;'>observation:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $donnees['OBSERVATION'] ." </p></td>";
			$html .= "<td style='width: 25%; padding-top: 10px; padding-right:10px;'><a style='text-decoration:underline; font-size:13px;'>Intervention pr&eacute;vue:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $donnees['INTERVENTION_PREVUE'] ." </p></td>";
			$html .= "</tr>";
			$html .= "</table>";
		}
		
		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
		$formVpa = new VpaForm();
		
		$user = $this->layout()->user;
		$id_personne = $user['id_personne'];
		
		$formRow = new FormRow();
		$formText = new FormText();
		$formTextArea = new FormTextarea();
		$formRadio = new FormRadio();
		$formHidden = new FormHidden();
		
		$html .= "<div id='titre_info_deces'>Entrez les r&eacute;sultats </div>
		          <div id='barre'></div>";
		$html .="<form  method='post' action='".$chemin."/hospitalisation/save-result-vpa'>";
		$html .= $formHidden($formVpa->get('idVpa'));
		$html .= $formHidden($formVpa->get('idPersonne'));
		
		$html .="<div style='width: 80%; margin-left: 195px;'>";
		$html .="<table id='form_patient_vpa' style='width: 100%; '>
					 <tr  style='width: 100%'>
					   <td  class='comment-form-patient'  style='width: 35%; '>". $formRow($formVpa->get('numero_vpa')).$formText($formVpa->get('numero_vpa'))."</td>
					   <td  class='comment-form-patient'  style='width: 35%; '>". $formRow($formVpa->get('type_intervention')).$formText($formVpa->get('type_intervention'))."</td>
				       <td  style='width: 10%; '> <span class='comment-form-patient'> <label style=''>Aptitude</label> </span> <span style='width: 10%;' class='comment-form-patient-radio'>".$formRadio($formVpa->get('aptitude'))."</span></td>
					   <td  class='comment-form-patient-label-im'>
				       		<label style='width: 48px; height: 48px; position: relative; right: 43px; top: 25px; z-index: 3;'> <img id='DeCoche' src='../images_icons/negatif.png'> </label>
				            <label style='width: 40px; height: 40px; position: relative; right: 40px; top: 20px; z-index: 3;'> <img id='Coche' src='../images_icons/tick-icon2.png'>   </label>
				       </td>
				       <td  style='width: 15%;'><a href='javascript:vider()'><img style=' margin-left: 45%;' src='../images_icons/118.png' title='vider tout'></a></td>
					 </tr>
					</table>";
		$html .="</div>";
		
		$html .="<div style='width: 100%; height: 100px;'>
	    		     <div style='margin-left:40px; color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:left;'>
                        <img  src='".$this->path."/images_icons/fleur1.jpg' />
                     </div>";
			
		$html .="<div class='block' id='thoughtbot' style='position: absolute; right: 40%; bottom: 70px; font-size: 18px; font-weight: bold;'><button type='submit' id='terminer'>Terminer</button></div>
                 </div>";
		$html .="<div class='block' id='thoughtbot' style='position: absolute; right: 49%; bottom: 70px; font-size: 18px; font-weight: bold;'><button type='submit' id='annuler'>Annuler</button></div>
                 </div>";
		
		$html .="</div>";
		
		$typeAnesthesie = $this->getDemandeTable()->listeDesTypeAnesthesie();
		
		$html .="<script> 
				  scriptTerminer(); 
				  $('#DeCoche').toggle(false);
				  $('#Coche').toggle(false);
				  $('#idVpa').val(".$idVpa.");
				  $('#idPersonne').val(".$id_personne.");
				  $('#form_patient_vpa input[name=aptitude]').click(function(){
				      var boutons = $('#form_patient_vpa input[name=aptitude]'); 
				      if( boutons[1].checked){ $('#Coche').toggle(true);  $('#DeCoche').toggle(false); }
				      if(!boutons[1].checked){ $('#Coche').toggle(false); $('#DeCoche').toggle(true);}
			      });
				  $('#form_patient_vpa input').attr('autocomplete', 'off');
				  $('#form_patient_vpa input').css({'font-size':'18px', 'color':'#065d10'});
				  
				  var myArrayTypeAnesthesie = [''];
				  var j = 0; 		
				 </script>";
		      
		      foreach ($typeAnesthesie as $liste){
		      	$html .="<script> myArrayTypeAnesthesie[j++]  = '" .$liste['libelle']. "'</script>";
		      }
		$html .="<script> 
				  $(function(){
                     $( '#type_intervention' ).autocomplete({
	                 source: myArrayTypeAnesthesie
	                 });
				  });
                 </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function saveResultVpaAction(){
		$resultatVpa = $this->getRequest()->getPost();
		$this->getResultatVpa()->saveResultat($resultatVpa);
		
		return $this->redirect()->toRoute('hospitalisation' , array('action' => 'liste-demandes-vpa'));
	}
	
	public function listeRechercheVpaAction() {
		$this->layout()->setTemplate('layout/Hospitalisation');
	}
	
	public function listeRechercheVpaAjaxAction() {
		$output = $this->getDemandeTable()->getListeRechercheVpa();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function detailsRechercheVisiteAction() {
	
		$this->getDateHelper();
		$id_personne = $this->params()->fromPost('id_personne',0);
		$id_cons = $this->params()->fromPost('id_cons',0);
		$terminer = $this->params()->fromPost('terminer',0);
		$idVpa = $this->params()->fromPost('idVpa',0);
	
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);
	
		$demande = $this->getDemandeTable()->getDemandeVpaWidthIdcons($id_cons);
	
		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
	
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
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		$html .= "<div id='titre_info_deces'>Informations sur la demande de VPA </div>
		          <div id='barre'></div>";
		foreach ($demande as $donnees) {
			$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
			$html .= "<tr style='width: 80%;'>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Consultation:</a><br><p style='font-weight:bold; font-size:17px;'>" . $id_cons . "</p></td>";
			$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" .$this->dateHelper->convertDateTime($donnees['Datedemande']). "</p></td>";
			$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>M&eacute;decin demandeur:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $donnees['PrenomMedecin'] ." ".$donnees['NomMedecin'] . "</p></td>";
			$html .= "</tr>";
			$html .= "</table>";
				
			$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
			$html .= "<tr style='width: 80%;'>";
			$html .= "<td style='width: 25%; padding-top: 10px; padding-right:10px;'><a style='text-decoration:underline; font-size:13px;'>Diagnostic:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $donnees['DIAGNOSTIC'] ." </p></td>";
			$html .= "<td style='width: 25%; padding-top: 10px; padding-right:10px;'><a style='text-decoration:underline; font-size:13px;'>Observation:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $donnees['OBSERVATION'] ." </p></td>";
			$html .= "<td style='width: 25%; padding-top: 10px; padding-right:10px;'><a style='text-decoration:underline; font-size:13px;'>Intervention pr&eacute;vue:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px; padding-left: 5px;'> ". $donnees['INTERVENTION_PREVUE'] ." </p></td>";
			$html .= "</tr>";
			$html .= "</table>";
		}
		$html .= "<div id='titre_info_deces'>Informations sur les r&eacute;sultats de la VPA </div>
		          <div id='barre'></div>";
		
		$resultatVpa = $this->getResultatVpa()->getResultatVpa($idVpa);
		
		$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
		
		$html .= "<tr style='width: 80%; font-family: time new romans'>";
		$html .= "<td style='width: 55%; height: 50px; '><span style='font-size:15px; font-family: Felix Titling;'>Num&eacute;ro VPA: </span> <span style='font-weight:bold; font-size:20px; color: #065d10;'>" .$resultatVpa->numeroVpa. "</span></td>";
		$html .= "<td rowspan='2' style='width: 2%; vertical-align: top;'> <div style='width: 4px; height: 110px; background: #ccc;'> </div> </td>";
		
		if($resultatVpa->aptitude == 1){
			$html .= "<td rowspan='2' style='width: 43%; height: 50px; '><span style='font-size:17px; font-family: Felix Titling;'>APTITUDE:  </span> <span style='font-weight:bold; font-size:25px; color: #065d10;'>  Oui <img src='../images_icons/coche.PNG' /></span></td>";
		}else {
			$html .= "<td rowspan='2' style='width: 43%; height: 50px; '><span style='font-size:17px; font-family: Felix Titling;'>APTITUDE:  </span> <span style='font-weight:bold; font-size:25px; color: #e91a1a;'>  Non <img src='../images_icons/decoche.PNG' /></span></td>";
		}
		
		$html .= "</tr>";
		
		$html .= "<tr style='width: 80%; font-family: time new romans; vertical-align: top;'>";
		$html .= "<td style='width: 50%; height: 50px; '><span style='font-size:15px; font-family: Felix Titling;'>Type d'anesth&eacute;sie: </span> <span style=' font-weight:bold; font-size:20px; color: #065d10;'>" . $resultatVpa->typeIntervention. "</span></td>";
		$html .= "</tr>";
		
		$html .= "</table>";
		
		$html .="<div style='width: 100%; height: 100px;'>
	    		     <div style='margin-left:40px; color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:left;'>
                        <img  src='".$this->path."/images_icons/fleur1.jpg' />
                     </div>";
			
		$html .="<div class='block' id='thoughtbot' style='position: absolute; right: 40%; bottom: 70px; font-size: 18px; font-weight: bold;'><button type='submit' id='terminer2'>Terminer</button></div>
                 </div>";
		
		$html .="<script>
				  scriptAnnulerVisualisation();
				 </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	/* INTERFACE DU MAJOR ------ INTERFACE DU MAJOR ------ INTERFACE DU MAJOR ------ INTERFACE DU MAJOR  */
	/* INTERFACE DU MAJOR ------ INTERFACE DU MAJOR ------ INTERFACE DU MAJOR ------ INTERFACE DU MAJOR  */
	/* INTERFACE DU MAJOR ------ INTERFACE DU MAJOR ------ INTERFACE DU MAJOR ------ INTERFACE DU MAJOR  */
	/* INTERFACE DU MAJOR ------ INTERFACE DU MAJOR ------ INTERFACE DU MAJOR ------ INTERFACE DU MAJOR  */
	/* INTERFACE DU MAJOR ------ INTERFACE DU MAJOR ------ INTERFACE DU MAJOR ------ INTERFACE DU MAJOR  */
	
	public function demandeHospitalisationAction()
	{
		$this->layout()->setTemplate('layout/Hospitalisation');
		
		$output = $this->getDemandeHospitalisationTable()->getListeDemandeHospitalisation();
		
		
		$formHospitalisation = new HospitaliserForm();
		$formHospitalisation->get('division')->setvalueOptions($this->getBatimentTable()->listeBatiments());
		
		if($this->getRequest()->isPost()) {
			$id_lit = $this->params()->fromPost('lit',0);
			$code_demande = $this->params()->fromPost('code_demande',0);
				
			$id_hosp = $this->getHospitalisationTable()->saveHospitalisation($code_demande);
			$this->getHospitalisationlitTable()->saveHospitalisationlit($id_hosp, $id_lit);
				
			$this->getDemandeHospitalisationTable()->validerDemandeHospitalisation($code_demande);
				
			$this->getLitTable()->updateLit($id_lit);
				
			return $this->redirect()->toRoute('hospitalisation' , array('action' => 'demande-hospitalisation'));
		}
		
		return array(
				'form' => $formHospitalisation
		);
	} 
	
	public function listeLibererPatientsAction()
	{
		$this->layout()->setTemplate('layout/Hospitalisation');
	}
	
	public function listeLibererPatientAjaxAction() {
		$output = $this->getHospitalisationTable()->getListePatientALiberer();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function liberationPatientAction() {
		$this->getDateHelper();
		$id_personne = $this->params()->fromPost('id_personne',0);
 		$id_cons = $this->params()->fromPost('id_cons',0);
 		$id_demande_hospi = $this->params()->fromPost('id_demande_hospi',0);
 		$id_hosp = $this->params()->fromPost('id_hosp',0);
	
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);
	
		$demande = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdcons($id_cons);
		
		
		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
	
		$html  = "<div style='width:100%;'>";
			
		//INFORMATIONS SUR LE PATIENT
		//INFORMATIONS SUR LE PATIENT
		//INFORMATIONS SUR LE PATIENT
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
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
	
		//INFORMATIONS SUR LA DEMANDE
		//INFORMATIONS SUR LA DEMANDE
		//INFORMATIONS SUR LA DEMANDE
		$html .= "<div id='titre_info_deces'>
				     <span id='titre_info_demande' style='margin-left: -10px; cursor:pointer;'>
				        <img src='/simens/public/img/light/plus.png' /> Infos sur la demande d'hospitalisation
				     </span>
				  </div>
		          <div id='barre'></div>";
		
		$html .= "<div id='info_demande'>";
		$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
		$html .= "<tr style='width: 80%;'>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Consultation:</a><br><p style='font-weight:bold; font-size:17px;'>" . $id_cons . "</p></td>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDateTime($demande['Datedemandehospi']) . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date fin pr&eacute;vue:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDate($demande['date_fin_prevue_hospi']) . "</p></td>";
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
		
		//INFORMATIONS SUR LA LIBERTAION DU PATIENT
		//INFORMATIONS SUR LA LIBERTAION DU PATIENT
		//INFORMATIONS SUR LA LIBERTAION DU PATIENT
		$hospitalisation = $this->getHospitalisationTable()->getHospitalisationWithCodedh($id_demande_hospi);
		$lit_hospitalisation = $this->getHospitalisationlitTable()->getHospitalisationlit($hospitalisation->id_hosp);
		$lit = $this->getLitTable()->getLit($lit_hospitalisation->id_materiel);
		$salle = $this->getSalleTable()->getSalle($lit->id_salle);
		$batiment = $this->getBatimentTable()->getBatiment($salle->id_batiment);
		
		$html .= "<div id='titre_info_deces'>
					   <span id='titre_info_hospitalisation' style='margin-left:-10px; cursor:pointer;'>
				          <img src='/simens/public/img/light/plus.png' /> Infos sur l'hospitalisation
				       </span>
					  </div>
		              <div id='barre'></div>";
		
		$html .= "<div id='info_hospitalisation'>";
		$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
		$html .= "<tr style='width: 80%;'>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date d&eacute;but:</a><br><p style='font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDateTime($hospitalisation->date_debut) . "</p></td>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Batiment:</a><br><p style=' font-weight:bold; font-size:17px;'>".$batiment->intitule."</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Salle:</a><br><p style=' font-weight:bold; font-size:17px;'>".$salle->numero_salle."</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Lit:</a><br><p style=' font-weight:bold; font-size:17px;'>".$lit->intitule."</p></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .= "</div>";
		
		
		//LIBERATION DU PATIENT
		//LIBERATION DU PATIENT
		//LIBERATION DU PATIENT
		$html .= "<div id='titre_info_deces'>
				    <span id='titre_info_liste' style='margin-left:-10px; cursor:pointer;'>
				      <img src='/simens/public/img/light/minus.png' /> lib&eacute;ration du patient
				    </span>
				  </div>
		          <div id='barre'></div>";
		    
		$unMedecin = $this->getPatientTable()->getInfoMedecin($hospitalisation->id_medecin);
		
		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
		$html .="<form id='Formulaire_Liberer_Patient_Major'  method='post' action='".$chemin."/hospitalisation/liberer-patient_major'>";
		
		$html .= "<div id='info_liste' class='listeSoinAAppliquer' >";
		$formLiberation = new LibererPatientMajorForm();
		$data = array('id_hosp' => $id_hosp, 'id_lit' => $lit_hospitalisation->id_materiel, 'note' => 'patient rÃ©tablit');
		$formLiberation->populateValues($data);
		
		$formRow = new FormRow();
		$formTextArea = new FormTextarea();
		$formHidden = new FormHidden();
		
		$html .=$formHidden($formLiberation->get('id_hosp'));
		$html .=$formHidden($formLiberation->get('id_lit'));
		$html .=$formHidden($formLiberation->get('liberer_lit'));
		$html .="<div style='width: 80%; margin-left: 195px;'>";
		$html .="<table id='form_patient' style='width: 100%; '>
		           <tr class='comment-form-patient' style='width: 100%'>
		              <td style='width: 30%; height: 50px; vertical-align: top; padding-top: 15px; padding-right: 15px;'><a style='text-decoration:underline; font-size:12px;'>M&eacute;decin demandeur:</a><br><p style='font-weight:bold; font-size:17px;'>".$unMedecin['PRENOM']." ".$unMedecin['NOM']."</p></td>
		              <td style='width: 25%; height: 50px; vertical-align: top; padding-top: 15px;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style='font-weight:bold; font-size:17px;'>".$this->dateHelper->convertDateTime($hospitalisation->date_fin)."</p></td>
		              <td style='width: 45%; height: 50px; vertical-align: top;'>
				          <div style='width: 90%;'>". $formRow($formLiberation->get('note')).$formTextArea($formLiberation->get('note'))."</div>
				      </td>
		           </tr>
				 </table>";
		$html .="</div>";
		$html .="</div>";

		$html .="<div style='width:100%; height: 110px;'>";
		$html .="<div style='width: 18%; height: 120px; float:left;'>";
		$html .="<table style='width: 100%;'>
				          <tr style='width: 100%;'>
				            <td style='height: 90px; padding-bottom: 20px;' >
					   		   <div style='margin-left:50px; color: white; opacity: 1; width:95px; height:70px;'>
                                 <img  src='/simens/public/images_icons/fleur1.jpg' />
                               </div>
				            </td>
				          </tr>
				 </table>";
		$html .="</div>";
		
		$html .= "<div style='width: 80%; height: 120px; float:left;'>";
		$html .="<table id='form_patient' style='width: 95%; height: 100px; margin-left: 30px; margin-right: 90px;'>
					 <tr class='comment-form-patient' style='width: 100%'>

				
					   <td  style='width: 30%;'>
					     <table style='width: 95%; float:right; height:2px;'>
				          <tr style='width: 100%;'>
				            <td style='height: 50px; padding-top: 10px;' >
					          <div class='block' id='thoughtbot' style='margin-left: 35%; vertical-align: bottom; font-size: 18px; font-weight: bold; float:left; padding-right: 10px; '><button type='submit' id='annulerLiberer'>Annuler</button></div>
                              <div class='block' id='thoughtbot' style='vertical-align: bottom; font-size: 18px; font-weight: bold; float:left;'><button type='submit' id='terminerLiberer'>Lib&eacute;rer</button></div>
				            </td>
				          </tr>
				         </table>
					   </td>
		
		
				 	 </tr>
				 </table>";
		$html .="</div>";
		$html .="</div>";
		$html .="</form>";

		
		$html .="<script>
				  var click_info_pat = 0;
				  $('#liberer_lit').val(0);
				  initAnimation();
				  animationPliantDepliant2();
				  animationPliantDepliant3();
		          animationPliantDepliant4();
				 </script>";
		

		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function libererPatientMajorAction()
	{
		$user = $this->layout()->user;
		$id_major = $user['id_personne']; //Le major ayant libéré le patient

		$id_hosp = $this->params()->fromPost('id_hosp',0);
		$note = $this->params()->fromPost('note',0);
		$id_lit = $this->params()->fromPost('id_lit',0);
		$liberer_lit = $this->params()->fromPost('liberer_lit',0);
		
		//Liberation du patient et du lit occupé
		$this->getHospitalisationlitTable()->updateHospitalisationlit($id_major, $id_hosp, $note);
		if($liberer_lit == 1) { $this->getLitTable()->libererLit($id_lit); }
		
		return $this->redirect()->toRoute('hospitalisation' , array('action' => 'liste-liberer-patients'));
	}
	
	public function infoPatientLibererAction() {
		$this->getDateHelper();
		$id_personne = $this->params()->fromPost('id_personne',0);
		$id_cons = $this->params()->fromPost('id_cons',0);
		$id_demande_hospi = $this->params()->fromPost('id_demande_hospi',0);
		$id_hosp = $this->params()->fromPost('id_hosp',0);
	
		$unPatient = $this->getPatientTable()->getInfoPatient($id_personne);
		$photo = $this->getPatientTable()->getPhoto($id_personne);
	
		$demande = $this->getDemandeHospitalisationTable()->getDemandeHospitalisationWithIdcons($id_cons);
	
	
		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
	
		$html  = "<div style='width:100%;'>";
			
		//INFORMATIONS SUR LE PATIENT
		//INFORMATIONS SUR LE PATIENT
		//INFORMATIONS SUR LE PATIENT
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
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->getPath()."/img/photos_patients/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
	
		//INFORMATIONS SUR LA DEMANDE
		//INFORMATIONS SUR LA DEMANDE
		//INFORMATIONS SUR LA DEMANDE
		$html .= "<div id='titre_info_deces'>
				     <span id='titre_info_demande' style='margin-left: -10px; cursor:pointer;'>
				        <img src='/simens/public/img/light/plus.png' /> Infos sur la demande d'hospitalisation
				     </span>
				  </div>
		          <div id='barre'></div>";
	
		$html .= "<div id='info_demande'>";
		$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
		$html .= "<tr style='width: 80%;'>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Consultation:</a><br><p style='font-weight:bold; font-size:17px;'>" . $id_cons . "</p></td>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDateTime($demande['Datedemandehospi']) . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date fin pr&eacute;vue:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDate($demande['date_fin_prevue_hospi']) . "</p></td>";
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
	
		//INFORMATIONS SUR LA LIBERTAION DU PATIENT
		//INFORMATIONS SUR LA LIBERTAION DU PATIENT
		//INFORMATIONS SUR LA LIBERTAION DU PATIENT
		$hospitalisation = $this->getHospitalisationTable()->getHospitalisationWithCodedh($id_demande_hospi);
		$lit_hospitalisation = $this->getHospitalisationlitTable()->getHospitalisationlit($hospitalisation->id_hosp);
		$lit = $this->getLitTable()->getLit($lit_hospitalisation->id_materiel);
		$salle = $this->getSalleTable()->getSalle($lit->id_salle);
		$batiment = $this->getBatimentTable()->getBatiment($salle->id_batiment);
	
		$html .= "<div id='titre_info_deces'>
					   <span id='titre_info_hospitalisation' style='margin-left:-10px; cursor:pointer;'>
				          <img src='/simens/public/img/light/plus.png' /> Infos sur l'hospitalisation
				       </span>
					  </div>
		              <div id='barre'></div>";
	
		$html .= "<div id='info_hospitalisation'>";
		$html .= "<table style='margin-top:10px; margin-left: 195px; width: 80%;'>";
		$html .= "<tr style='width: 80%;'>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date d&eacute;but:</a><br><p style='font-weight:bold; font-size:17px;'>" . $this->dateHelper->convertDateTime($hospitalisation->date_debut) . "</p></td>";
		$html .= "<td style='width: 25%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Batiment:</a><br><p style=' font-weight:bold; font-size:17px;'>".$batiment->intitule."</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Salle:</a><br><p style=' font-weight:bold; font-size:17px;'>".$salle->numero_salle."</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Lit:</a><br><p style=' font-weight:bold; font-size:17px;'>".$lit->intitule."</p></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .= "</div>";
	
	
		//LIBERATION DU PATIENT
		//LIBERATION DU PATIENT
		//LIBERATION DU PATIENT
		$html .= "<div id='titre_info_deces'>
				    <span id='titre_info_liberation_liste' style='margin-left:-10px; cursor:pointer;'>
				      <img src='/simens/public/img/light/minus.png' /> infos sur la lib&eacute;ration du patient
				    </span>
				  </div>
		          <div id='barre'></div>";
	
		$unMedecin = $this->getPatientTable()->getInfoMedecin($hospitalisation->id_medecin);
		$unMajor = $this->getPatientTable()->getInfoMedecin($lit_hospitalisation->id_major);
		
		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
	
		$html .="<div id='info_liste' class='listeSoinAAppliquer' >";

		$html .="<div style='width: 80%; margin-left: 195px;'>";
		$html .="<table id='form_patient' style='width: 100%; '>
		           <tr class='comment-form-patient' style='width: 100%'>
		              <td style='width: 30%; height: 50px; vertical-align: top; padding-top: 15px; padding-right: 15px;'><a style='text-decoration:underline; font-size:12px;'>M&eacute;decin demandeur:</a><br><p style='font-weight:bold; font-size:17px;'>".$unMedecin['PRENOM']." ".$unMedecin['NOM']."</p></td>
		              <td style='width: 25%; height: 50px; vertical-align: top; padding-top: 15px;'><a style='text-decoration:underline; font-size:12px;'>Date de la demande:</a><br><p style='font-weight:bold; font-size:17px;'>".$this->dateHelper->convertDateTime($hospitalisation->date_fin)."</p></td>
		              <td style='width: 45%; height: 50px; vertical-align: top;'>
				      </td>
		           </tr>
		              		
		           <tr class='comment-form-patient' style='width: 100%'>
		              <td style='width: 30%; height: 50px; vertical-align: top; padding-top: 15px; padding-right: 15px;'><a style='text-decoration:underline; font-size:12px;'>Lib&eacute;rer par: </a><br><p style='font-weight:bold; font-size:17px;'>".$unMajor['PRENOM']." ".$unMajor['NOM']."</p></td>
		              
		              <td style='width: 25%; height: 50px; vertical-align: top; padding-top: 15px;'>
		              	<a style='text-decoration:underline; font-size:12px;'>Date de la lib&eacute;ration: </a>
		              	<br><p style='font-weight:bold; font-size:17px;'>".$this->dateHelper->convertDateTime($lit_hospitalisation->date_liberation_lit)."</p>
		              </td>
		              
		              <td style='width: 45%; height: 50px; vertical-align: top; padding-top: 10px; padding-bottom: 0px; padding-right: 30px;'>
		              	<a style='text-decoration:underline; font-size:13px;'>Note:</a>
		              	<br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'> ".$lit_hospitalisation->note_liberation." </p>
				      </td>
		           </tr>
		              		
				 </table>";
		$html .="</div>";
		$html .="</div>";
	
		$html .="<div style='width:100%; height: 110px;'>";
		$html .="<div style='width: 18%; height: 120px; float:left;'>";
		$html .="<table style='width: 100%;'>
				          <tr style='width: 100%;'>
				            <td style='height: 90px; padding-bottom: 20px;' >
					   		   <div style='margin-left:50px; color: white; opacity: 1; width:95px; height:70px;'>
                                 <img  src='/simens/public/images_icons/fleur1.jpg' />
                               </div>
				            </td>
				          </tr>
				 </table>";
		$html .="</div>";
	
		$html .= "<div style='width: 80%; height: 120px; float:left;'>";
		$html .="<table id='form_patient' style='width: 95%; height: 100px; margin-left: 30px; margin-right: 90px;'>
					 <tr class='comment-form-patient' style='width: 100%'>
	
	
					   <td  style='width: 30%;'>
					     <table style='width: 95%; float:right; height:2px;'>
				          <tr style='width: 100%;'>
				            <td style='height: 50px; padding-top: 10px;' >
					          <div class='block' id='thoughtbot' style='margin-left: 35%; vertical-align: bottom; font-size: 18px; font-weight: bold; float:left; padding-right: 10px; '><button type='submit' id='terminerVisualisationLiberation'>Terminer</button></div>
				            </td>
				          </tr>
				         </table>
					   </td>
	
	
				 	 </tr>
				 </table>";
		$html .="</div>";
		$html .="</div>";
	
	
		$html .="<script>
				  var click_info_pat = 0;
				  $('#liberer_lit').val(0);
				  initAnimation();
				  animationPliantDepliant2();
				  animationPliantDepliant4();
		          animationPliantDepliant5();
				 </script>";
	
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function gestionDesLitsAction(){
		$this->layout()->setTemplate('layout/Hospitalisation');
		
		$formHospitalisation = new HospitaliserPourGestionLitsForm();
		
		if($this->getRequest()->isPost()) {
			$id_lit = $this->params()->fromPost('id_lit',0);
			$code_demande = $this->params()->fromPost('code_demande',0);
			
			$id_hosp = $this->getHospitalisationTable()->saveHospitalisation($code_demande);
			$this->getHospitalisationlitTable()->saveHospitalisationlit($id_hosp, $id_lit);
		
			$this->getDemandeHospitalisationTable()->validerDemandeHospitalisation($code_demande);
		
			$this->getLitTable()->updateLit($id_lit);
		
			return $this->redirect()->toRoute('hospitalisation' , array('action' => 'gestion-des-lits'));
		}
		
		return array(
				'form' => $formHospitalisation
		);
		
	}
	
	public function listeLitsAjaxAction()
	{
		$output = $this->getHospitalisationTable()->getListeLits();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function occuperLitAction()
	{
		$id_lit = $this->params()->fromPost('id_lit',0);
		$this->getLitTable()->occuperLit($id_lit); 
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( ) );
	}
	
	public function libererLitAction()
	{
		$id_lit = $this->params()->fromPost('id_lit',0);
		$this->getLitTable()->libererLit($id_lit);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( ) );
	}
	
	public function etatLitAction()
	{
		$id_lit = $this->params()->fromPost('id_lit',0);
		$lit = $this->getLitTable()->getLit($id_lit);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $lit->etat ) );
	}
	
	public function informationPatientAction()
	{
		$this->getDateHelper();
		
		$id_patient = $this->params()->fromPost('id_patient',0);
		
		$pat = $this->getPatientTable ();
		$unPatient = $pat->getInfoPatient ( $id_patient );
		$photo = $pat->getPhoto ( $id_patient );
		$date = $this->dateHelper->convertDate( $unPatient['DATE_NAISSANCE'] );
		
		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
		
		$html = "<div id='photo' style='float:left; margin-right:20px;'> 
				    <img  src='".$chemin."/img/photos_patients/" . $photo . "'  style='width:105px; height:105px;'>
				</div>";
		
		$html .= "<table>";
		$html .= "<tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['NOM'] . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['PRENOM'] . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['ADRESSE'] . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['TELEPHONE'] . "</p></td>";
		$html .= "</tr>";
		$html .= "</table>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function infoLitSalleBatimentAction()
	{
		$id_lit = $this->params()->fromPost('id_lit',0);
		
		$lit = $this->getLitTable()->getLit($id_lit);
		$salle = $this->getSalleTable()->getSalle($lit->id_salle);
		$batiment = $this->getBatimentTable()->getBatiment($salle->id_batiment);
		
		$html  ="<script> $('#lit').val('".$lit->intitule."').attr('disabled', 'true'); </script>";
		$html .="<script> $('#salle').val('".$salle->numero_salle."').attr('disabled', 'true'); </script>";
		$html .="<script> $('#division').val('".$batiment->intitule."').attr('disabled', 'true'); </script>";
		
		 
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
}
