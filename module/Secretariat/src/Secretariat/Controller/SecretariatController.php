<?php

namespace Secretariat\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Secretariat\Form\PatientForm;
use Secretariat\Model\Personne;
use Secretariat\View\Helper\DateHelper;
use Secretariat\View\Helper\DocumentPdf;
use Secretariat\View\Helper\DemandeAnalysePdf;
use Secretariat\View\Helper\AnalysesDemandeesPdf;

class SecretariatController extends AbstractActionController {
	protected $personneTable;
	protected $patientTable;
	protected $analyseTable;
	
	public function getPersonneTable() {
		if (! $this->personneTable) {
			$sm = $this->getServiceLocator ();
			$this->personneTable = $sm->get ( 'Secretariat\Model\PersonneTable' );
		}
		return $this->personneTable;
	}
	
	public function getPatientTable() {
		if (! $this->patientTable) {
			$sm = $this->getServiceLocator ();
			$this->patientTable = $sm->get ( 'Secretariat\Model\PatientTable' );
		}
		return $this->patientTable;
	}
	
	public function getAnalyseTable() {
		if (! $this->analyseTable) {
			$sm = $this->getServiceLocator ();
			$this->analyseTable = $sm->get ( 'Secretariat\Model\AnalyseTable' );
		}
		return $this->analyseTable;
	}

	
/*****************************************************************************************************************************/
/*****************************************************************************************************************************/
/*****************************************************************************************************************************/
	public function baseUrl(){
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		return $tabURI[0];
	}
	
	public function chemin(){
		return $this->getServiceLocator()->get('Request')->getBasePath();
	}
	
	public function ajouterPatientAction() {
		$this->layout ()->setTemplate ( 'layout/secretariat' );
		
		$form = new PatientForm();
		
		$form->get('nationalite_origine')->setvalueOptions($this->getPersonneTable()->listeDeTousLesPays());
		$form->get('nationalite_actuelle')->setvalueOptions($this->getPersonneTable()->listeDeTousLesPays());
		
		$request = $this->getRequest(); 
		if ($request->isPost()) {
			$personne =  new Personne();
			$form->setInputFilter($personne->getInputFilter());
			$form->setData($request->getPost());
			
			if ($form->isValid()) {
				
				$personne->exchangeArray($form->getData());
				$personne->photo = 'identite.jpg';
				
				$fileBase64 = substr($this->params ()->fromPost ('fichier_tmp'), 23);
				if($fileBase64){ $img = imagecreatefromstring(base64_decode($fileBase64)); } else { $img = false; }
				
				if ($img != false) {
					$photo = (new \DateTime ( 'now' )) ->format ( 'dmy_His' ).'.jpg';
					imagejpeg ( $img, $this->baseUrl().'public/img/photos_patients/' . $photo );
					$personne->photo = $photo;
				}
				
 				$idpersonne = $this->getPersonneTable() ->savePersonne($personne);
 				$idemploye = $this->layout()->user['idpersonne'];
 				$this->getPatientTable()->savePatient($idpersonne, $idemploye);
				if($personne->depistage == 1){ $this->getPatientTable()->saveDepistagePatient($idpersonne, $personne->ethnie, $idemploye); }
				
				return $this->redirect()->toRoute('secretariat' , array('action'=>'liste-patient') );
				
			}
			
		}

		
		$data = array('nationalite_origine' => 'SÃ©nÃ©gal', 'nationalite_actuelle' => 'SÃ©nÃ©gal');
		$form->populateValues($data);
		
		return new ViewModel ( array (
				'form' => $form
		) );
	}
	
	public function listePatientsAjaxAction() {
	
		$output = $this->getPatientTable ()->listePatientsAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
		
	}
	
	protected function nbJours($debut, $fin) {
	    //60 secondes X 60 minutes X 24 heures dans une journee
	    $nbSecondes = 60*60*24;
	
	    $debut_ts = strtotime($debut);
	    $fin_ts = strtotime($fin);
	    $diff = $fin_ts - $debut_ts;
	    return ($diff / $nbSecondes);
	}
	
	public function listePatientAction() {
		$this->layout ()->setTemplate ( 'layout/secretariat' );
		
		//$list = $this->getPatientTable()->getListeDepistagePatient();
		
		//var_dump($list); exit();
		
		return new ViewModel ( );
	}
	
	public function infosPatientAction() {
		
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
		
		$control = new DateHelper();
		
		$patient = $this->getPatientTable()->getPatient($id);
		$personne = $this->getPersonneTable()->getPersonne($id);
		$depistage = $this->getPatientTable()->getDepistagePatient($id);
		$date_naissance = null;
		if($personne->date_naissance){ $date_naissance = $control->convertDate( $personne->date_naissance ); }
		
		
		$depister = 0;
		$type = "Externe";
		$typage = "";
		if($depistage->current()){
		    $depister = 1;
		    if($depistage->current()['typepatient'] == 1){
		        $type = "Interne"; 
		        $typage = "(".$depistage->current()['typage'].")"; 
		    }
		    else{
		        if($depistage->current()['typage']){
		            if(!in_array($depistage->current()['typage'], array('AA','AB') )){
		                $typage = "(<span style='color: red;'>".$depistage->current()['typage']."</span>)" ;
		            }else{
		                $typage = "(".$depistage->current()['typage'].")" ;
		            }
		        }
		    }  
		}
		
		
		$html ="
				
	    <div style='width: 100%;'>

        <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:left; margin-right:40px; width:105px; height:105px;'/>";

		//Gestion des AGE
		if($personne->age){
		    $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$personne->age." ans </span></div>";
		}else{
		    $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		    $age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
		    if($age_jours < 31){
		        $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";		        
		    }else if($age_jours >= 31) {
		        
		        $nb_mois = (int)($age_jours/30);
		        $nb_jours = $age_jours - ($nb_mois*30);
		        
		        $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
		    }
		}

		
        $html .="<p>
         <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:right; margin-right:15px; width:95px; height:95px; color: white; opacity: 0.09;'/>
        </p>
             
        <table id='etat_civil'>
             <tr>
			   	<td style='width:27%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->prenom." </p></div>
			   	</td>

			   	<td style='width:35%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->lieu_naissance."  </p></div>
			   	</td>

			    <td style='width:38%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->telephone." </p></div>
			   	</td>

			 </tr>

			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nom." </p></div>
			   	</td>";

	   if($depister == 0){
	       $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; origine</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_origine." </p></div>
			   	</td>";
	   }else{
	       $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Ethnie</a><br><p style='font-weight: bold;font-size: 19px;'> ".$depistage->current()['ethnie']." </p></div>
			   	</td>";
	   }

	   $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Email</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->email." </p></div>
			   	</td>

			 </tr>

			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Sexe</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->sexe." </p></div>
			   	</td>

			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; actuelle</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_actuelle." </p></div>
			   	</td>

			   	<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->profession." </p></div>
			   	</td>

			 </tr>

			  <tr>
			   	<td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   	
			   		<div id='aa'><a style='text-decoration: underline; '>Date de naissance</a><br>
			   		  <p style='font-weight: bold;font-size: 19px;'>
			   		     ".$date_naissance."
			   		     
			   		  </p>
			   		</div>
			   		
			   	</td>

			    <td style='width: 195px; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->adresse." </p></div>
			   	</td>

			    <td  style='width: 195px; font-family: police1;font-size: 12px;'>
                   <div  id='aa'><a style='text-decoration: underline;'>Type</a><br><p style='font-weight: bold;font-size: 19px;'> ".$type." ".$typage."</p></div>
			  	</td>
			   	
			  </tr>
			  	 
           </table>

           <div id='barre'></div>

           <div style='color: white; opacity: 1; width:95px; height:40px; float:right'>
             <img  src='".$this->chemin()."/images_icons/fleur1.jpg' />
           </div>
	       <table id='numero' style=' padding-top:5px;  '>
           <tr>
             <td style='padding-top:3px; padding-left:25px; padding-right:5px; font-size: 12px; width: 80px; '> Code du patient: </td>
             <td style='font-weight: bold; '>".$id."</td>
                <td style='font-weight: bold;padding-left:20px;'>|</td>
             <td style='padding-top:5px; padding-left:10px; font-size: 12px; '> Date d'enregistrement: </td>
             <td style='font-weight: bold;'>". $control->convertDateTime( $patient->date_enregistrement ) ."</td>
           </tr>
           </table>

	    <div class='block' id='thoughtbot' style=' vertical-align: bottom; padding-left:60%; padding-top: 35px; font-size: 18px; font-weight: bold;'><button id='terminer'>Terminer</button></div>
             		
        <div style=' height: 80px; width: 100px; '> </div>
     
        </div> ";
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function modifierPatientAction() {

		$this->layout ()->setTemplate ( 'layout/secretariat' );
		
		$idpersonne = (int) $this->params()->fromRoute('val', 0);
		
		$form = new PatientForm();
		
		$form->get('nationalite_origine')->setvalueOptions($this->getPersonneTable()->listeDeTousLesPays());
		$form->get('nationalite_actuelle')->setvalueOptions($this->getPersonneTable()->listeDeTousLesPays());
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$personne =  new Personne();
			$form->setInputFilter($personne->getInputFilter());
			$form->setData($request->getPost());
				
			if ($form->isValid()) {
				
				$personne->exchangeArray($form->getData());
				
				$fileBase64 = substr($this->params ()->fromPost ('fichier_tmp'), 23);
				if($fileBase64){ $img = imagecreatefromstring(base64_decode($fileBase64)); } else { $img = false; }
		
				if ($img != false) {
					$photo = (new \DateTime ( 'now' )) ->format ( 'dmy_His' ).'.jpg';
					imagejpeg ( $img, $this->baseUrl().'public/img/photos_patients/' . $photo );
					$personne->photo = $photo;
				}
				$idpatient = $personne->idpersonne;
				$typepatient = $personne->typepatient; if(!$typepatient){ $typepatient = 0; }
				
				$this->getPersonneTable() ->savePersonne($personne);
				$idemploye = $this->layout()->user['idpersonne'];
				$this->getPatientTable()->updatePatient($idpatient, $idemploye);
				if($personne->depistage == 1){ $this->getPatientTable()->updateDepistagePatient($idpatient, $personne->ethnie, $typepatient, $idemploye); }
		
				return $this->redirect()->toRoute('secretariat' , array('action'=>'liste-patient') );
		
			}
			
		}

		$personne = $this->getPersonneTable()->getPersonne($idpersonne);
		$patient = $this->getPatientTable()->getPatient($idpersonne);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpersonne);
		$typepatient = 0;
		if($depistage->current()){ 
		    $personne->ethnie = $depistage->current()['ethnie']; 
		    $personne->typepatient = $depistage->current()['typepatient'];
		    $personne->depistage = 1;
		    $typepatient = $depistage->current()['typepatient'];
		}
		
		$form->bind($personne);
		
		$data = array();
		if($personne->date_naissance){ $data['date_naissance'] = (new DateHelper())->convertDate( $personne->date_naissance ); }
		$form->populateValues($data);
		
		return new ViewModel ( array (
		    'form' => $form,
		    'photo' => $personne->photo,
		    'typepatient' => $typepatient,
		    'depistage' => $depistage->current(),
		) );
		
	}
	
	
	public function demandesAnalysesAction() {
		
		$this->layout ()->setTemplate ( 'layout/secretariat' );
		
// 		$listeTypesAnalyses = $this->getPatientTable()->getListeDesTypesAnalyses();
// 		$myArrayActe = array();
// 		foreach ($listeTypesAnalyses as $listeTA){
// 		    $myArrayActe[$listeTA['idtype']] =  $listeTA['libelle'];
// 		}
// 		var_dump($myArrayActe); exit();
		
//	    $listeAnalysesDemandees = $this->getAnalyseTable()->getListeAnalysesDemandeesDP(20);
		
//		var_dump($listeAnalysesDemandees); exit();

		return new ViewModel ( array () );
	}
	
	public function listeRecherchePatientAjaxAction() {
	
		$output = $this->getPatientTable ()->listeRecherchePatientAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function popupVuePatientAction() {

		$idpersonne = ( int ) $this->params ()->fromPost ( 'id', 0 );

		//MISE A JOUR DE L'AGE DU PATIENT
		//$personne = $this->getPatientTable()->miseAJourAgePatient($id);
		//*******************************

		$personne = $this->getPersonneTable ()->getPersonne($idpersonne);
			
		$date = null;

		if($personne->date_naissance){ $date = (new DateHelper())->convertDate( $personne->date_naissance ); }
		
		$html = "<div style='float:left;' ><div id='photo' style='float:left; margin-right:20px; margin-bottom: 10px;'> <img  src='".$this->chemin()."/img/photos_patients/" . $personne->photo . "'  style='width:105px; height:105px;'></div>";
			
		//Gestion des AGE
		if($personne->age){
		    $html .= "<div style='margin-left:8px;'> <div style='text-decoration:none; font-size:14px; float:left; padding-right: 7px; '>Age:</div>  <div style='font-weight:bold; font-size:19px; font-family: time new romans; color: green; font-weight: bold;'>" . $personne->age . " ans</div></div></div>";
		    
		}else{
		    $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		    $age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
		    if($age_jours < 31){
		        $html .= "<div style='margin-left:8px;'> <div style='text-decoration:none; font-size:14px; float:left; padding-right: 7px; '>Age:</div>  <div style='font-weight:bold; font-size:19px; font-family: time new romans; color: green; font-weight: bold;'>" . $age_jours . " jours</div></div></div>";
		        
		    }else if($age_jours >= 31) {
		
		        $nb_mois = (int)($age_jours/30);
		        $nb_jours = $age_jours - ($nb_mois*30);
		
		        $html .= "<div style='margin-left:8px;'> <div style='text-decoration:none; font-size:14px; float:left; padding-right: 7px; '>Age:</div>  <div style='font-weight:bold; font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </div></div></div>";
		        
		    }
		}
		
			
		$html .= "<table>";
		
		$html .= "<tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $personne->nom . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $personne->prenom . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $personne->adresse . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $personne->telephone . "</p></td>";
		$html .= "</tr>";
		
		$html .= "</table>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	
	//Pour les listes de recherche **** pour les listes de recherche
	//Pour les listes de recherche **** pour les listes de recherche
	//Pour les listes de recherche **** pour les listes de recherche
	public function informationDuPatient($id) {
		
		$control = new DateHelper();
		$personne = $this->getPersonneTable()->getPersonne($id);
		$patient = $this->getPatientTable()->getPatient($id);
		$depistage = $this->getPatientTable()->getDepistagePatient($id);
		$date_naissance = null;
		if($personne->date_naissance){ $date_naissance = $control->convertDate( $personne->date_naissance ); }
		
		$depister = 0;
		$type = "Externe";
		$typage = "";
		if($depistage->current()){
		    $depister = 1;
		    if($depistage->current()['typepatient'] == 1){
		        $type = "Interne";
		        $typage = "(".$depistage->current()['typage'].")";
		    }
		    else{
		        if($depistage->current()['typage']){
		            if(!in_array($depistage->current()['typage'], array('AA','AB') )){
		                $typage = "(<span style='color: red;'>".$depistage->current()['typage']."</span>)" ;
		            }else{
		                $typage = "(".$depistage->current()['typage'].")" ;
		            }
		        }
		    }
		}
		
		$html ="
		
	    <div style='width: 100%;' align='center'>
		
	    <table style='width: 94%; height: 100px; margin-top: 2px;' >
			<tr style='width: 100%'>
				<td colspan='3' style='height: 25px; '> 
				
				  <a id='precedent' style='text-decoration: none; font-family: police2; width:50px; cursor: pointer;'>
	                <img src='".$this->chemin()."/images_icons/transfert_gauche.PNG' />
		            Pr&eacute;c&eacute;dent
		          </a>
	        		
				</td>
				 
			</tr>
			<tr style='width: 100%;' > 
				
			    <td style='width: 13%;' >
				  <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='width:105px; height:105px; margin-bottom: 10px;'/>";
		
		//Gestion des AGE
		if($personne->age){
		    $html .="<div style=' margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$personne->age." ans </span></div>";
		}else{
		    $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		    $age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
		    if($age_jours < 31){
		        $html .="<div style=' margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
		    }else if($age_jours >= 31) {
		        
		        $nb_mois = (int)($age_jours/30);
		        $nb_jours = $age_jours - ($nb_mois*30);
		        
		        $html .="<div style=' margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
		    }
		}
		
		
		$html .=" </td>

				<td style='width: 75%;' >

					 <!-- TABLEAU DES INFORMATIONS -->
				     <!-- TABLEAU DES INFORMATIONS -->
					 <!-- TABLEAU DES INFORMATIONS -->
				     <table id='etat_civil'>
                        <tr>
			   	           <td style='width:27%; font-family: police1;font-size: 12px;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->prenom." </p></div>
			   	           </td>
		
			   	           <td style='width:35%; font-family: police1;font-size: 12px;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->lieu_naissance."  </p></div>
			   	           </td>
		
			               <td style='width:38%; font-family: police1;font-size: 12px;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->telephone." </p></div>
			   	           </td>
			            </tr>
		
			            <tr>
			               <td style=' font-family: police1;font-size: 12px;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nom." </p></div>
			   	           </td>";
		
	             if($depister == 0){
	                 $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		            <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; origine</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_origine." </p></div>
			   	              </td>";
	             }else{
	             
	                 $html .="<td style=' font-family: police1;font-size: 12px;'>
			   	     	        <div id='aa'><a style='text-decoration: underline;'>Ethnie</a><br><p style='font-weight: bold;font-size: 19px;'> ".$depistage->current()['ethnie']." </p></div>
			   	              </td>";
	              }
	              
			   	  $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Email</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->email." </p></div>
			   	           </td>
		
			            </tr>
		
			            <tr>
			               <td style=' font-family: police1;font-size: 12px;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Sexe</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->sexe." </p></div>
			   	           </td>
		
			               <td style=' font-family: police1;font-size: 12px;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; actuelle</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_actuelle." </p></div>
			   	           </td>
		
			   	           <td style=' font-family: police1;font-size: 12px;'>
			   		           <div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->profession." </p></div>
			   	           </td>
		
			            </tr>
		
			            <tr>
			   	           <td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		           <div id='aa'><a style='text-decoration: underline; '>Date de naissance</a><br>
			   		              <p style='font-weight: bold;font-size: 19px;'>
			   		              ".$date_naissance."
			   		              </p>
			   		           </div>
			   	           </td>
			               <td style='font-family: police1;font-size: 12px;'>
			   		           <div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->adresse." </p></div>
			   	           </td>  
			   		               
			   		       <td style='padding-right: 25px; font-family: police1;font-size: 12px; '> 
			   		           <div id='aa'><a style='text-decoration: underline;'>Type</a><br><p style='font-weight: bold;font-size: 19px;'> ".$type." ".$typage."</p></div>
    	   		           	   <div style='width: 50px; height: 35px; float: right; margin-top: -40px;'><a href='javascript:diagnostic(".$id.");' > <img id='diagnostic_".$id."' style='float: right; cursor: pointer; ' src='../images_icons/detailsd.png' /> </a></div>
    	   		           </td>
			            </tr>
			   		  
                     </table> 
 					 <!-- FIN TABLEAU DES INFORMATIONS --> 
           			 <!-- FIN TABLEAU DES INFORMATIONS -->
			   		 <!-- FIN TABLEAU DES INFORMATIONS -->
				</td>
				
				<td style='width: 12%;' >
				  <span style='color: white; '>
                    <img src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='width:105px; height:105px; opacity: 0.09;'/>
                    <div style='margin-top: 20px; margin-right: 40px; font-size:17px; font-family: Iskoola Pota; color: green; float: right; font-style: italic; opacity: 1;'> N&deg;: ".$patient->idpersonne." </div>
                  </span>
				</td>
				
			</tr>
		</table>
						
		</div>";
		
		
		
		
		return $html;
		
	}
	
	public function demandesAnalysesVueAction() {
		
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );

		/*----------------------------------------------------*/
		$vuePatient = $this->informationDuPatient($id);
		
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
		$existeADA = 0; //Existance d'Analyses Demandées Aujourdhui
		$listeAnalysesDemandees = $this->getAnalyseTable()->getListeAnalysesDemandeesDP($id);
		if($listeAnalysesDemandees){ $existeADA = 1; }
		
		/*----------------------------------------------------*/
		$listeTypesAnalyses = $this->getPatientTable()->getListeDesTypesAnalyses();
		$tabTypesAnalyses = array(0 => '');
		foreach ($listeTypesAnalyses as $listeTA){
		    $tabTypesAnalyses[$listeTA['idtype']] =  $listeTA['libelle'];
		}
		
		/*----------------------------------------------------*/
		$tabListeAnalysesParType = array();
		for($i = 1 ; $i<=5 ; $i++){ // 5 est le nombre de type d'analyse
		    $tabListeAnalysesParType[$i] = $this->getListeAnalysesParType($i);
		}
		
		/*----------------------------------------------------*/
		$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		$diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($id, $aujourdhui);
		
		if($diagnostic_demande){
		    $text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
		    $vuePatient .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); modification(); </script>";
		}
		
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
		
		$donnees = array($vuePatient, $existeADA, $listeAnalysesDemandees, $tabTypesAnalyses, $tabListeAnalysesParType);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $donnees ) );
	}
	
	public function getListeAnalysesParType($type)
	{
	    $liste_select = "";
	    foreach($this->getPatientTable()->getListeDesAnalyses($type) as $listeAnalyses){
	        $liste_select.= "<option value=".$listeAnalyses['idanalyse'].">".$listeAnalyses['designation']."</option>";
	    }
	    return $liste_select;
	}
	
	public function getListeAnalysesAction()
	{
		$id = (int)$this->params()->fromPost ('id');
		$liste_select = "";
		foreach($this->getPatientTable()->getListeDesAnalyses($id) as $listeAnalyses){
			$liste_select.= "<option value=".$listeAnalyses['idanalyse'].">".$listeAnalyses['designation']."</option>";
		}
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $liste_select));
	}
	
	public function prixMill($prix) {
		$str="";
		$long =strlen($prix)-1;
	
		for($i = $long ; $i>=0; $i--)
		{
		$j=$long -$i;
		if( ($j%3 == 0) && $j!=0)
		{ $str= " ".$str;   }
		$p= $prix[$i];
	
		$str = $p.$str;
		}
	
		if(!$str){ $str = $prix; }
	
		return($str);
	}
	
	public function getTarifAnalyseAction()
	{
		$id = (int)$this->params()->fromPost ('id');

		$tarif = $this->getPatientTable()->getTarifAnalyse($id);
		$tarifString = $this->prixMill( $tarif );
		
		$html = array((int)$tarif, $tarifString);
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html));
	}
	
	public function envoyerDemandesAnalysesAction()
	{
		$analyses = $this->params()->fromPost ('analyses');
		$idemploye = $this->layout()->user['idpersonne'];
		$idpatient = $this->params()->fromPost ('idpatient');
		$diagnostic_demande = $this->params()->fromPost ('diagnostic_demande');
		$verifModifier = $this->params()->fromPost ('verifModifier');
		
		if($verifModifier == 0){
		    if($diagnostic_demande){ $this->getAnalyseTable()->addDignosticAnalyse($idemploye, $idpatient, $diagnostic_demande); }
		    $this->getAnalyseTable()->addDemandeAnalyse($analyses, $idemploye, $idpatient);
		}else{
		    if($diagnostic_demande){ 
		        $this->getAnalyseTable()->updateDignosticAnalyse($idemploye, $idpatient, $diagnostic_demande); 
		    }else{
		        $this->getAnalyseTable()->deleteDignosticAnalyse($idpatient);
		    }
		    $this->getAnalyseTable()->updateDemandeAnalyse($analyses, $idemploye, $idpatient);
		}
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ());
	}
	

	public function listeDemandesAjaxAction() 
	{
		$output = $this->getPatientTable ()->listeDemandesAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeDemandesAction() {
		$this->layout ()->setTemplate ( 'layout/secretariat' );

		//$output = $this->getPatientTable ()->getMontantTotalAnalyses(59);
		//foreach ($output as $out){
			//var_dump($output ); exit();
		//}
		
		
		
		return new ViewModel ( );
	}
	
	//Pour les listes **** pour les listes **** pour les listes
	//Pour les listes **** pour les listes **** pour les listes
	//Pour les listes **** pour les listes **** pour les listes
	public function informationPatientAction($id)
	{
		$control = new DateHelper();
		
		$patient = $this->getPatientTable()->getPatient($id);
		$personne = $this->getPersonneTable()->getPersonne($id);
		$depistage = $this->getPatientTable()->getDepistagePatient($id);
		$date_naissance = null;
		if($personne->date_naissance){ $date_naissance = $control->convertDate( $personne->date_naissance ); }
		
		$depister = 0;
		$type = "Externe";
		$typage = "";
		if($depistage->current()){
		    $depister = 1;
		    if($depistage->current()['typepatient'] == 1){
		        $type = "Interne";
		        $typage = "(".$depistage->current()['typage'].")";
		    }
		    else{
		        if($depistage->current()['typage']){
		            if(!in_array($depistage->current()['typage'], array('AA','AB') )){
		                $typage = "(<span style='color: red;'>".$depistage->current()['typage']."</span>)" ;
		            }else{
		                $typage = "(".$depistage->current()['typage'].")" ;
		            }
		        }
		    }
		}
		
		$html ="
		
	    <div style='width: 100%;'>
		
        <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:left; margin-right:40px; width:105px; height:105px;'/>";
		
		//Gestion des AGE
		if($personne->age){
		    $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$personne->age." ans </span></div>";
		}else{
		    $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		    $age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
		    if($age_jours < 31){
		        $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
		    }else if($age_jours >= 31) {
		        
		        $nb_mois = (int)($age_jours/30);
		        $nb_jours = $age_jours - ($nb_mois*30);
		        
		        $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
		    }
		}
   
        $html .="<p style='color: white; opacity: 0.09;'>
         <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:right; margin-right:15px; width:95px; height:95px;'/>
         <div style='position: absolute; top: 220px; right: 65px; font-size:17px; font-family: Iskoola Pota; color: green; float: right; font-style: italic; '> N&deg;: ".$patient->idpersonne." </div>
        </p>
        <table id='etat_civil'>
             <tr>
			   	<td style='width:27%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nomee</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->prenom." </p></div>
			   	</td>
		
			   	<td style='width:35%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->lieu_naissance."  </p></div>
			   	</td>
		
			    <td style='width:38%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->telephone." </p></div>
			   	</td>
		
			 </tr>
		
			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nom." </p></div>
			   	</td>";
		
		if($depister == 0){
		    $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		            <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; origine</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_origine." </p></div>
			   	              </td>";
		}else{
		
		    $html .="<td style=' font-family: police1;font-size: 12px;'>
			   	     	        <div id='aa'><a style='text-decoration: underline;'>Ethnie</a><br><p style='font-weight: bold;font-size: 19px;'> ".$depistage->current()['ethnie']." </p></div>
			   	              </td>";
		}
		
	   $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Email</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->email." </p></div>
			   	</td>
		
			 </tr>
		
			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Sexe</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->sexe." </p></div>
			   	</td>
		
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; actuelle</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_actuelle." </p></div>
			   	</td>
		
			   	<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->profession." </p></div>
			   	</td>
		
			 </tr>
		
			  <tr>
			   	<td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			 
			   		<div id='aa'><a style='text-decoration: underline; '>Date de naissance</a><br>
			   		  <p style='font-weight: bold;font-size: 19px;'>
			   		     ".$date_naissance."
			   	
			   		  </p>
			   		</div>
		
			   	</td>
		
			    <td style='width: 195px; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->adresse." </p></div>
			   	</td>
			   				
			   	<td style='padding-right: 25px; font-family: police1;font-size: 12px; '> 
			   	    <div id='aa'><a style='text-decoration: underline;'>Type</a><br><p style='font-weight: bold;font-size: 19px;'> ".$type." ".$typage." </p></div>
    	   		</td>			
			  </tr>
			  	 <tr>
		
			  	 </tr>
           </table>
           
         
		   <div style=' height: 10px; width: 100px; '> </div>
   
           </div> ";
		
		return $html;
	}
	
	public function listeDemandesAnalyseAction($id)
	{
		
		$listeDemandesAnalyses = $this->getPatientTable ()->getDemandesAnalyses($id);
		$listeAnalysesDemandees = $this->getPatientTable ()->getAnalysesDemandees($id);
		$i = 1;
		$iddemande = 0;
		$controle = new DateHelper();
		
		$html ='<table style="width: 100%;" >
 				    <tr style="width: 100%;" >
				        <td style="width: 35%; height: 350px;" >';
				        
				        
		      $html .="<table class='table table-bordered tab_list_mini' id='listeDemandesFiltre' >";
				        	
			  $html .="<thead style='width: 100%;'>
				         <tr style='height:25px; width:100%; cursor:pointer;'>
					        <th id='numerof' style='cursor: pointer;'>Inf</th>
					        <th id='datef' style='cursor: pointer;'>Date de la demande</th>
				            <th id='optionf' >Opt</th>
				         </tr>
			           </thead>";
				        
			  $html .="<tbody id='donnees' class='liste_patient' style='width: 100%;'>";
			  
			  foreach ($listeDemandesAnalyses as $listeDemandes){
			  	
			  	$aujourdhui = (new \DateTime() ) ->format('d/m/Y');
			  	$date = $controle->convertDate( $listeDemandes['date'] );
			  	if($date == $aujourdhui){ $date = "aujourd'hui  - ".$listeDemandes['time']; } else { $date = $date.' - '.$listeDemandes['time']; } 
			  	
			  	//Informations du secretaire
			  	$infosSecretaire = $this->getPatientTable ()->getInfosSecretaire($listeDemandes['idsecretaire']);
			  	
			  	$html .="<tr style='height: 25px; width:100%;'>
			  	           <td id='numerof'>
      					       <hass><img style='padding-left: 10px; cursor: pointer;' class='info_secretaire".$listeDemandes['iddemande']."' src='../images_icons/info_infirmier.png' title='envoyÃ© par ".$infosSecretaire['prenom'].' '.$infosSecretaire['nom']."' /></hass>
			  			   </td>
      					   <td>". $date ."</td>
			  	           <td>";

			  	if($i == 1){
			  		$iddemande = $listeDemandes['iddemande'];
			  		$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalyses(".$listeDemandes['iddemande'].") '>";
			  		$html .="<img style='padding-left: 3px; ' src='../images_icons/transfert_droite2.png' />"; $i = 0;
			  		$html .="</a>";
			  	}else {
			  		$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalyses(".$listeDemandes['iddemande'].") '>";
			  		$html .="<img style='padding-left: 3px; cursor: pointer;' class='' src='../images_icons/transfert_droite2.png' />";
			  		$html .="</a>";
			  	}
				
				$html .=" </td>
			  	        </tr>";
			  	
			  	$html .="<script> $('.info_secretaire".$listeDemandes['iddemande']."').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } }); </script>";
			  }
			  
		      $html .="</tbody>";
				        
				        
			  $html .="<tfoot class='foot_style foot_style_demande' style='width: 100%;'>
		                 <tr style='height:45px; width:100%; cursor:pointer;'>
					       <th id='numerof_' > <input type='hidden' /> </th>
				           <th id='datef_' > <input type='text' name='search_browser' value=' Date' class='search_init' /></th>
				           <th id='optionf_' > <input type='hidden' /></th>
				         </tr>
				        
		               </tfoot>";
				        
			  $html .="</table>";
				         
				
			  $html.="</td>";
				
				        
				        
		      $html .='<td style="width: 4%;" > <div style="width: 3px; height: 350px; background: #cccccc; margin-left: 15px;"></div> </td>
				        
				        
				        
				       <td id="liste_analyses_demandes" style="width: 61%; height: 50px;" >';
				        
		
		      $demande = $this->getPatientTable ()->getDemandeAnalysesAvecIddemande($iddemande);
		      $controle = new DateHelper();
		      $aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		      $date = $controle->convertDate( $demande['date'] );
		      if($date == $aujourdhui){ $date = "aujourd'hui  - ".$demande['time']; } else { $date = $date.' - '.$demande['time']; }
		      
		      $total = $this->getPatientTable()->getMontantTotalAnalyses($iddemande);

		      $diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($demande['idpatient'], $demande['date']);
		      
		      $html .="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span> | <span style='padding-right: 20px; padding-left: 20px; color: green; font-size: 17px; font-family: times new roman;'> Total pay&eacute;: ".$this->prixMill("$total")." frs</span>";
		      
		      if($diagnostic_demande){
		          $text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
		          $html .="<span> <a href='javascript:diagnostic(".$id.")' > <img id='diagnostic_".$id."'  style='padding-left: 3px;' src='../images_icons/detailsd.png' /> </a> </span>";
		          $html .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); </script>";
		      }
		      
		      $html .="<a href='javascript:imprimerAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a> </div>";
		      
		      $html .="<table class='table table-bordered tab_list_mini' id='listeAnalyseFiltre' >";
			
		      $html .="<thead style='width: 100%;'>
				         <tr style='height:25px; width:100%; cursor:pointer;'>
					        <th id='typeA' style='cursor: pointer;'>Type</th>
					        <th id='analyseA' style='cursor: pointer;'>Analyse</th>
				            <th id='optionA' style='font-size: 12px;' >Tarif(frs)</th>
				         </tr>
			           </thead>";
		
		      $html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";

		      foreach ($listeAnalysesDemandees as $listeAnalyses){
		      	
		      	$html .="<tr style='height:25px; width:100%; font-family: times new roman;'>
					       <td id='typeA' style='font-size: 14px;'> ".$listeAnalyses['Libelle']." </td>
					       <td id='analyseA' style='font-size: 14px;'> ".$listeAnalyses['Designation']." </td>
				           <td id='optionA' style='font-size: 17px;'> <div style='float: right;'> ".$this->prixMill($listeAnalyses['Tarif'])." </div>  </td>
				         </tr>";
		      
		      }
		               
		      $html .="</tbody>";
		      
		      
		      $html .="<tfoot class='foot_style foot_style_analyse' style='width: 100%;'>
		                 <tr style='height:45px; width:100%; cursor:pointer;'>
					       <th id='typeA_'> <input type='text' name='search_browser' value=' Type' class='search_init' /></th>
				           <th id='analyseA_'> <input type='text' name='search_browser' value=' Analyse' class='search_init' /></th>
				           <th id='optionA_'> <input type='hidden' /></th>
				         </tr>
		      
		               </tfoot>";
		
		      $html .="</table>
		      		
		      		
		      		
		               </td>
				       </tr>
				      </table>";
		
		
		$html .="<script> 
				 listeDemandesAnalyses(); listeAnalysesDemandes(); 
				</script>";
		
		return $html;
	}
	
	public function getInformationsPatientAction()
	{
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );

		$iddemande = $this->getPatientTable()->getDemandesAnalyses($id)->current()['iddemande'];
		
		$html = $this->informationPatientAction($id);
		
		$html2 = $this->listeDemandesAnalyseAction($id);
		
		$html3 = array($html, $html2, $iddemande);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html3 ) );
	}
	
	
	public function listeDemandesFiltreAjaxAction()
	{
		$output = $this->getPatientTable ()->listeDemandesFiltreAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function getListeAnalysesDemandeesAction()
	{
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
		
		$listeAnalysesDemandees = $this->getPatientTable ()->getListeAnalysesDemandees($iddemande);
		$demande = $this->getPatientTable ()->getDemandeAnalysesAvecIddemande($iddemande);
		
		$controle = new DateHelper();
		$aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		$date = $controle->convertDate( $demande['date'] );
		if($date == $aujourdhui){ $date = "aujourd'hui  - ".$demande['time']; } else { $date = $date.' - '.$demande['time']; }
		
		$total = $this->getPatientTable()->getMontantTotalAnalyses($iddemande);
		
		$diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($demande['idpatient'], $demande['date']);
		
		$html ="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span> | <span style='padding-right: 20px; padding-left: 20px; color: green; font-size: 17px; font-family: times new roman;'> Total pay&eacute;: ".$this->prixMill("$total")." frs</span>";
		
		if($diagnostic_demande){
		    $text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
		    $html .="<span> <a href='javascript:diagnostic(".$iddemande.")' > <img id='diagnostic_".$iddemande."'  style='padding-left: 3px;' src='../images_icons/detailsd.png' /> </a> </span>";
		    $html .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); </script>";
		}
		
		$html .="<a href='javascript:imprimerAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a> </div>";
		
		
		$html .="<table class='table table-bordered tab_list_mini' id='listeAnalyseFiltre' >";
			
		$html .="<thead style='width: 100%;'>
				         <tr style='height:25px; width:100%; cursor:pointer;'>
					        <th id='typeA' style='cursor: pointer;'>Type</th>
					        <th id='analyseA' style='cursor: pointer;'>Analyse</th>
				            <th id='optionA' style='font-size: 12px;' >Tarif(frs)</th>
				         </tr>
			     </thead>";
		
		$html .="<tbody id='liste_analyses_demandes' class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
		
		foreach ($listeAnalysesDemandees as $listeAnalyses){
			 
			$html .="<tr style='height:25px; width:100%; font-family: times new roman;'>
					       <td id='typeA' style='font-size: 14px;'> ".$listeAnalyses['Libelle']." </td>
					       <td id='analyseA' style='font-size: 14px;'> ".$listeAnalyses['Designation']." </td>
				           <td id='optionA' style='font-size: 17px;'> <div style='float: right;'> ".$this->prixMill($listeAnalyses['Tarif'])." </div> </td>
				         </tr>";
		
		}
		 
		$html .="</tbody>";
		
		
		$html .="<tfoot class='foot_style foot_style_analyse' style='width: 100%;'>
		                 <tr style='height:45px; width:100%; cursor:pointer;'>
					       <th id='typeA_'> <input type='text' name='search_browser' value=' Type' class='search_init' /></th>
				           <th id='analyseA_'> <input type='text' name='search_browser' value=' Analyse' class='search_init' /></th>
				           <th id='optionA_'> <input type='hidden' /></th>
				         </tr>
		
		         </tfoot>";
		
		$html .="</table>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function impressionDemandesAnalysesAction()
	{
		$service = $this->layout()->user['NomService'];
		$idpatient = $this->params()->fromPost( 'idpatient' );
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
		
		$formData = $this->getRequest ()->getPost ();
		$analyses = $formData['analyses'];
		$typesAnalyses = $formData['typesAnalyses'];
		$tarifs = $formData['tarifs'];
		
		$analysesTab = explode(',', $analyses);
		$typesAnalysesTab = explode(',', $typesAnalyses);
		$tarifsTab = explode(',', $tarifs);
		
		//******************************************************
		//******************************************************
		//*************** Création de l'imprimé pdf **************
		//******************************************************
		//******************************************************
		//Créer le document
		$DocPdf = new DocumentPdf();
		//Créer la page
		$page = new DemandeAnalysePdf();
		
		//Envoyer les données sur le patient
		$page->setDonneesPatient($personne);
		$page->setService($service);
		$page->setAnalyses($analysesTab);
		$page->setTypesAnalyses($typesAnalysesTab);
		$page->setTarifs($tarifsTab);
		$page->setDepistage($depistage);
		
		//Ajouter une note à la page
		$page->addNote();
		//Ajouter la page au document
		$DocPdf->addPage($page->getPage());
		//Afficher le document contenant la page
		$DocPdf->getDocument();
	}
	
	public function impressionAnalysesDemandeesAction()
	{
		$service = $this->layout()->user['NomService'];
		$iddemande = $this->params()->fromPost( 'iddemande' );
		
		$idpatient = $this->getPatientTable()->getDemandeAnalysesAvecIddemande($iddemande)['idpatient'];
		$personne = $this->getPersonneTable()->getPersonne($idpatient);

		$listeAnalysesDemandees = $this->getPatientTable()->getListeAnalysesDemandees($iddemande);
		
		//******************************************************
		//******************************************************
		//************** Création de l'imprimé pdf *************
		//******************************************************
		//******************************************************
		//Créer le document
		$DocPdf = new DocumentPdf();
		//Créer la page
		$page = new AnalysesDemandeesPdf();
		
		//Envoyer les données sur le patient
		$page->setDonneesPatient($personne);
		$page->setService($service);
		$page->setListeAnalysesDemandees($listeAnalysesDemandees);
		
		//Ajouter une note à la page
		$page->addNote();
		//Ajouter la page au document
		$DocPdf->addPage($page->getPage());
		//Afficher le document contenant la page
		$DocPdf->getDocument();
	}
	
	
	
}
