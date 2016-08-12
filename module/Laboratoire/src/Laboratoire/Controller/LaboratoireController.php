<?php 
namespace Laboratoire\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Secretariat\Model\Personne;
use Laboratoire\View\Helper\DateHelper;
use Laboratoire\View\Helper\DocumentPdf;
use Laboratoire\View\Helper\AnalysesDemandeesPdf;
use Laboratoire\View\Helper\ResultatsAnalysesDemandeesPdf;

class LaboratoireController extends AbstractActionController {
	protected $personneTable;
	protected $patientTable;
	protected $analyseTable;
	protected $resultatDemandeAnalyseTable;
	
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

	public function getResultatDemandeAnalyseTable() {
	    if (! $this->resultatDemandeAnalyseTable) {
	        $sm = $this->getServiceLocator ();
	        $this->resultatDemandeAnalyseTable = $sm->get ( 'Laboratoire\Model\ResultatDemandeAnalyseTable' );
	    }
	    return $this->resultatDemandeAnalyseTable;
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
	
    public function listeDemandesAnalysesAjaxAction() {
	
		$output = $this->getPatientTable ()->listeDemandesAnalyses();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
		
	}
	
	public function listeDemandesAnalysesAction() {
		$this->layout ()->setTemplate ( 'layout/laboratoire' );
		
// 		$control = new DateHelper();
// 		$listeAnalysesTypeGroup = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeGroupeDate(1);
// 		$tableauDonneesAnalyse = array();
		
// 		foreach ($listeAnalysesTypeGroup as $liste){
// 		    $tableauDonneesAnalyse[] = $control->convertDate($liste['date']);
// 		}

// 		var_dump($tableauDonneesAnalyse); exit();
		
		return new ViewModel ( );
	}
	
	protected function nbJours($debut, $fin) {
	    //60 secondes X 60 minutes X 24 heures dans une journee
	    $nbSecondes = 60*60*24;
	
	    $debut_ts = strtotime($debut);
	    $fin_ts = strtotime($fin);
	    $diff = $fin_ts - $debut_ts;
	    return ($diff / $nbSecondes);
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
        
        
        
        $html .="<p style='color: white; opacity: 0.09;'>
         <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:right; margin-right:15px; width:95px; height:95px;'/>
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
                   <div  id='aa'><a style='text-decoration: underline;'>Type   </a><br><p style='font-weight: bold;font-size: 19px;'> ".$type." ".$typage."</p></div>
			  	</td>			
			  </tr>
			  	 <tr>

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
                   <div  id='aa'><a style='text-decoration: underline;'>Type   </a><br><p style='font-weight: bold;font-size: 19px;'> ".$type." ".$typage." </p></div>
			  	</td>				
			  </tr>
			  	 <tr>
		
			  	 </tr>
           </table>
           
         
		   <div style=' height: 10px; width: 100px; '> </div>
   
           </div> ";
		
		return $html;
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

	
	public function listeDemandesAnalyseNonTraiter($id)
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
			  	
			  	//Informations sur le secretaire
			  	$infosSecretaire = $this->getPatientTable ()->getInfosSecretaire($listeDemandes['idsecretaire']);
			  	
			  	$html .="<tr style='height: 25px; width:100%;'>
			  	           <td id='numerof'>
      					       <hass><img style='padding-left: 10px; cursor: pointer;' class='info_secretaire".$listeDemandes['iddemande']."' src='../images_icons/info_infirmier.png' title='envoyé par: ".$infosSecretaire['prenom'].' '.$infosSecretaire['nom']."' /></hass>
			  			   </td>
      					   <td id='datef' class='dateAffichee  dateAffichee_".$listeDemandes['iddemande']."' >". $date ."</td>
			  	           <td id='optionf'>";

			  	if($i == 1){
			  		$iddemande = $listeDemandes['iddemande'];
			  		$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalyses(".$listeDemandes['iddemande'].")' >";
			  		$html .="<img style='padding-left: 3px; ' src='../images_icons/transfert_droite2.png' /></a>"; $i = 0;
			  	}else {
			  		$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalyses(".$listeDemandes['iddemande'].")' >";
			  		$html .="<img style='padding-left: 3px; cursor: pointer;' class='' src='../images_icons/transfert_droite2.png' /></a>";
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
				
				        
				        
 		      $html .="<td style='width: 4%;' > <div style='width: 3px; height: 350px; background: #cccccc; margin-left: 15px;'></div> </td>";
				        
 			
			  $html .="<td id='liste_analyses_demandes' style='width: 61%; height: 50px;' >";
				        
		
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
		      
 		      $html .="<a href='javascript:resultatsDesAnalyses(".$iddemande.");'> <img style='padding-left: 3px; ' src='../images_icons/resultat2.png' title='Resultats' /> </a> <a href='javascript:imprimerResultatsAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a> </div>";
		      
		      
		      $html .="<table class='table table-bordered tab_list_mini' id='listeAnalyseFiltre' >";
			
		      $html .="<thead style='width: 100%;'>
				         <tr style='height:25px; width:100%; cursor:pointer;'>
					        <th id='typeA' style='cursor: pointer;'>Type</th>
					        <th id='analyseA' style='cursor: pointer;'>Analyse</th>
				            <th id='optionA' style='font-size: 12px;' >Options</th>
				         </tr>
			           </thead>";
		
		      $html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";

 		      foreach ($listeAnalysesDemandees as $listeAnalyses){
		      	
 		        $existeResultat = $this->getResultatDemandeAnalyseTable()->getResultatDemandeAnalyse($listeAnalyses['iddemande']);
 		          
		      	$html .="<tr style='height:25px; width:100%; font-family: times new roman;'>
					       <td id='typeA' style='font-size: 14px;'> ".$listeAnalyses['Libelle']." </td>
					       <td id='analyseA' style='font-size: 14px;'> ".$listeAnalyses['Designation']." </td> ";
				$html .="<td id='optionA' style='font-size: 17px;'>";
				$html .="<div> 
				         <a href='javascript:resultatAnalyse(".$listeAnalyses['iddemande'].");' style='cursor: pointer;'> 
				             <img class='titre_resultat_".$listeAnalyses['iddemande']."'  src='../images_icons/resultat1.png' title='r&eacute;sultat' /> 
				         </a>";

				$html .="<a class='resultat_existe".$listeAnalyses['iddemande']."' style='margin-left: 10px;'>";
				if($existeResultat){ $html .= "<img  src='../images_icons/tick_16.png' />"; }
				$html .="</a>";
				
				$html .="</div>
				         </td>
				         </tr>";
				
		      	$html .="<script> $('.titre_resultat_".$listeAnalyses['iddemande']."').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } }); </script>";
		      	 
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
		      		
		      		
		      		
   	          $html .="</td>
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
		
		$html2 = $this->listeDemandesAnalyseNonTraiter($id);
		
		$html3 = array($html, $html2,  $iddemande);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html3 ) );
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
		
		$html .="<a href='javascript:resultatsDesAnalyses(".$iddemande.");'> <img style='padding-left: 3px; ' src='../images_icons/resultat2.png' title='Resultats' /> </a> <a href='javascript:imprimerResultatsAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a> </div>";
		
		
		$html .="<table class='table table-bordered tab_list_mini' id='listeAnalyseFiltre' >";
			
		$html .="<thead style='width: 100%;'>
				         <tr style='height:25px; width:100%; cursor:pointer;'>
					        <th id='typeA' style='cursor: pointer;'>Type</th>
					        <th id='analyseA' style='cursor: pointer;'>Analyse</th>
				            <th id='optionA' style='font-size: 12px;' >Options</th>
				         </tr>
			     </thead>";
		
	
		$html .="<tbody id='liste_analyses_demandes' class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
	
		foreach ($listeAnalysesDemandees as $listeAnalyses){
	
		    $existeResultat = $this->getResultatDemandeAnalyseTable()->getResultatDemandeAnalyse($listeAnalyses['iddemande']);
		    
			$html .="<tr style='height:25px; width:100%; font-family: times new roman;'>
					       <td id='typeA' style='font-size: 14px;'> ".$listeAnalyses['Libelle']." </td>
					       <td id='analyseA' style='font-size: 14px;'> ".$listeAnalyses['Designation']." </td> ";
			$html .="<td id='optionA' style='font-size: 17px;'>";
			$html .="<div>
				     <a href='javascript:resultatAnalyse(".$listeAnalyses['iddemande'].");' style='cursor: pointer;'> 
				         <img class='titre_resultat_".$listeAnalyses['iddemande']."' src='../images_icons/resultat1.png' title='r&eacute;sultat' /> 
				     </a>";
			
			$html .="<a class='resultat_existe".$listeAnalyses['iddemande']."' style='margin-left: 10px;'>";
			if($existeResultat){ $html .= "<img src='../images_icons/tick_16.png'  />"; }
			$html .="</a>";
			
			$html .="</div>
				     </td>
				     </tr>";
			
			
			$html .="<script> $('.titre_resultat_".$listeAnalyses['iddemande']."').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } }); </script>";
	
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
	
	//****** =========== RECUPERER L'ANALYSE =========== *******
	//****** =========== RECUPERER L'ANALYSE =========== *******
	//****** =========== RECUPERER L'ANALYSE =========== *******
	protected function getResultatsNfs($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursNfs($iddemande);
	    $html ="";
	    if($resultat){
	        for($i = 1 ; $i<=23 ; $i++){
	            $html .= "<script> $('#champ".$i."').val('".$resultat['champ'.$i]."'); </script>";
	        }
	        $html .= "<script> $('#type_materiel_nfs').val('".$resultat['type_materiel']."'); </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsGsrhGroupage($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursGsrhGroupage($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script> 
	            $('#groupe').val('".$resultat['groupe']."'); 
	            $('#rhesus').val('".$resultat['rhesus']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsRechercheAntigene($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursRechercheAntigene($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#antigene_d_faible').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTestCombsDirect($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsDirect($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#test_combs_direct').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTestCombsIndirect($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsIndirect($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#test_combs_indirect').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTestDemmel($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestDemmel($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#test_demmel').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTestCompatibilite($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCompatibilite($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#test_compatibilite').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsVitesseSedimentation($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCompatibilite($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#vitesse_sedimentation').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTauxReticulocyte($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTauxReticulocyte($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#taux_reticulocyte').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsGoutteEpaisse($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursGoutteEpaisse($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#goutte_epaisse').val('".$resultat['goutte_epaisse']."');
	            if('".$resultat['goutte_epaisse']."' == 'Positif'){ $('#goutte_epaisse_positif').toggle(true); $('#densite_parasitaire').val('".$resultat['densite_parasitaire']."'); }
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTpInr($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTpInr($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#temps_quick_temoin').val('".$resultat['temps_quick_temoin']."');
	            $('#temps_quick_patient').val('".$resultat['temps_quick_patient']."');
	            $('#taux_prothrombine_patient').val('".$resultat['taux_prothrombine_patient']."');
	            $('#inr_patient').val('".$resultat['inr_patient']."');
	        </script>";
	    }
	    return $html;
	}
	
	
	protected function getResultatsTca($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTca($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#tca_patient').val('".$resultat['tca_patient']."');
	            $('#temoin_patient').val('".$resultat['temoin_patient']."');
	        </script>";
	    }
	    return $html;
	}
	
	
	protected function getResultatsFibrinemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursFibrinemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#fibrinemie').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTempsSaignement($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTempsSaignement($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#temps_saignement').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}

	protected function getResultatsGlycemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursGlycemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#glycemie_1').val('".$resultat['glycemie_1']."');
     	        $('#glycemie_2').val('".$resultat['glycemie_2']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCreatininemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCreatininemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#creatininemie').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsAzotemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursAzotemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#uree_sanguine').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsAcideUrique($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursAcideUrique($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#acide_urique').val('".$resultat['valeur']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCholesterolTotal($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolTotal($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#cholesterol_total_1').val('".$resultat['cholesterol_total_1']."');
                $('#cholesterol_total_2').val('".$resultat['cholesterol_total_2']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTriglycerides($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTriglycerides($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#triglycerides_1').val('".$resultat['triglycerides_1']."');
	    	    $('#triglycerides_2').val('".$resultat['triglycerides_2']."');
	    	</script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCholesterolHDL($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolHDL($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#cholesterol_HDL_1').val('".$resultat['cholesterol_HDL_1']."');
	    	    $('#cholesterol_HDL_2').val('".$resultat['cholesterol_HDL_2']."');
	    	</script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCholesterolLDL($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolLDL($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#cholesterol_LDL_1').val('".$resultat['cholesterol_LDL_1']."');
	    	    $('#cholesterol_LDL_2').val('".$resultat['cholesterol_LDL_2']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsIonogramme($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursIonogramme($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#sodium_sanguin').val('".$resultat['sodium_sanguin']."');
	    	    $('#potassium_sanguin').val('".$resultat['potassium_sanguin']."');
	    	    $('#chlore_sanguin').val('".$resultat['chlore_sanguin']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCalcemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCalcemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#calcemie').val('".$resultat['valeur']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsMagnesemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursMagnesemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#magnesemie').val('".$resultat['valeur']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsPhosphoremie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursPhosphoremie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#phosphoremie').val('".$resultat['valeur']."');
	    	 </script>";
	    }
	    return $html;
	}

	protected function getResultatsTgoAsat($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTgoAsat($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#tgo_asat').val('".$resultat['valeur']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTgpAlat($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTgpAlat($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#tgp_alat').val('".$resultat['valeur']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsPhosphatageAlcaline($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursPhosphatageAlcaline($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#phosphatage_alcaline').val('".$resultat['valeur']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsGamaGtYgt($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursGamaGtYgt($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#gama_gt').val('".$resultat['valeur']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsFerSerique($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursFerSerique($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#fer_serique_ug').val('".$resultat['valeur_ug']."');
	    	    $('#fer_serique_umol').val('".$resultat['valeur_umol']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTypageHemoglobine($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTypageHemoglobine($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#type_materiel_typage_hemoglobine').val('".$resultat['type_materiel']."');
	    	    $('#typage_hemoglobine').val('".$resultat['valeur']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	//***************** ========== RECUPERER UNE ANALYSE ========== ***************
	//***************** ========== RECUPERER UNE ANALYSE ========== ***************
	//***************** ========== RECUPERER UNE ANALYSE ========== ***************
	public function recupererAnalyseAction(){
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );

		$analyse = $this->getAnalyseTable()->getAnalysesDemandees($iddemande);
		
		$html  = "<table class='designEnTeteAnalyse' style='width: 100%;' > 
		           <input type='hidden' id='idanalyse' value='".$analyse['Idanalyse']."'>
				   <tr style='width: 100%;' > <td class='enTete'>". $analyse['Libelle'] ."</td> </tr>
				   <tr> <th class='enTitre'> <div>". $analyse['Designation'] ."</div> </th> </tr>";
		
		if($analyse['Idanalyse'] ==  1){ $html .= $this->nfs_1(); $html .= $this->getResultatsNfs($iddemande); } 
		if($analyse['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2(); $html .= $this->getResultatsGsrhGroupage($iddemande); }
		if($analyse['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3(); $html .= $this->getResultatsRechercheAntigene($iddemande); }
		if($analyse['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4(); $html .= $this->getResultatsTestCombsDirect($iddemande); }
		if($analyse['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5(); $html .= $this->getResultatsTestCombsIndirect($iddemande); }
		if($analyse['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6(); $html .= $this->getResultatsTestCompatibilite($iddemande); }
		if($analyse['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); $html .= $this->getResultatsVitesseSedimentation($iddemande); }
		if($analyse['Idanalyse'] ==  8){ $html .= $this->test_demmel_8(); $html .= $this->getResultatsTestDemmel($iddemande); }
		if($analyse['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9(); $html .= $this->getResultatsTauxReticulocyte($iddemande); }
		if($analyse['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10(); $html .= $this->getResultatsGoutteEpaisse($iddemande); }
		
		   if($analyse['Idanalyse'] == 11){ $html .= $this->adenogramme_11();                 }
		   if($analyse['Idanalyse'] == 12){ $html .= $this->medulodramme_12();                }
		   if($analyse['Idanalyse'] == 13){ $html .= $this->cytochimie_myeloperoxydase_13();  }
		
		if($analyse['Idanalyse'] == 14){ $html .= $this->tp_inr_14(); $html .= $this->getResultatsTpInr($iddemande); }
		if($analyse['Idanalyse'] == 15){ $html .= $this->tca_15(); $html .= $this->getResultatsTca($iddemande); }
		if($analyse['Idanalyse'] == 16){ $html .= $this->fibrinemie_16(); $html .= $this->getResultatsFibrinemie($iddemande); }
		if($analyse['Idanalyse'] == 17){ $html .= $this->temps_saignement_17(); $html .= $this->getResultatsTempsSaignement($iddemande); }
		
		   if($analyse['Idanalyse'] == 18){ $html .= $this->facteur_viii_18(); }
		   if($analyse['Idanalyse'] == 19){ $html .= $this->facteur_ix_19();   }
		   if($analyse['Idanalyse'] == 20){ $html .= $this->dimeres_20();      }
		
		if($analyse['Idanalyse'] == 21){ $html .= $this->glycemie_21(); $html .= $this->getResultatsGlycemie($iddemande); }
		if($analyse['Idanalyse'] == 22){ $html .= $this->creatininemie_22(); $html .= $this->getResultatsCreatininemie($iddemande); }
		if($analyse['Idanalyse'] == 23){ $html .= $this->azotemie_23(); $html .= $this->getResultatsAzotemie($iddemande); }
		if($analyse['Idanalyse'] == 24){ $html .= $this->acide_urique_24(); $html .= $this->getResultatsAcideUrique($iddemande); }
		if($analyse['Idanalyse'] == 25){ $html .= $this->cholesterol_total_25(); $html .= $this->getResultatsCholesterolTotal($iddemande); }
		if($analyse['Idanalyse'] == 26){ $html .= $this->triglycerides_26(); $html .= $this->getResultatsTriglycerides($iddemande); }
		if($analyse['Idanalyse'] == 27){ $html .= $this->cholesterol_HDL_27(); $html .= $this->getResultatsCholesterolHDL($iddemande); }
		if($analyse['Idanalyse'] == 28){ $html .= $this->cholesterol_LDL_28(); $html .= $this->getResultatsCholesterolLDL($iddemande); }
		if($analyse['Idanalyse'] == 29){ 
		    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL TOTAL </div> </th> </tr>";
		    $html .= $this->cholesterol_total_25(); $html .= $this->getResultatsCholesterolTotal($iddemande);
 		    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL HDL </div> </th> </tr>";
 		    $html .= $this->cholesterol_HDL_27(); $html .= $this->getResultatsCholesterolHDL($iddemande);
 		    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL LDL </div> </th> </tr>";
 		    $html .= $this->cholesterol_LDL_28(); $html .= $this->getResultatsCholesterolLDL($iddemande); 
 		    $html .="<tr> <th class='enTitre'> <div> TRIGLYCERIDES </div> </th> </tr>";
 		    $html .= $this->triglycerides_26(); $html .= $this->getResultatsTriglycerides($iddemande);
		}
		if($analyse['Idanalyse'] == 30){ $html .= $this->lipides_totaux_30();   }
		if($analyse['Idanalyse'] == 31){ $html .= $this->ionogramme_31(); $html .= $this->getResultatsIonogramme($iddemande); }
		if($analyse['Idanalyse'] == 32){ $html .= $this->calcemie_32(); $html .= $this->getResultatsCalcemie($iddemande); }
		if($analyse['Idanalyse'] == 33){ $html .= $this->magnesemie_33(); $html .= $this->getResultatsMagnesemie($iddemande); }
		if($analyse['Idanalyse'] == 34){ $html .= $this->phosphoremie_34(); $html .= $this->getResultatsPhosphoremie($iddemande); }
		if($analyse['Idanalyse'] == 35){ $html .= $this->tgo_asat_35(); $html .= $this->getResultatsTgoAsat($iddemande); }
		if($analyse['Idanalyse'] == 36){ $html .= $this->tgp_alat_36(); $html .= $this->getResultatsTgpAlat($iddemande); }
		
		     if($analyse['Idanalyse'] == 37){
		         $html .="<tr> <th class='enTitre'> <div> ASAT </div> </th> </tr>";
		         $html .= $this->tgo_asat_35();     
		         $html .="<tr> <th class='enTitre'> <div> ALAT </div> </th> </tr>";
		         $html .= $this->tgp_alat_36();
		     }
		
		if($analyse['Idanalyse'] == 38){ $html .= $this->phosphatage_alcaline_38(); $html .= $this->getResultatsPhosphatageAlcaline($iddemande); }
		if($analyse['Idanalyse'] == 39){ $html .= $this->gama_gt_ygt_39(); $html .= $this->getResultatsGamaGtYgt($iddemande); }
		if($analyse['Idanalyse'] == 40){ $html .= $this->fer_serique_40(); $html .= $this->getResultatsFerSerique($iddemande); }
		if($analyse['Idanalyse'] == 41){ $html .= $this->ferritinine_41(); }
		if($analyse['Idanalyse'] == 42){ $html .= $this->bilirubine_totale_directe_42(); }
		if($analyse['Idanalyse'] == 43){ $html .= $this->hemoglobine_glyquee_hbac_43(); }
		if($analyse['Idanalyse'] == 44){ $html .= $this->electrophorese_hemoglobine_44(); }
		if($analyse['Idanalyse'] == 45){ $html .= $this->electrophorese_preteines_45(); }
		if($analyse['Idanalyse'] == 46){ $html .= $this->albuminemie_46(); }
		if($analyse['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47(); }
		if($analyse['Idanalyse'] == 48){ $html .= $this->protidemie_48(); }
		if($analyse['Idanalyse'] == 49){ $html .= $this->proteinurie_49(); }
		if($analyse['Idanalyse'] == 50){ $html .= $this->hlm_compte_daddis_50(); }
		if($analyse['Idanalyse'] == 51){ $html .= $this->beta_hcg_plasmatique_51(); }
		if($analyse['Idanalyse'] == 52){ $html .= $this->psa_52(); }
		if($analyse['Idanalyse'] == 53){ $html .= $this->crp_53(); }
		if($analyse['Idanalyse'] == 54){ $html .= $this->facteurs_rhumatoides_54(); }
		if($analyse['Idanalyse'] == 55){ $html .= $this->rf_waaler_rose_55(); }
		if($analyse['Idanalyse'] == 56){ $html .= $this->toxoplasmose_56(); }
		if($analyse['Idanalyse'] == 57){ $html .= $this->rubeole_57(); }
		if($analyse['Idanalyse'] == 58){ $html .= $this->culot_urinaire_58(); }
		if($analyse['Idanalyse'] == 59){ $html .= $this->serologie_chlamydiae_59(); }
		if($analyse['Idanalyse'] == 60){ $html .= $this->serologie_syphilitique_60(); }
		if($analyse['Idanalyse'] == 61){ $html .= $this->aslo_61(); }
		if($analyse['Idanalyse'] == 62){ $html .= $this->widal_62(); }
		if($analyse['Idanalyse'] == 63){ $html .= $this->ag_hbs_63(); }
		if($analyse['Idanalyse'] == 64){ $html .= $this->hiv_64(); }
		if($analyse['Idanalyse'] == 65){ $html .= $this->pv_65(); }
		if($analyse['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
		if($analyse['Idanalyse'] == 67){ $html .= $this->pus_67(); }
		if($analyse['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68();  $html .= $this->getResultatsTypageHemoglobine($iddemande); }
		
		
		$html .= "</table>";
		
		$donnees = array($analyse['Idanalyse'], $html);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $donnees ) );
	}
	
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	public function enregistrerResultatAction()
	{
	    $idanalyse = ( int ) $this->params ()->fromPost ( 'idanalyse', 0 );
	    $iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
	    $tab = $this->params ()->fromPost ( 'tab' );
	    $idemploye = $this->layout()->user['idpersonne'];
	    
	    $donneesExiste = 0;
	    
	    if($idanalyse == 1){
	        $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	        $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursNfs($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 2){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGsrhGroupage($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 3){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursRechercheAntigene($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 4){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestCombsDirect($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 5){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestCombsIndirect($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 6){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestCompatibilite($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 7){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursVitesseSedimentation($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 8){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestDemmel($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 9){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTauxReticulocyte($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 10){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGoutteEpaisse($tab, $iddemande);
	    }
	    
	    
	    else
	        if($idanalyse == 14){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTpInr($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 15){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTca($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 16){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFibrinemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 17){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTempsSaignement($tab, $iddemande);
	    }
	    
	    
	    else
	        if($idanalyse == 21){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGlycemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 22){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCreatininemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 23){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAzotemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 24){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAcideUrique($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 25){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolTotal($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 26){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTriglycerides($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 27){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolHDL($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 28){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolLDL($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 29){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeurs_Total_HDL_LDL_Triglycerides($tab, $iddemande);
	    }
	    
	    
	    else
	        if($idanalyse == 31){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursIonogramme($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 32){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCalcemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 33){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursMagnesemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 34){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPhosphoremie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 35){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTgoAsat($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 36){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTgpAlat($tab, $iddemande);
	    }
	    
	    else
	        if($idanalyse == 38){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPhosphatageAlcaline($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 39){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGamaGtYgt($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 40){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFerSerique($tab, $iddemande);
	    }
	    
	    
	    
	    
	    else
	        if($idanalyse == 68){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTypageHemoglobine($tab, $iddemande);
	        }
	    
	    $donnees = array($iddemande, $donneesExiste);
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $donnees ) );
	}
	
	
	//****** ========== RECUPERER LES ANALYSES DE LA DEMANDE ========== ******
	//****** ========== RECUPERER LES ANALYSES DE LA DEMANDE ========== ******
	//****** ========== RECUPERER LES ANALYSES DE LA DEMANDE ========== ******
	public function recupererLesAnalysesDeLaDemandeAction()
	{
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
	
		$listeAnalyse = $this->getAnalyseTable()->getListeAnalysesDemandees($iddemande);
		
		$html = "";
		$libelle = "";
		$tabAnalyses = array();
		$tabDemandes = array();
		
		foreach ($listeAnalyse as $liste) {

			$html .="<table class='designEnTeteAnalyse' style='width: 100%;' >";
			
			if($libelle != $liste['Libelle']){
			    $html .="<tr style='width: 100%;' > <td class='enTete'>". $liste['Libelle'] ."</td> </tr>";
			    $libelle = $liste['Libelle'];
			}
			
			$html .="<tr> <th class='enTitre'> <div>". $liste['Designation'] ."</div> </th> </tr>";
			
			if($liste['Idanalyse'] ==  1){ $html .= $this->nfs_1(); $html .= $this->getResultatsNfs($liste['iddemande']); }
			if($liste['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2(); $html .= $this->getResultatsGsrhGroupage($liste['iddemande']); }
			if($liste['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3(); $html .= $this->getResultatsRechercheAntigene($liste['iddemande']); }
			if($liste['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4(); $html .= $this->getResultatsTestCombsDirect($liste['iddemande']); }
			if($liste['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5(); $html .= $this->getResultatsTestCombsIndirect($liste['iddemande']); }
			if($liste['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6(); $html .= $this->getResultatsTestCompatibilite($liste['iddemande']); }
			if($liste['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); $html .= $this->getResultatsVitesseSedimentation($liste['iddemande']); }
			if($liste['Idanalyse'] ==  8){ $html .= $this->test_demmel_8(); $html .= $this->getResultatsTestDemmel($liste['iddemande']); }
			if($liste['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9(); $html .= $this->getResultatsTauxReticulocyte($liste['iddemande']); }
			if($liste['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10(); $html .= $this->getResultatsGoutteEpaisse($liste['iddemande']); }
			
			   if($liste['Idanalyse'] == 11){ $html .= $this->adenogramme_11();                 }
			   if($liste['Idanalyse'] == 12){ $html .= $this->medulodramme_12();                }
			   if($liste['Idanalyse'] == 13){ $html .= $this->cytochimie_myeloperoxydase_13();  }
			
			if($liste['Idanalyse'] == 14){ $html .= $this->tp_inr_14(); $html .= $this->getResultatsTpInr($liste['iddemande']); }
			if($liste['Idanalyse'] == 15){ $html .= $this->tca_15(); $html .= $this->getResultatsTca($liste['iddemande']); }
			if($liste['Idanalyse'] == 16){ $html .= $this->fibrinemie_16(); $html .= $this->getResultatsFibrinemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 17){ $html .= $this->temps_saignement_17(); $html .= $this->getResultatsTempsSaignement($liste['iddemande']); }
			
			   if($liste['Idanalyse'] == 18){ $html .= $this->facteur_viii_18(); }
			   if($liste['Idanalyse'] == 19){ $html .= $this->facteur_ix_19();   }
			   if($liste['Idanalyse'] == 20){ $html .= $this->dimeres_20();      }
			
			if($liste['Idanalyse'] == 21){ $html .= $this->glycemie_21(); $html .= $this->getResultatsGlycemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 22){ $html .= $this->creatininemie_22(); $html .= $this->getResultatsCreatininemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 23){ $html .= $this->azotemie_23(); $html .= $this->getResultatsAzotemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 24){ $html .= $this->acide_urique_24(); $html .= $this->getResultatsAcideUrique($liste['iddemande']); }
			if($liste['Idanalyse'] == 25){ $html .= $this->cholesterol_total_25(); $html .= $this->getResultatsCholesterolTotal($liste['iddemande']); }
			if($liste['Idanalyse'] == 26){ $html .= $this->triglycerides_26(); $html .= $this->getResultatsTriglycerides($liste['iddemande']); }
			if($liste['Idanalyse'] == 27){ $html .= $this->cholesterol_HDL_27(); $html .= $this->getResultatsCholesterolHDL($liste['iddemande']); }
			if($liste['Idanalyse'] == 28){ $html .= $this->cholesterol_LDL_28(); $html .= $this->getResultatsCholesterolLDL($liste['iddemande']); }
			if($liste['Idanalyse'] == 29){
			    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL TOTAL </div> </th> </tr>";
			    $html .= $this->cholesterol_total_25(); $html .= $this->getResultatsCholesterolTotal($liste['iddemande']);
			    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL HDL </div> </th> </tr>";
			    $html .= $this->cholesterol_HDL_27(); $html .= $this->getResultatsCholesterolHDL($liste['iddemande']);
			    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL LDL </div> </th> </tr>";
			    $html .= $this->cholesterol_LDL_28(); $html .= $this->getResultatsCholesterolLDL($liste['iddemande']);
			    $html .="<tr> <th class='enTitre'> <div> TRIGLYCERIDES </div> </th> </tr>";
			    $html .= $this->triglycerides_26(); $html .= $this->getResultatsTriglycerides($liste['iddemande']);
			}
			  if($liste['Idanalyse'] == 30){ $html .= $this->lipides_totaux_30(); }
			if($liste['Idanalyse'] == 31){ $html .= $this->ionogramme_31(); $html .= $this->getResultatsIonogramme($liste['iddemande']); }
			if($liste['Idanalyse'] == 32){ $html .= $this->calcemie_32(); $html .= $this->getResultatsCalcemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 33){ $html .= $this->magnesemie_33(); $html .= $this->getResultatsMagnesemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 34){ $html .= $this->phosphoremie_34(); $html .= $this->getResultatsPhosphoremie($liste['iddemande']); }
			if($liste['Idanalyse'] == 35){ $html .= $this->tgo_asat_35(); $html .= $this->getResultatsTgoAsat($liste['iddemande']); }
			if($liste['Idanalyse'] == 36){ $html .= $this->tgp_alat_36(); $html .= $this->getResultatsTgpAlat($liste['iddemande']); }
			
			   if($liste['Idanalyse'] == 37){
			      $html .="<tr> <th class='enTitre'> <div> ASAT </div> </th> </tr>";
			      $html .= $this->tgo_asat_35();
			      $html .="<tr> <th class='enTitre'> <div> ALAT </div> </th> </tr>";
			      $html .= $this->tgp_alat_36();
			   }
			
			if($liste['Idanalyse'] == 38){ $html .= $this->phosphatage_alcaline_38(); $html .= $this->getResultatsPhosphatageAlcaline($liste['iddemande']); }
			if($liste['Idanalyse'] == 39){ $html .= $this->gama_gt_ygt_39(); $html .= $this->getResultatsGamaGtYgt($liste['iddemande']); }
			if($liste['Idanalyse'] == 40){ $html .= $this->fer_serique_40(); $html .= $this->getResultatsFerSerique($liste['iddemande']); }
			if($liste['Idanalyse'] == 41){ $html .= $this->ferritinine_41(); }
			if($liste['Idanalyse'] == 42){ $html .= $this->bilirubine_totale_directe_42(); }
			if($liste['Idanalyse'] == 43){ $html .= $this->hemoglobine_glyquee_hbac_43(); }
			if($liste['Idanalyse'] == 44){ $html .= $this->electrophorese_hemoglobine_44(); }
			if($liste['Idanalyse'] == 45){ $html .= $this->electrophorese_preteines_45(); }	
			if($liste['Idanalyse'] == 46){ $html .= $this->albuminemie_46(); }
			if($liste['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47(); }
			if($liste['Idanalyse'] == 48){ $html .= $this->protidemie_48(); }
			if($liste['Idanalyse'] == 49){ $html .= $this->proteinurie_49(); }
			if($liste['Idanalyse'] == 50){ $html .= $this->hlm_compte_daddis_50(); }
			if($liste['Idanalyse'] == 51){ $html .= $this->beta_hcg_plasmatique_51(); }
			if($liste['Idanalyse'] == 52){ $html .= $this->psa_52(); }
			if($liste['Idanalyse'] == 53){ $html .= $this->crp_53(); }
			if($liste['Idanalyse'] == 54){ $html .= $this->facteurs_rhumatoides_54(); }
			if($liste['Idanalyse'] == 55){ $html .= $this->rf_waaler_rose_55(); }
			if($liste['Idanalyse'] == 56){ $html .= $this->toxoplasmose_56(); }
			if($liste['Idanalyse'] == 57){ $html .= $this->rubeole_57(); }
			if($liste['Idanalyse'] == 58){ $html .= $this->culot_urinaire_58(); }
			if($liste['Idanalyse'] == 59){ $html .= $this->serologie_chlamydiae_59(); }
			if($liste['Idanalyse'] == 60){ $html .= $this->serologie_syphilitique_60(); }
			if($liste['Idanalyse'] == 61){ $html .= $this->aslo_61(); }
			if($liste['Idanalyse'] == 62){ $html .= $this->widal_62(); }
			if($liste['Idanalyse'] == 63){ $html .= $this->ag_hbs_63(); }
			if($liste['Idanalyse'] == 64){ $html .= $this->hiv_64(); }
			if($liste['Idanalyse'] == 65){ $html .= $this->pv_65(); }
			if($liste['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
			if($liste['Idanalyse'] == 67){ $html .= $this->pus_67(); }
			if($liste['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68();  $html .= $this->getResultatsTypageHemoglobine($liste['iddemande']); }
			
			
			$tabAnalyses[] = $liste['Idanalyse'];
			$tabDemandes[] = $liste['iddemande'];
			
			$html .="</table>
				      <div style='width: 100%; height: 20px;'> </div>";
		}
	
		$donnees = array($html, $tabAnalyses, $tabDemandes);
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $donnees ) );
	}
	
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	public function enregistrerResultatsDemandeAction()
	{
	    $tabAnalyses = $this->params ()->fromPost ( 'tabAnalyses' );
	    $tabDemandes = $this->params ()->fromPost ( 'tabDemandes' );
	    $tableau = $this->params ()->fromPost ( 'tab' );
	    $idemploye = $this->layout()->user['idpersonne'];
	    
	    for ($i = 0 ; $i<count($tabAnalyses) ; $i++){
	        $idanalyse = $tabAnalyses[$i];
	        $iddemande = $tabDemandes[$i];
	        
	        if($idanalyse == 1){
	            $tab = $tableau[$idanalyse];
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $this->getResultatDemandeAnalyseTable()->addValeursNfs($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 2){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursGsrhGroupage($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 3){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursRechercheAntigene($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 4){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursTestCombsDirect($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 5){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursTestCombsIndirect($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 6){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursTestCompatibilite($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 7){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursVitesseSedimentation($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 8){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursTestDemmel($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 9){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursTauxReticulocyte($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 10){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGoutteEpaisse($tab, $iddemande);
	        }
	        
	   
	        else
	            if($idanalyse == 14){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTpInr($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 15){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTca($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 16){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFibrinemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 17){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTempsSaignement($tab, $iddemande);
	        }
	        
	        
	        else
	            if($idanalyse == 21){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGlycemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 22){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCreatininemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 23){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAzotemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 24){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAcideUrique($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 25){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolTotal($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 26){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTriglycerides($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 27){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolHDL($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 28){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolLDL($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 29){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeurs_Total_HDL_LDL_Triglycerides($tab, $iddemande);
	        }
	        
	        
	        else
	            if($idanalyse == 31){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursIonogramme($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 32){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCalcemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 33){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursMagnesemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 34){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPhosphoremie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 35){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTgoAsat($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 36){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTgpAlat($tab, $iddemande);
	        }
	        
	        
	        else
	            if($idanalyse == 38){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPhosphatageAlcaline($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 39){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGamaGtYgt($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 40){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFerSerique($tab, $iddemande);
	        }
	        else
	        if($idanalyse == 68){
	            $tab = $tableau[$idanalyse];
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTypageHemoglobine($tab, $iddemande);
	        }
	        
	    }
	    
	    //Le plus petit iddemande du tableau
	    $iddemande =  min($tabDemandes);
	    
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $iddemande ) );
	}
	
	
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE ========== ******
	public function recupererLesAnalysesDemandeesParTypeAction()
	{
		$idtype = ( int ) $this->params ()->fromPost ( 'idtype', 0 );
	
		$listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParType($idtype);
		
		$tableauDonnees = array();
		$tableauPatients = array();
		$tableauDemandes = array();
		
		foreach ($listeAnalysesType as $liste){
			$tableauDonnees[] = $liste;
			if( !in_array($liste['idpersonne'], $tableauPatients) ) { $tableauPatients[] = $liste['idpersonne']; }
		}
		
		$html ="";
		
		for($i = 0 ; $i < count($tableauPatients) ; $i++){
			$k = 0 ;
				
			$html .="<table class='designEnTeteAnalyse  patientListeAnalyse  pat_".$tableauPatients[$i]."' style='width: 100%;' >";
			for ($j = 0 ; $j < count($tableauDonnees) ; $j++){
		
				if($tableauDonnees[$j]['idpersonne'] == $tableauPatients[$i]){
					
				    if($k == 0){
						$k++;
						$depistage = $this->getPatientTable()->getDepistagePatient($tableauDonnees[$j]['idpersonne']);
						$typepatient = 'E';
						if($depistage->current()){ if($depistage->current()['typepatient'] == 1){ $typepatient = 'I'; } }
						$html .="<tr style='width: 90%;' > <td class='enTete2'>". $tableauDonnees[$j]['prenom'].' '.$tableauDonnees[$j]['nom'] .' ('.$tableauDonnees[$j]['idpersonne']."-".$typepatient.")</td> </tr>";
					}
		
					$html .="<tr> <th class='enTitre'> <div>". $tableauDonnees[$j]['Designation'] ."</div> </th> </tr>";
				    
					
					$tableauDemandes [] = $tableauDonnees[$j]['iddemande'];
					//Pour la reconnaissance de l'analyse demand�e
					$html .="<tr><td><table style='width: 100%; margin-left: 0px;'  class='listeAnalyseTAD  ER_".$tableauDonnees[$j]['iddemande']."' >";
					
					
					if($tableauDonnees[$j]['Idanalyse'] ==  1){ $html .= $this->nfs_1();                   }
					if($tableauDonnees[$j]['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2();         }
	                if($tableauDonnees[$j]['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3();    }
	                if($tableauDonnees[$j]['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4();     }
	                if($tableauDonnees[$j]['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5();   }
	                if($tableauDonnees[$j]['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6();    }
	                if($tableauDonnees[$j]['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); }
	                if($tableauDonnees[$j]['Idanalyse'] ==  8){ $html .= $this->test_demmel_8();           }
	                if($tableauDonnees[$j]['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10();       }
	                 
	                   if($tableauDonnees[$j]['Idanalyse'] == 11){ $html .= $this->adenogramme_11();                 }
	                   if($tableauDonnees[$j]['Idanalyse'] == 12){ $html .= $this->medulodramme_12();                }
	                   if($tableauDonnees[$j]['Idanalyse'] == 13){ $html .= $this->cytochimie_myeloperoxydase_13();  }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 14){ $html .= $this->tp_inr_14();               }
	                if($tableauDonnees[$j]['Idanalyse'] == 15){ $html .= $this->tca_15();                  }
	                if($tableauDonnees[$j]['Idanalyse'] == 16){ $html .= $this->fibrinemie_16();           }
	                if($tableauDonnees[$j]['Idanalyse'] == 17){ $html .= $this->temps_saignement_17();     }
	                
	                   if($tableauDonnees[$j]['Idanalyse'] == 18){ $html .= $this->facteur_viii_18(); }
	                   if($tableauDonnees[$j]['Idanalyse'] == 19){ $html .= $this->facteur_ix_19();   }
	                   if($tableauDonnees[$j]['Idanalyse'] == 20){ $html .= $this->dimeres_20();      }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 21){ $html .= $this->glycemie_21();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 22){ $html .= $this->creatininemie_22();        }
	                if($tableauDonnees[$j]['Idanalyse'] == 23){ $html .= $this->azotemie_23();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 24){ $html .= $this->acide_urique_24();         }
	                if($tableauDonnees[$j]['Idanalyse'] == 25){ $html .= $this->cholesterol_total_25();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 26){ $html .= $this->triglycerides_26();        } 
	                if($tableauDonnees[$j]['Idanalyse'] == 27){ $html .= $this->cholesterol_HDL_27();      }
	                if($tableauDonnees[$j]['Idanalyse'] == 28){ $html .= $this->cholesterol_LDL_28();      }
	                if($tableauDonnees[$j]['Idanalyse'] == 29){
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL TOTAL </div> </th> </tr>";
	                    $html .= $this->cholesterol_total_25();
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL HDL </div> </th> </tr>";
	                    $html .= $this->cholesterol_HDL_27();
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL LDL </div> </th> </tr>";
	                    $html .= $this->cholesterol_LDL_28();
	                    $html .="<tr> <th class='enTitre'> <div> TRIGLYCERIDES </div> </th> </tr>";
	                    $html .= $this->triglycerides_26();
	                }
	                   if($tableauDonnees[$j]['Idanalyse'] == 30){ $html .= $this->lipides_totaux_30(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 31){ $html .= $this->ionogramme_31();           }
	                if($tableauDonnees[$j]['Idanalyse'] == 32){ $html .= $this->calcemie_32();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 33){ $html .= $this->magnesemie_33();           } 
	                if($tableauDonnees[$j]['Idanalyse'] == 34){ $html .= $this->phosphoremie_34();         }
	                if($tableauDonnees[$j]['Idanalyse'] == 35){ $html .= $this->tgo_asat_35();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 36){ $html .= $this->tgp_alat_36();             }

	                   if($tableauDonnees[$j]['Idanalyse'] == 37){
	                      $html .="<tr> <th class='enTitre'> <div> ASAT </div> </th> </tr>";
	                      $html .= $this->tgo_asat_35();
	                      $html .="<tr> <th class='enTitre'> <div> ALAT </div> </th> </tr>";
 	                      $html .= $this->tgp_alat_36();
	                   }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 38){ $html .= $this->phosphatage_alcaline_38(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 39){ $html .= $this->gama_gt_ygt_39();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 40){ $html .= $this->fer_serique_40();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 41){ $html .= $this->ferritinine_41();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 42){ $html .= $this->bilirubine_totale_directe_42();  }
	                if($tableauDonnees[$j]['Idanalyse'] == 43){ $html .= $this->hemoglobine_glyquee_hbac_43();   } 
	                if($tableauDonnees[$j]['Idanalyse'] == 44){ $html .= $this->electrophorese_hemoglobine_44(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 45){ $html .= $this->electrophorese_preteines_45();   }
	                if($tableauDonnees[$j]['Idanalyse'] == 46){ $html .= $this->albuminemie_46();                }
	                if($tableauDonnees[$j]['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 48){ $html .= $this->protidemie_48();                 }
	                if($tableauDonnees[$j]['Idanalyse'] == 49){ $html .= $this->proteinurie_49();                }
	                if($tableauDonnees[$j]['Idanalyse'] == 50){ $html .= $this->hlm_compte_daddis_50();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 51){ $html .= $this->beta_hcg_plasmatique_51();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 52){ $html .= $this->psa_52();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 53){ $html .= $this->crp_53();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 54){ $html .= $this->facteurs_rhumatoides_54();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 55){ $html .= $this->rf_waaler_rose_55();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 56){ $html .= $this->toxoplasmose_56();               }
	                if($tableauDonnees[$j]['Idanalyse'] == 57){ $html .= $this->rubeole_57();                    }
	                if($tableauDonnees[$j]['Idanalyse'] == 58){ $html .= $this->culot_urinaire_58();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 59){ $html .= $this->serologie_chlamydiae_59();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 60){ $html .= $this->serologie_syphilitique_60();     }
	                if($tableauDonnees[$j]['Idanalyse'] == 61){ $html .= $this->aslo_61();                       }
	                if($tableauDonnees[$j]['Idanalyse'] == 62){ $html .= $this->widal_62();                      }
	                if($tableauDonnees[$j]['Idanalyse'] == 63){ $html .= $this->ag_hbs_63();                     }
	                if($tableauDonnees[$j]['Idanalyse'] == 64){ $html .= $this->hiv_64();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 65){ $html .= $this->pv_65(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 67){ $html .= $this->pus_67(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68(); } 
	                
					
	                $html .="</table></td></tr>";
				}
				
			}
			
			$html .="<tr><td style='height: 20px;'></td></tr></table>";
		}
		//Demande d'analyse et nombre de patients en info bulle
		$nbPatient = count($tableauPatients);
		if($nbPatient == 0 ){ $nbPatient = $nbPatient.' patient'; $html .="--- Aucun ---"; }
		else if($nbPatient == 1){ $nbPatient = $nbPatient.' patient'; }else{ $nbPatient = $nbPatient.' patients'; }
		$html .="<script> $('#infosNbPatientParType img').attr('title', '".$nbPatient."'); </script>";
		
		//Liste des analyses demand�es dans la liste des analyses existantes
		//Liste des analyses demand�es dans la liste des analyses existantes
		$listeAnalysesTypeGroup = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeGroupeAnalyse($idtype);
		$listeAnalysesTab = array();
		foreach ($listeAnalysesTypeGroup as $liste){ $listeAnalysesTab[] = $liste['Idanalyse']; }
	
		$liste_select = "<option>  </option>";
		foreach($this->getPatientTable()->getListeDesAnalyses($idtype) as $listeAnalyses){
		    if(in_array($listeAnalyses['idanalyse'], $listeAnalysesTab)){
		        $liste_select.= "<option style=\'color: red;\' value=".$listeAnalyses['idanalyse']." > ".str_replace("'", "\'", $listeAnalyses['designation'])." </option>";
		    }else {
		        $liste_select.= "<option value=".$listeAnalyses['idanalyse']." > ".str_replace("'", "\'", $listeAnalyses['designation'])." </option>";
		    }
		}
		
		//Liste des dates de demandes d'analyses
		//Liste des dates de demandes d'analyses
		$listeAnalysesTypeGroupDate = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeGroupeDate($idtype);
		$listeAnalysesTabDate = array();
		$control = new DateHelper();
		$liste_date = "<option>  </option>";
		foreach ($listeAnalysesTypeGroupDate as $liste){
		    $aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		    $hier = date("d/m/Y", strtotime('-1 day'));
		    
		    $laDate = $control->convertDate($liste['date']);
		    if($laDate == $aujourdhui){ $laDate = "Aujourd\'hui"; }
		    elseif($laDate == $hier){ $laDate = "Hier"; }
		    
		    $liste_date.= "<option value=".$liste['date']." > ".$laDate." </option>";
		}
		
		$html .="<script> $('#listeAnalyseParType').html('".$liste_select."'); </script>";
		$html .="<script> $('#listeAnalyseParTypeParDate').html('".$liste_date."'); </script>";
	
		
		//R�cup�ration de la liste des demandes, pour savoir les patients pour qui on a entr� des r�sultats
		$html .="<script> var listeDesDemandesSelect = []; </script>";
		for($i = 0 ; $i < count($tableauDemandes) ; $i++){
		    $html .="<script> listeDesDemandesSelect[".$i."]=".$tableauDemandes[$i]."; </script>";
		}
		
		
		//R�cup�ration de la liste des codes des patients
		//R�cup�ration de la liste des codes des patients
		$liste_code= "<option>  </option>";
		for($i = 0 ; $i < count($tableauPatients) ; $i++){
		    $liste_code.= "<option value=".$tableauPatients[$i]." > ".$tableauPatients[$i]."</option>";
		}
		$html .="<script> $('#listeCodesDesPatients').html('".$liste_code."'); </script>";
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ========== ******
	public function recupererLesAnalysesDemandeesParTypeEtAnalyseAction()
	{
	    $idtype = ( int ) $this->params ()->fromPost ( 'idtype', 0 );
	    $idanalyse = ( int ) $this->params ()->fromPost ( 'idanalyse', 0 );
	
	    $listeAnalysesType = null;
	    if($idanalyse == 0){
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParType($idtype);
	    }else{ 
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeEtAnalyse($idtype, $idanalyse); 
	    }
	
	    $tableauDonnees = array();
	    $tableauPatients = array();
	    $tableauDemandes = array();
	
	    foreach ($listeAnalysesType as $liste){
	        $tableauDonnees[] = $liste;
	        if( !in_array($liste['idpersonne'], $tableauPatients) ) { $tableauPatients[] = $liste['idpersonne']; }
	    }
	
	    $html ="";
	
	    for($i = 0 ; $i < count($tableauPatients) ; $i++){
	        $k = 0 ;
	
	        $html .="<table class='designEnTeteAnalyse  patientListeAnalyse  pat_".$tableauPatients[$i]."' style='width: 100%;' >";
	        for ($j = 0 ; $j < count($tableauDonnees) ; $j++){
	
	            if($tableauDonnees[$j]['idpersonne'] == $tableauPatients[$i]){
	                	
	                if($k == 0){
	                    $k++;
	                    $depistage = $this->getPatientTable()->getDepistagePatient($tableauDonnees[$j]['idpersonne']);
						$typepatient = 'E';
						if($depistage->current()){ if($depistage->current()['typepatient'] == 1){ $typepatient = 'I'; } }
	                    $html .="<tr style='width: 90%;' > <td class='enTete2'>". $tableauDonnees[$j]['prenom'].' '.$tableauDonnees[$j]['nom'] .' ('.$tableauDonnees[$j]['idpersonne']."-".$typepatient.") </td> </tr>";
	                }
	
	                $html .="<tr> <th class='enTitre'> <div>". $tableauDonnees[$j]['Designation'] ." </div> </th> </tr>";
	
	                
	                $tableauDemandes [] = $tableauDonnees[$j]['iddemande'];
	                //Pour la reconnaissance de l'analyse demand�e
	                $html .="<tr><td><table style='width: 100%; margin-left: 0px;'  class='listeAnalyseTAD  ER_".$tableauDonnees[$j]['iddemande']."' >";
	                 
	                
	                if($tableauDonnees[$j]['Idanalyse'] ==  1){ $html .= $this->nfs_1();                   }
	                if($tableauDonnees[$j]['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2();         }
	                if($tableauDonnees[$j]['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3();    }
	                if($tableauDonnees[$j]['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4();     }
	                if($tableauDonnees[$j]['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5();   }
	                if($tableauDonnees[$j]['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6();    }
	                if($tableauDonnees[$j]['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); }
	                if($tableauDonnees[$j]['Idanalyse'] ==  8){ $html .= $this->test_demmel_8();           }
	                if($tableauDonnees[$j]['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10();       }
	                 
	                   if($tableauDonnees[$j]['Idanalyse'] == 11){ $html .= $this->adenogramme_11();                 }
	                   if($tableauDonnees[$j]['Idanalyse'] == 12){ $html .= $this->medulodramme_12();                }
	                   if($tableauDonnees[$j]['Idanalyse'] == 13){ $html .= $this->cytochimie_myeloperoxydase_13();  }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 14){ $html .= $this->tp_inr_14();               }
	                if($tableauDonnees[$j]['Idanalyse'] == 15){ $html .= $this->tca_15();                  }
	                if($tableauDonnees[$j]['Idanalyse'] == 16){ $html .= $this->fibrinemie_16();           }
	                if($tableauDonnees[$j]['Idanalyse'] == 17){ $html .= $this->temps_saignement_17();     }
	                 
	                   if($tableauDonnees[$j]['Idanalyse'] == 18){ $html .= $this->facteur_viii_18(); }
	                   if($tableauDonnees[$j]['Idanalyse'] == 19){ $html .= $this->facteur_ix_19();   }
	                   if($tableauDonnees[$j]['Idanalyse'] == 20){ $html .= $this->dimeres_20();      }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 21){ $html .= $this->glycemie_21();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 22){ $html .= $this->creatininemie_22();        }
	                if($tableauDonnees[$j]['Idanalyse'] == 23){ $html .= $this->azotemie_23();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 24){ $html .= $this->acide_urique_24();         }
	                if($tableauDonnees[$j]['Idanalyse'] == 25){ $html .= $this->cholesterol_total_25();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 26){ $html .= $this->triglycerides_26();        }
	                if($tableauDonnees[$j]['Idanalyse'] == 27){ $html .= $this->cholesterol_HDL_27();      }
	                if($tableauDonnees[$j]['Idanalyse'] == 28){ $html .= $this->cholesterol_LDL_28();      }
	                if($tableauDonnees[$j]['Idanalyse'] == 29){
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL TOTAL </div> </th> </tr>";
	                    $html .= $this->cholesterol_total_25();
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL HDL </div> </th> </tr>";
	                    $html .= $this->cholesterol_HDL_27();
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL LDL </div> </th> </tr>";
	                    $html .= $this->cholesterol_LDL_28();
	                    $html .="<tr> <th class='enTitre'> <div> TRIGLYCERIDES </div> </th> </tr>";
	                    $html .= $this->triglycerides_26();
	                }
	                   if($tableauDonnees[$j]['Idanalyse'] == 30){ $html .= $this->lipides_totaux_30();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 31){ $html .= $this->ionogramme_31();           }
	                if($tableauDonnees[$j]['Idanalyse'] == 32){ $html .= $this->calcemie_32();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 33){ $html .= $this->magnesemie_33();           }
	                if($tableauDonnees[$j]['Idanalyse'] == 34){ $html .= $this->phosphoremie_34();         }
	                if($tableauDonnees[$j]['Idanalyse'] == 35){ $html .= $this->tgo_asat_35();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 36){ $html .= $this->tgp_alat_36();             }
	                
	                   if($tableauDonnees[$j]['Idanalyse'] == 37){
	                      $html .="<tr> <th class='enTitre'> <div> ASAT </div> </th> </tr>";
	                      $html .= $this->tgo_asat_35();
	                      $html .="<tr> <th class='enTitre'> <div> ALAT </div> </th> </tr>";
	                      $html .= $this->tgp_alat_36();
	                   }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 38){ $html .= $this->phosphatage_alcaline_38(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 39){ $html .= $this->gama_gt_ygt_39();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 40){ $html .= $this->fer_serique_40();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 41){ $html .= $this->ferritinine_41();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 42){ $html .= $this->bilirubine_totale_directe_42();  }
	                if($tableauDonnees[$j]['Idanalyse'] == 43){ $html .= $this->hemoglobine_glyquee_hbac_43();   }
	                if($tableauDonnees[$j]['Idanalyse'] == 44){ $html .= $this->electrophorese_hemoglobine_44(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 45){ $html .= $this->electrophorese_preteines_45();   }
	                if($tableauDonnees[$j]['Idanalyse'] == 46){ $html .= $this->albuminemie_46();                }
	                if($tableauDonnees[$j]['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 48){ $html .= $this->protidemie_48();                 }
	                if($tableauDonnees[$j]['Idanalyse'] == 49){ $html .= $this->proteinurie_49();                }
	                if($tableauDonnees[$j]['Idanalyse'] == 50){ $html .= $this->hlm_compte_daddis_50();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 51){ $html .= $this->beta_hcg_plasmatique_51();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 52){ $html .= $this->psa_52();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 53){ $html .= $this->crp_53();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 54){ $html .= $this->facteurs_rhumatoides_54();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 55){ $html .= $this->rf_waaler_rose_55();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 56){ $html .= $this->toxoplasmose_56();               }
	                if($tableauDonnees[$j]['Idanalyse'] == 57){ $html .= $this->rubeole_57();                    }
	                if($tableauDonnees[$j]['Idanalyse'] == 58){ $html .= $this->culot_urinaire_58();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 59){ $html .= $this->serologie_chlamydiae_59();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 60){ $html .= $this->serologie_syphilitique_60();     }
	                if($tableauDonnees[$j]['Idanalyse'] == 61){ $html .= $this->aslo_61();                       }
	                if($tableauDonnees[$j]['Idanalyse'] == 62){ $html .= $this->widal_62();                      }
	                if($tableauDonnees[$j]['Idanalyse'] == 63){ $html .= $this->ag_hbs_63();                     }
	                if($tableauDonnees[$j]['Idanalyse'] == 64){ $html .= $this->hiv_64();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 65){ $html .= $this->pv_65(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 67){ $html .= $this->pus_67(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68(); }
	                 
	                
	                
	                $html .="</table></td></tr>";
	            }
	
	        }
	        	
	        $html .="<tr><td style='height: 20px;'></td></tr></table>";
	    }
	
	    if($idanalyse != 0){
	        $nbPatient = count($tableauPatients);
	        if($nbPatient == 0 ){ $nbPatient = $nbPatient.' patient'; $html .="--- Aucun ---"; }
	        else if($nbPatient == 1 ){ $nbPatient = $nbPatient.' patient'; }
	        else { $nbPatient = $nbPatient.' patients'; }
	        $html .="<script> $('#infosNbPatientParAnalyse img').attr('title', '".$nbPatient."').css({'opacity' : '1'}); </script>";
	    }else{
	        $html .="<script> $('#infosNbPatientParAnalyse img').attr('title', '').css({'opacity' : '0'}); </script>";
	    }
	    
	    //Liste des dates de demandes d'analyses
	    //Liste des dates de demandes d'analyses
	    $listeAnalysesTypeGroupDate = null;
	    if($idanalyse == 0){
	        $listeAnalysesTypeGroupDate = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeGroupeDate($idtype);
	    }else{
	        $listeAnalysesTypeGroupDate = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeEtParAnalyseGroupeDate($idtype, $idanalyse);
	    }

	    $listeAnalysesTabDate = array();
	    $control = new DateHelper();
	    $liste_date = "<option>  </option>";
	    foreach ($listeAnalysesTypeGroupDate as $liste){
	        $aujourdhui = (new \DateTime() ) ->format('d/m/Y');
	        $hier = date("d/m/Y", strtotime('-1 day'));
	        
	        $laDate = $control->convertDate($liste['date']);
	        if($laDate == $aujourdhui){ $laDate = "Aujourd\'hui"; }
	        elseif ($laDate == $hier){ $laDate = "Hier"; }
	        
	        $liste_date.= "<option value=".$liste['date']." > ".$laDate." </option>";
	    }
	    $html .="<script> $('#listeAnalyseParTypeParDate').html('".$liste_date."'); </script>";
	    
	    
	    //R�cup�ration de la liste des demandes, pour savoir les patients pour qui on a entr� des r�sultats
	    $html .="<script> var listeDesDemandesSelect = []; </script>";
	    for($i = 0 ; $i < count($tableauDemandes) ; $i++){
	        $html .="<script> listeDesDemandesSelect[".$i."]=".$tableauDemandes[$i]."; </script>";
	    }
 	    
	    
	    //R�cup�ration de la liste des codes des patients
	    //R�cup�ration de la liste des codes des patients
	    $liste_code= "<option>  </option>";
	    for($i = 0 ; $i < count($tableauPatients) ; $i++){
	        $liste_code.= "<option value=".$tableauPatients[$i]." > ".$tableauPatients[$i]."</option>";
	    }
	    $html .="<script> $('#listeCodesDesPatients').html('".$liste_code."'); </script>";
	    
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ET DATE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ET DATE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ET DATE ========== ******
	public function recupererLesAnalysesDemandeesParTypeEtAnalyseEtDateAction()
	{
	 
	    $idtype = ( int ) $this->params ()->fromPost ( 'idtype', 0 );
	    $idanalyse = ( int ) $this->params ()->fromPost ( 'idanalyse', 0 );
	    $date = $this->params ()->fromPost ( 'date' );
	    
	    $html ="";
	    $listeAnalysesType = null;
	    
	    if($idanalyse && $date){
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeEtAnalyseEtDate($idtype, $idanalyse, $date);
	    }elseif ($idanalyse && !$date){
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeEtAnalyse($idtype, $idanalyse);
	    }elseif (!$idanalyse && $date){
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeEtDate($idtype, $date);
	    }elseif (!$idanalyse && !$date){
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParType($idtype);
	    }
	    
	    
	    $tableauDonnees = array();
	    $tableauPatients = array();
	    $tableauDemandes = array();
	    
	    if($listeAnalysesType){
	        foreach ($listeAnalysesType as $liste){
	            $tableauDonnees[] = $liste;
	            if( !in_array($liste['idpersonne'], $tableauPatients) ) { $tableauPatients[] = $liste['idpersonne']; }
	        }
	         
	        for($i = 0 ; $i < count($tableauPatients) ; $i++){
	            $k = 0 ;
	             
	            $html .="<table class='designEnTeteAnalyse  patientListeAnalyse  pat_".$tableauPatients[$i]."' style='width: 100%;' >";
	            
	            for ($j = 0 ; $j < count($tableauDonnees) ; $j++){
	                 
	                if($tableauDonnees[$j]['idpersonne'] == $tableauPatients[$i]){
	                     
	                    if($k == 0){
	                        $k++;
	                        $depistage = $this->getPatientTable()->getDepistagePatient($tableauDonnees[$j]['idpersonne']);
						    $typepatient = 'E';
						    if($depistage->current()){ if($depistage->current()['typepatient'] == 1){ $typepatient = 'I'; } }
	                        $html .="<tr style='width: 90%;' > <td class='enTete2'>". $tableauDonnees[$j]['prenom'].' '.$tableauDonnees[$j]['nom'] .' ('.$tableauDonnees[$j]['idpersonne']."-".$typepatient.")</td> </tr>";
	                    }
	                     
	                    $html .="<tr> <th class='enTitre'> <div>". $tableauDonnees[$j]['Designation'] ." </div> </th> </tr>";
	                     
	                    
	                    $tableauDemandes [] = $tableauDonnees[$j]['iddemande'];
	                    //Pour la reconnaissance de l'analyse demand�e
	                    $html .="<tr><td><table style='width: 100%; margin-left: 0px;'  class='listeAnalyseTAD  ER_".$tableauDonnees[$j]['iddemande']."' >";
	                    
	                    if($tableauDonnees[$j]['Idanalyse'] ==  1){ $html .= $this->nfs_1();                   }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2();         }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3();    }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4();     }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5();   }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6();    }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  8){ $html .= $this->test_demmel_8();           }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9();    }
	                    if($tableauDonnees[$j]['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10();       }
	                     
    	                   if($tableauDonnees[$j]['Idanalyse'] == 11){ $html .= $this->adenogramme_11();                 }
	                       if($tableauDonnees[$j]['Idanalyse'] == 12){ $html .= $this->medulodramme_12();                }
	                       if($tableauDonnees[$j]['Idanalyse'] == 13){ $html .= $this->cytochimie_myeloperoxydase_13();  }
	                    
	                    if($tableauDonnees[$j]['Idanalyse'] == 14){ $html .= $this->tp_inr_14();               }
	                    if($tableauDonnees[$j]['Idanalyse'] == 15){ $html .= $this->tca_15();                  }
	                    if($tableauDonnees[$j]['Idanalyse'] == 16){ $html .= $this->fibrinemie_16();           }
	                    if($tableauDonnees[$j]['Idanalyse'] == 17){ $html .= $this->temps_saignement_17();     }
	                     
	                       if($tableauDonnees[$j]['Idanalyse'] == 18){ $html .= $this->facteur_viii_18(); }
	                       if($tableauDonnees[$j]['Idanalyse'] == 19){ $html .= $this->facteur_ix_19();   }
	                       if($tableauDonnees[$j]['Idanalyse'] == 20){ $html .= $this->dimeres_20();      }
	                    
	                    if($tableauDonnees[$j]['Idanalyse'] == 21){ $html .= $this->glycemie_21();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 22){ $html .= $this->creatininemie_22();        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 23){ $html .= $this->azotemie_23();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 24){ $html .= $this->acide_urique_24();         }
	                    if($tableauDonnees[$j]['Idanalyse'] == 25){ $html .= $this->cholesterol_total_25();    }
	                    if($tableauDonnees[$j]['Idanalyse'] == 26){ $html .= $this->triglycerides_26();        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 27){ $html .= $this->cholesterol_HDL_27();      }
	                    if($tableauDonnees[$j]['Idanalyse'] == 28){ $html .= $this->cholesterol_LDL_28();      }
	                    if($tableauDonnees[$j]['Idanalyse'] == 29){
	                        $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL TOTAL </div> </th> </tr>";
	                        $html .= $this->cholesterol_total_25(); 
	                        $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL HDL </div> </th> </tr>";
	                        $html .= $this->cholesterol_HDL_27();
	                        $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL LDL </div> </th> </tr>";
	                        $html .= $this->cholesterol_LDL_28(); 
	                        $html .="<tr> <th class='enTitre'> <div> TRIGLYCERIDES </div> </th> </tr>";
	                        $html .= $this->triglycerides_26();
	                    }
	                    if($tableauDonnees[$j]['Idanalyse'] == 30){ $html .= $this->lipides_totaux_30();       }
	                    if($tableauDonnees[$j]['Idanalyse'] == 31){ $html .= $this->ionogramme_31();           }
	                    if($tableauDonnees[$j]['Idanalyse'] == 32){ $html .= $this->calcemie_32();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 33){ $html .= $this->magnesemie_33();           }
	                    if($tableauDonnees[$j]['Idanalyse'] == 34){ $html .= $this->phosphoremie_34();         }
	                    if($tableauDonnees[$j]['Idanalyse'] == 35){ $html .= $this->tgo_asat_35();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 36){ $html .= $this->tgp_alat_36();             }
	                    
	                       if($tableauDonnees[$j]['Idanalyse'] == 37){
	                          $html .="<tr> <th class='enTitre'> <div> ASAT </div> </th> </tr>";
	                          $html .= $this->tgo_asat_35();
	                          $html .="<tr> <th class='enTitre'> <div> ALAT </div> </th> </tr>";
	                          $html .= $this->tgp_alat_36();
	                       }
	                    
	                    if($tableauDonnees[$j]['Idanalyse'] == 38){ $html .= $this->phosphatage_alcaline_38(); }
	                    if($tableauDonnees[$j]['Idanalyse'] == 39){ $html .= $this->gama_gt_ygt_39();          }
	                    if($tableauDonnees[$j]['Idanalyse'] == 40){ $html .= $this->fer_serique_40();          }
	                    if($tableauDonnees[$j]['Idanalyse'] == 41){ $html .= $this->ferritinine_41();          }
	                    if($tableauDonnees[$j]['Idanalyse'] == 42){ $html .= $this->bilirubine_totale_directe_42();  }
	                    if($tableauDonnees[$j]['Idanalyse'] == 43){ $html .= $this->hemoglobine_glyquee_hbac_43();   }
	                    if($tableauDonnees[$j]['Idanalyse'] == 44){ $html .= $this->electrophorese_hemoglobine_44(); }
	                    if($tableauDonnees[$j]['Idanalyse'] == 45){ $html .= $this->electrophorese_preteines_45();   }
	                    if($tableauDonnees[$j]['Idanalyse'] == 46){ $html .= $this->albuminemie_46();                }
	                    if($tableauDonnees[$j]['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47();          }
	                    if($tableauDonnees[$j]['Idanalyse'] == 48){ $html .= $this->protidemie_48();                 }
	                    if($tableauDonnees[$j]['Idanalyse'] == 49){ $html .= $this->proteinurie_49();                }
	                    if($tableauDonnees[$j]['Idanalyse'] == 50){ $html .= $this->hlm_compte_daddis_50();          }
	                    if($tableauDonnees[$j]['Idanalyse'] == 51){ $html .= $this->beta_hcg_plasmatique_51();       }
	                    if($tableauDonnees[$j]['Idanalyse'] == 52){ $html .= $this->psa_52();                        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 53){ $html .= $this->crp_53();                        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 54){ $html .= $this->facteurs_rhumatoides_54();       }
	                    if($tableauDonnees[$j]['Idanalyse'] == 55){ $html .= $this->rf_waaler_rose_55();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 56){ $html .= $this->toxoplasmose_56();               }
	                    if($tableauDonnees[$j]['Idanalyse'] == 57){ $html .= $this->rubeole_57();                    }
	                    if($tableauDonnees[$j]['Idanalyse'] == 58){ $html .= $this->culot_urinaire_58();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 59){ $html .= $this->serologie_chlamydiae_59();       }
	                    if($tableauDonnees[$j]['Idanalyse'] == 60){ $html .= $this->serologie_syphilitique_60();     }
	                    if($tableauDonnees[$j]['Idanalyse'] == 61){ $html .= $this->aslo_61();                       }
	                    if($tableauDonnees[$j]['Idanalyse'] == 62){ $html .= $this->widal_62();                      }
	                    if($tableauDonnees[$j]['Idanalyse'] == 63){ $html .= $this->ag_hbs_63();                     }
	                    if($tableauDonnees[$j]['Idanalyse'] == 64){ $html .= $this->hiv_64();                        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 65){ $html .= $this->pv_65(); }
	                    if($tableauDonnees[$j]['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
	                    if($tableauDonnees[$j]['Idanalyse'] == 67){ $html .= $this->pus_67(); }
	                    if($tableauDonnees[$j]['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68(); }
	                     
	                    
	                    
	                    $html .="</table></td></tr>";
	                }
	                 
	            }
	             
	            $html .="<tr><td style='height: 20px;'></td></tr></table>";
	        }
	        if($date){
	            $nbPatient = count($tableauPatients);
	            if( $nbPatient == 1 ){ $nbPatient = $nbPatient.' patient'; } else { $nbPatient = $nbPatient.' patients'; }
	            $html .="<script> $('#infosNbPatientParAnalyseParDate img').attr('title', '".$nbPatient."').css({'opacity' : '1'}); </script>";
	        }else{
	            $html .="<script> $('#infosNbPatientParAnalyseParDate img').attr('title', '').css({'opacity' : '0'}); </script>";
	        }
	    }
	    

	    //R�cup�ration de la liste des codes des patients
	    //R�cup�ration de la liste des codes des patients
	    $liste_code= "<option>  </option>";
	    for($i = 0 ; $i < count($tableauPatients) ; $i++){
	        $liste_code.= "<option value=".$tableauPatients[$i]." > ".$tableauPatients[$i]."</option>";
	    }
	    $html .="<script> $('#listeCodesDesPatients').html('".$liste_code."'); </script>";
	    
	    
	    
	    //R�cup�ration de la liste des demandes, pour connaitre les demandes 
	    $html .="<script> var listeDesDemandesSelect = []; </script>";
	    for($i = 0 ; $i < count($tableauDemandes) ; $i++){
	        $html .="<script> listeDesDemandesSelect[".$i."]=".$tableauDemandes[$i]."; </script>";
	    }
	    
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
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
	
	/*
	 * GESTION DES LABELS DES DIFFERENTES ANALYSES *********** GESTION DES LABELS DES DIFFERENTES ANALYSES
	 * GESTION DES LABELS DES DIFFERENTES ANALYSES *********** GESTION DES LABELS DES DIFFERENTES ANALYSES
	 * GESTION DES LABELS DES DIFFERENTES ANALYSES *********** GESTION DES LABELS DES DIFFERENTES ANALYSES
	 * GESTION DES LABELS DES DIFFERENTES ANALYSES *********** GESTION DES LABELS DES DIFFERENTES ANALYSES
	 */
	/**
	 * analyse 1
	 */
	public function nfs_1(){
	    
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<input id='nfs_1' type='hidden' value='1'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 20px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_nfs'> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Leucocytes <input id='champ1' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> /mm&sup3 </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (4 000 - 10 000) </label></td>";
	    $html .= "</tr>";
	     
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%; background: re;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> P.N </td>";
	    $html .="         <td style='width: 35%;'> <input id='champ2' type='number' readonly='true'  step='any'> /mm&sup3; </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ7' type='number' step='any'> % </td>";
	    $html .="       </tr>";
	    $html .="     </table>";
	    $html .="   </label>";
	    $html .=" </td>";
	    $html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'><div style='width:65%; float:left; '>(2000 - 7000)</div> <div style='width: 35%; float:left;'>(45 - 70)</div> </label></td>";
	    $html .="</tr>";
	    
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> P.E </td>";
	    $html .="         <td style='width: 35%; '> <input id='champ3' type='number' readonly='true'  step='any'> /mm&sup3; </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ8' type='number' step='any'> % </td>";
	    $html .="       </tr>";
	    $html .="     </table>";
	    $html .="   </label>";
	    $html .=" </td>";
	    $html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(20 - 500)</div> <div style='width: 35%; float:left;'>(0 - 5)</div> </label></td>";
	    $html .="</tr>";
	    
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> P.B </td>";
	    $html .="         <td style='width: 35%; '> <input id='champ4' type='number' readonly='true'  step='any'> /mm&sup3; </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ9' type='number' step='any'> % </td>";
	    $html .="       </tr>";
	    $html .="     </table>";
	    $html .="   </label>";
	    $html .=" </td>";
	    $html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(0 - 100)</div> <div style='width: 35%; float:left;'>(0 - 3)</div> </label></td>";
	    $html .="</tr>";
	    
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> Lymphocytes </td>";
	    $html .="         <td style='width: 35%; '> <input id='champ5' type='number' readonly='true'  step='any'> /mm&sup3; </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ10' type='number' step='any'> % </td>";
	    $html .="       </tr>";
	    $html .="     </table>";
	    $html .="   </label>";
	    $html .=" </td>";
	    $html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(800 - 4000)</div> <div style='width: 35%; float:left;'>(20 - 40)</div> </label></td>";
	    $html .="</tr>";
	    
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> Monocytes </td>";
	    $html .="         <td style='width: 35%; '> <input id='champ6' type='number' readonly='true'  step='any'> /mm&sup3; </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ11' type='number' step='any'> % </td>";
	    $html .="       </tr>";
	    $html .="     </table>";
	    $html .="   </label>";
	    $html .=" </td>";
	    $html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(120 - 1200)</div> <div style='width: 35%; float:left;'>(3 - 15)</div> </label></td>";
	    $html .="</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%; height: 20px; '> </td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Erythrocytes <input id='champ12' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> 10p6/mm&sup3 </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (3,5 - 5,0) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;' ><span> H&eacute;moglobine <input id='champ13' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px; font-weight:bold;' > g/dl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px;  font-weight:bold; width: 80%;'> (11 - 15) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> H&eacute;matocrite <input id='champ14' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (37 - 50) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> V.G.M <input id='champ15' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (80 - 100) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> T.C.M.H <input id='champ16' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> pg </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (27 - 34) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> C.C.M.H <input id='champ17' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/dl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (32 - 36) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDR - CV <input id='champ18' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (11 - 16) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDR - DS <input id='champ19' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (35 - 56) </label></td>";
	    $html .= "</tr>";
	    
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%; height: 20px; '> </td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Plaquettes <input id='champ20' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> 10&sup3/mm&sup3 </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (150 - 450) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> VMP <input id='champ21' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 6, 5 - 12, 0 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDP <input id='champ22' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/dl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 9, 0 - 17, 0 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> PCT <input id='champ23' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 0, 108 - 0, 282 </label></td>";
	    $html .= "</tr>";
	    
	    
	    $html .= "</table> </td> </tr>";
	    
	    
	    return $html;
	}
	
	
	/**
	 * analyse 2
	 */
	public function gsrh_groupage_2(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Groupe <select name='groupe' id='groupe' > <option >  </option> <option value='A' >A</option> <option value='B' >B</option> <option value='AB' >AB</option> <option value='O' >O</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Rh&eacute;sus <select name='rhesus' id='rhesus' > <option >  </option> <option value='Rh+' >Rh+</option> <option value='Rh-' >Rh-</option> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	     
	    return $html;
	}
	
	
	/**
	 * analyse 3
	 */
	public function recherche_antigene_3(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Pr&eacute;sence d'antig&egrave;ne <select name='antigene_d_faible' id='antigene_d_faible' > <option >  </option> <option value='Presence' >Pr&eacute;sent</option> <option value='Absence' >Absent</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	    
	    return $html;
	}
	
	
	/**
	 * analyse 4
	 */
	public function test_combs_direct_4(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_combs_direct' id='test_combs_direct' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	     
	    return $html;
	}
	
	
	/**
	 * analyse 5
	 */
	public function test_combs_indirect_5(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_combs_indirect' id='test_combs_indirect' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 6
	 */
	public function test_compatibilite_6(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_compatibilite' id='test_compatibilite' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 7
	 */
	public function vitesse_sedimentation_7(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Valeur VS 1 heure <input type='number' id='vitesse_sedimentation' name='vitesse_sedimentation'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mm </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> H<15 | H>20 ; +60ans <30 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 8
	 */
	public function test_demmel_8(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_demmel' id='test_demmel' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 9
	 */
	public function taux_reticulocytes_9(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> TR <input name='taux_reticulocyte' id='taux_reticulocyte' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mm&sup3; </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 25 000 &agrave; 80 000 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 10
	 */
	public function goutte_epaisse_10(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Goutte &eacute;paisse <select onchange='getDensiteGE(this.value)' name='goutte_epaisse' id='goutte_epaisse' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' id='goutte_epaisse_positif' style='width: 100%; display: none;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Densit&eacute; parasitaire <input name='densite_parasitaire' id='densite_parasitaire' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> p/ul </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<script>  function getDensiteGE(valeur){ if(valeur == 'Positif'){ $('#goutte_epaisse_positif').toggle(true); }else{ $('#goutte_epaisse_positif').toggle(false); } } </script>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 11
	 */
	public function adenogramme_11(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> adenogramme (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 12
	 */
	public function medulodramme_12(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> medulodramme (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 13
	 */
	public function cytochimie_myeloperoxydase_13(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> cytochimie myeloperoxydase (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 14
	 */
	public function tp_inr_14(){
	     
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	     
	    //TQ - TQ - TQ ***************************************
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'>TQ (Temps de Quick) </span></label></td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> T&eacute;moin <input type='number' id='temps_quick_temoin' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 11 - 13  </label></td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient <input type='number' id='temps_quick_patient' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    //TP - TP - TP ***************************************
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'>TP (Taux de Prothrombine) </span></label></td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient <input type='number' id='taux_prothrombine_patient' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 70 - 100 </label></td>";
	    $html .= "</tr>";
	    
	    //INR - INR - INR ***************************************
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'>INR (International Normalised Ratio) </span></label></td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient  <input type='number' id='inr_patient' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: < 1,2 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='background: #fdfdfd;' ><span style='font-weight: bold;'> NB: Patient sous sintron N = {2;3} </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='background: #fdfdfd; padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='background: #fdfdfd; padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	 
	    $html .= "</table> </td> </tr>";
	    
	    return $html;
	}
	
	
	/**
	 * analyse 15
	 */
	public function tca_15(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient <input type='number' id='tca_patient' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 25 &agrave; 41 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> T&eacute;moin <input type='number' id='temoin_patient' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Ratio <input type='number' id='tca_ratio' step='any' readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> < 1,2 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 16
	 */
	public function fibrinemie_16(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> fibrin&eacute;mie <input id='fibrinemie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 2 - 4</label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 17
	 */
	public function temps_saignement_17(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> temps de saignement <input id='temps_saignement' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mn </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 2 - 6 mn</label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 18
	 */
	public function facteur_viii_18(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> facteur VIII <input id='facteur_8' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 19
	 */
	public function facteur_ix_19(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> facteur IX <input id='facteur_9' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 20
	 */
	public function dimeres_20(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> d dimeres <input id='d_dimeres' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}

	
	/**
	 * analyse 21
	 */
	public function glycemie_21(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='glycemie_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 0,7 &agrave; 1,10 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='glycemie_2' type='number' step='any' readonly='true' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 4,1 &agrave; 5,9 </label></td>";
	    $html .= "</tr>";
	    
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 22
	 */
	public function creatininemie_22(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> cr&eacute;atinin&eacute;mie <input id='creatininemie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: H= 7 &agrave; 13 | F= 6 &agrave; 11</label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='creatininemie_umol' type='number' step='any' readonly='true' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> umol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 23
	 */
	public function azotemie_23(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> ur&eacute;e sanguine <input id='uree_sanguine' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 0,15 &agrave; 0,45</label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 24
	 */
	public function acide_urique_24(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> uric&eacute;mie <input id='acide_urique' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> N : H= 35  &agrave; 72, F= 26 &agrave; 60 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 25
	 */
	public function cholesterol_total_25(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_total_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_total_2' type='number' step='any' readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	    
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table style='width: 90%; margin-left: -20px;'>";
	    
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "  <td style='width: 100%;'>
	                  <label class='lab1' style='background: #f9f9f9; height: 85px; padding-left: 10px; font-size: 13px;' >
	                      <span style='text-decoration: underline;'> Commentaire </span> <br>
	    
                          Moins de 30 ans <1, 80 (4, 7 mmol/l) - Plus de 30 ans < 2, 00 (< 5, 2mmol/l) <br>
                          Interpr&eacute;tation clinique : <span style='font-weight: bold;'> suspect </span> sup&eacute;rieur &agrave;  2, 20 (5, 7 mmol/l)<br>
                          <span style='font-weight: bold;'> Risque &eacute;lev&eacute;</span> sup&eacute;rieur &agrave; 2, 60 (6, 7 mmol/l)
	                  </label>
	                </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 26
	 */
	public function triglycerides_26(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='triglycerides_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%; font-size: 12px;'> H: 0,40 - 1,50 | F: 0,30 - 1,40 </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='triglycerides_2' type='number' step='any'  readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%; font-size: 12px;'> H: 0,45 - 1,70 | F: 0,35 - 1,60 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	     
	    return $html;
	}
	
	/**
	 * analyse 27
	 */
	public function cholesterol_HDL_27(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_HDL_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_HDL_2' type='number' step='any' readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	    
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table style='width: 90%; margin-left: -20px;'>";
	     
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "  <td style='width: 100%;'>
	                  <label class='lab1' style='background: #f9f9f9; height: 65px; padding-left: 10px; font-size: 13px;' >
	                      <span style='text-decoration: underline;'> Commentaire </span> <br>
	  
                          N: < 0, 35 ( < 0, 9 mmol/l) facteur de risque pour coronaropathies <br>
                          N: > 0, 60 ( > 1, 5 mmol/l) risque r&eacute;duit pour coronaropathies <br>
	        
	                  </label>
	                </td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	
	    
	    $html .= "<tr class='rapport_chol_hdl' style='display: none;'> <td align='center'>";
	    $html .= "<table style='width: 100%; margin-top: -10px;' >";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='background: #f9f9f9; padding-top: 5px; width: 100%; margin-left: 8px;' ><span style='font-weight: bold;'> &raquo; Rapport: CHOL/HDL <input id='rapport_chol_hdl' type='number' step='any' readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='background: #f9f9f9; padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='background: #f9f9f9; padding-top: 5px; width: 75%;'> N: < 3,5 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	    
	    return $html;
	}
	

	/**
	 * analyse 28
	 */
	public function cholesterol_LDL_28(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_LDL_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_LDL_2' type='number' step='any'  readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	     
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table style='width: 90%; margin-left: -20px;'>";
	
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "  <td style='width: 100%;'>
	                  <label class='lab1' style='background: #f9f9f9; height: 175px; padding-left: 10px; font-size: 13px;' >
	                      <span style='text-decoration: underline;'> Commentaire </span> <br>
	 
                          N: H < 0, 50 (< 1, 3 mmol/l); F: < 0, 63 (< 1,6 mmol/l) risque r&eacute;duit pour coronaropathies <br>
                          N: H > 1, 72 (> 4,5 mmol/l); F: > 1, 67 (4, 3 mmol/l) risque accru pour coronaropathies <br>
                          CIBLE THERAPEUTIQUE : <br>
                          En pr&eacute;sence d'un seul facteur de risque < 4,9 mmol/L < 1,90 g/L <br>
                          En pr&eacute;sence de 2 facteurs de risque < 4,1 mmol/L < 1,60 g/L <br>
                          En pr&eacute;sence de plus de 2 facteurs de risque < 3,4 mmol/L < 1,30 g/L <br>
                          En cas d'ant&eacute;c&eacute;dent cardiovasculaire < 2,6 mmol/L < 1,00 g/L <br>
	        
	                  </label>
	                </td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 30
	 */
	public function lipides_totaux_30(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> lipides totaux <input id='lipides_totaux' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 31
	 */
	public function ionogramme_31(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Sodium Sanguin ou Natr&eacute;mie <input id='sodium_sanguin' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 136 &agrave; 145 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Potassium Sanguin ou Kali&eacute;mie <input id='potassium_sanguin' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 3,5 &agrave; 5,1 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Chlore Sanguin ou Chlor&eacute;mie <input id='chlore_sanguin' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 97 &agrave; 111 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 32
	 */
	public function calcemie_32(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='height: 40px;'><span style='font-weight: bold; '> calc&eacute;mie <input id='calcemie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;  height: 40px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%; height: 40px;'> N: Adultes: 86 &agrave; 103  <spa style='padding-left: 18px;'> Enfants: 100 &agrave; 120 </spa> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 33
	 */
	public function magnesemie_33(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> magn&eacute;s&eacute;mie <input id='magnesemie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 17 &agrave; 24 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 34
	 */
	public function phosphoremie_34(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='height: 40px;'><span style='font-weight: bold; '> phosphor&eacute;mie <input id='phosphoremie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;  height: 40px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%; height: 40px;'> N: Adultes: 25 &agrave; 50  <spa style='padding-left: 18px;'> Enfants: 40 &agrave; 70 </spa> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 35
	 */
	public function tgo_asat_35(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> TGO/ASAT <input id='tgo_asat' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> u/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: H < 35 , F < 31 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 36
	 */
	public function tgp_alat_36(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> TGP/ALAT <input id='tgp_alat' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> u/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: H < 45 , F < 34 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 38
	 */
	public function phosphatage_alcaline_38(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '> Phosphatage alcaline <input id='phosphatage_alcaline' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> u/l </label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> N: H: 40 &agrave; 129 ; F: 35 &agrave; 104 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 39
	 */
	public function gama_gt_ygt_39(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> Gama GT <input id='gama_gt' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> u/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: H: 11 &agrave; 50 ; F: 7 &agrave; 32  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 40
	 */
	public function fer_serique_40(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '> fer s&eacute;rique <input id='fer_serique_ug' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> ug/dl </label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> N: H: 64,8  &agrave; 175 - F: 50,3 &agrave; 170 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '> fer s&eacute;rique <input id='fer_serique_umol' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> umol/l </label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> N: H: 11,6 &agrave; 31,3 - F: 9,0 &agrave; 30,4 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 41
	 */
	public function ferritinine_41(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> ferritinine <input id='ferritinine' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> ng/ml </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table style='width: 90%; margin-left: -20px;'>";
	    
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "  <td style='width: 100%;'>
	                  <label class='lab1' style='background: #f9f9f9; height: 90px; padding-left: 10px; font-size: 13px;' >
	                      <span style='text-decoration: underline;'> Valeurs Attendues: </span> <br>
	    
                          - hommes: 70 -  435    ng/ml <br>   
                          - Femmes cycliques: 10 - 160    ng/ml <br>    
                          - Femmes m&eacute;nopaus&eacute;es: 25 - 280    ng/ml <br>   
	        
	                  </label>
	                </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	    
	    return $html;
	}
	
	/**
	 * analyse 42
	 */
	public function bilirubine_totale_directe_42(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Bilirubine totale <input id='bilirubine_totale_mg' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> Bilirubine totale <input id='bilirubine_totale_umol' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> umol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> Bilirubine directe <input id='bilirubine_directe' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 43
	 */
	public function hemoglobine_glyquee_hbac_43(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%; margin-left: 5px;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='hemoglobine_glyquee_hbac' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab3' style='padding-top: 5px; width: 89%;'> HbA1C DCCT N: 4,27 - 6,07 </label></td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '>  <input id='hemoglobine_glyquee_hbac_mmol' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/mol </label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab3' style='padding-top: 5px; width: 89%;'> HbA1C IFCC N: 23 - 42 </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 44
	 */
	public function electrophorese_hemoglobine_44(){
		
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table id='electro_hemo' style='width: 100%; margin-left: 5px;'>";
	
	    $html .= "<tr id='electro_hemo_1' class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='electro_hemo_label_1' type='text' style='font-weight: bold; padding-right: 5px; margin-right: 30px;'> </span></label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab2' style='padding-top: 5px;'> <input id='electro_hemo_valeur_1' type='number' step='any'> % </label></td>";
	    $html .= "  <td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    
        $html .= "<tr class='ligneAnanlyse' id='electro_hemo_mp' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'> <div style='float: left; width: 25%; text-align: center; font-weight: bold; font-size: 25px;'> <div style='float: left; width: 50%; cursor: pointer; ' id='electro_hemo_moins'> - </div> <div style=' float: left; width: 45%; cursor: pointer;'  id='electro_hemo_plus'> + </div> </div> </label></td>";
	    $html .= "  <td style='width: 35%;'></td>";
	    $html .= "  <td style='width: 20%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	    
		return $html;
	}
	
	/**
	 * analyse 45
	 */
	public function electrophorese_preteines_45(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label><span style='font-weight: bold; '> Albumine <input id='albumine' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='albumine_conc' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 40,2 - 47,6 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label><span style='font-weight: bold; '> Alpha 1 <input id='alpha_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='alpha_1_conc' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 2,1 - 3,5 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label><span style='font-weight: bold; '> Alpha 2 <input id='alpha_2' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='alpha_2_conc' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 5,1 - 8,5 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label><span style='font-weight: bold; '> Beta 1 <input id='beta_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='beta_1_conc' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 3,4 - 5,2 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label><span style='font-weight: bold; '> Beta 2 <input id='beta_2' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='beta_2_conc' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 2,3 - 4,7 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label><span style='font-weight: bold; '> Gamma <input id='gamma' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='gamma_conc' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 8,0 - 13,5 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='4' style='height: 3px;'></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label><span style='font-size: 16px;'> Proteine totale:  </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='proteine_totale' type='number' step='any'  > </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 80%; font-size: 14px;'> g/dL </label></td>";
	    $html .= "</tr>";

	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='3' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_electrophorese_proteines' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' > </textarea> </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 80%; height: 80px; font-size: 14px;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
	    
	    
	    return $html;
	}
	
	/**
	 * analyse 46
	 */
	public function albuminemie_46(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Albumin&eacute;mie <input id='albuminemie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 35 - 53 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 47
	 */
	public function albumine_urinaire_47(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Albumine sucre (urines) <input id='albumine_urine' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 48
	 */
	public function protidemie_48(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '> Protid&eacute;mie <input id='protidemie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 45%;'><label class='lab3' style='padding-top: 5px; width: 89%; font-size: 13px;'> N: Adultes: 66 &agrave; 83 ; Nouveaux n&eacute;s: 52 &agrave; 91 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 49
	 */
	public function proteinurie_49(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Prot&eacute;inurie <input id='proteinurie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/24H </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 0,2 - 0,15 g/24H </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 50
	 */
	public function hlm_compte_daddis_50(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> H&eacute;maties <input id='hematies_hlm' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> /min </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N < 2000 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Leucocytes <input id='leucocytes_hlm' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> /min </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N < 2000 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 51
	 */
	public function beta_hcg_plasmatique_51(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> B&eacute;ta HCG <input id='beta_hcg' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 52
	 */
	public function psa_52(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='psa' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> ng/ml </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 0,1 &agrave;  2,6 </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 53
	 */
	public function crp_53(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> CRP <input id='crp' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> < 6 mg/l </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 54
	 */
	public function facteurs_rhumatoides_54(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> RF <input id='facteurs_rhumatoides' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> ul/ml </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 0 - 30 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 55
	 */
	public function rf_waaler_rose_55(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> rf waaler rose <input id='rf_waaler_rose' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> ng/ml </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> < 8UI/mL </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 56
	 */
	public function toxoplasmose_56(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='toxoplasmose_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='toxoplasmose_2' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 57
	 */
	public function rubeole_57(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='rubeole_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='rubeole_2' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 58
	 */
	public function culot_urinaire_58(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='culot_urinaire_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='culot_urinaire_2' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 59
	 */
	public function serologie_chlamydiae_59(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='serologie_chlamydiae' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 60
	 */
	public function serologie_syphilitique_60(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='serologie_syphilitique' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 61
	 */
	public function aslo_61(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> aslo <input id='aslo' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> < 200 UI/mL </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 62
	 */
	public function widal_62(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Typhi TO <input id='widal_to' type='number' step='any'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_to' style='width: 60%;' id='widal' type='text'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Typhi TH <input id='widal_th' type='number' step='any'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_th' style='width: 60%;' id='widal' type='text'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi AO <input id='widal_ao' type='number' step='any'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_ao' style='width: 60%;' id='widal' type='text'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi AH <input id='widal_ah' type='number' step='any'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_ah' style='width: 60%;' id='widal' type='text'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi BO <input id='widal_bo' type='number' step='any'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_bo' style='width: 60%;' id='widal' type='text'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi BH <input id='widal_bh' type='number' step='any'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_bh' style='width: 60%;' id='widal' type='text'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi CO <input id='widal_co' type='number' step='any'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_co' style='width: 60%;' id='widal' type='text'> </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi CH <input id='widal_ch' type='number' step='any'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_ch' style='width: 60%;' id='widal' type='text'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}

	/**
	 * analyse 63
	 */
	public function ag_hbs_63(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Ag Hbs <input id='ag_hbs' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> Quantitatif </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 64
	 */
	public function hiv_64(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> HIV <select id='hiv' > <option >  </option> <option value='positif' >Positif</option> <option value='negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}

	/**
	 * analyse 65
	 */
	public function pv_65(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> pv (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 66
	 */
	public function ecbu_66(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> ecbu (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 67
	 */
	public function pus_67(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> pus (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 68
	 */
	public function typage_hemoglobine_68 (){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'><div class='noteTypeMateriel' style='float: left; height: 20px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_typage_hemoglobine'> </div></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> typage de l'h&eacute;moglobine <input id='typage_hemoglobine' type='text' style='padding-right: 10px;'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	
	public function impressionResultatsAnalysesDemandeesAction()
	{
	    $service = $this->layout()->user['NomService'];
	    $iddemande = $this->params()->fromPost( 'iddemande' );
	
	    
	    $idpatient = $this->getPatientTable()->getDemandeAnalysesAvecIddemande($iddemande)['idpatient'];
	    $personne  = $this->getPersonneTable()->getPersonne($idpatient);
	    $depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
	    
	    //Recuperation de la liste des analyses pour lesquelles les r�sultats sont d�j� renseign�s
	    $listeResultats = $this->getResultatDemandeAnalyseTable()->getListeResultatsAnalysesDemandees($iddemande);
	    
	    $analysesDemandees = array();
	    $resultatsAnalysesDemandees = array();
	    for($i = 0 ; $i < count($listeResultats) ; $i++ ){
	        $idanalyse = $listeResultats[$i]['idanalyse'];
	        $iddemande = $listeResultats[$i]['iddemande'];
	        
	        if($idanalyse == 1){
	            $analysesDemandees  [$i] = $listeResultats[$i];
	            $resultatsAnalysesDemandees[1] = $this->getResultatDemandeAnalyseTable()->getValeursNfs($iddemande);
	        }
	        
	        
	        elseif($idanalyse == 68){
	            $analysesDemandees  [$i] = $listeResultats[$i];
	            $resultatsAnalysesDemandees[68] = $this->getResultatDemandeAnalyseTable()->getValeursTypageHemoglobine($iddemande);
	        }
	        
	    }
	    
	    //******************************************************
	    //******************************************************
	    //************** Cr�ation de l'imprim� pdf *************
	    //******************************************************
	    //******************************************************
	    //Cr�er le document
	    $DocPdf = new DocumentPdf();
	    //Cr�er la page
	    $page = new ResultatsAnalysesDemandeesPdf();
	
	    //Envoyer les donn�es sur le patient
	    $page->setDonneesPatient($personne);
	    $page->setService($service);
	    $page->setAnalysesDemandees($analysesDemandees);
	    $page->setResultatsAnalysesDemandees($resultatsAnalysesDemandees);
	    $page->setDepistage($depistage);
	    
	
	    //Ajouter une note � la page
	    $page->addNote();
	    //Ajouter la page au document
	    $DocPdf->addPage($page->getPage());
	    //Afficher le document contenant la page
	    $DocPdf->getDocument();
	}
	
}
