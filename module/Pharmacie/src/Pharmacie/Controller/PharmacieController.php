<?php

namespace Pharmacie\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\DateTime;
use Pharmacie\Form\MedicamentForm;
use Zend\Json\Json;
use Zend\Form\View\Helper\FormRow;
use Pharmacie\Model\Consommable;
use Pharmacie\View\Helper\DateHelper;
use Pharmacie\Form\CommandeForm;
use Zend\Form\View\Helper\FormInput;
use Zend\Form\View\Helper\FormElement;
use Zend\Form\View\Helper\FormCollection;

class PharmacieController extends AbstractActionController {
	protected $consommableTable;
	public function getConsommableTable() {
		if (! $this->consommableTable) {
			$sm = $this->getServiceLocator ();
			$this->consommableTable = $sm->get ( 'Pharmacie\Model\ConsommableTable' );
		}
		return $this->consommableTable;
	}
	public function indexAction() {
		$this->layout ()->setTemplate ( 'layout/pharmacie' );
		$view = new ViewModel ();
		return $view;
	}
	public function ajouterAction() {
		$this->layout ()->setTemplate ( 'layout/pharmacie' );
		if ($this->getRequest()->isPost ()) {
			$indication_therapeutique = $this->params ()->fromPost ( 'indication_therapeutique' );
			$mise_en_garde = $this->params ()->fromPost ( 'mise_en_garde' );
			$adresse_fabricant = $this->params ()->fromPost ( 'adresse_fabricant' );
			$composition = $this->params ()->fromPost ( 'composition' );
			$excipient_notoire = $this->params ()->fromPost ( 'excipient_notoire' );
			$voie_administration = $this->params ()->fromPost ( 'voie_administration' );
			$intitule = $this->params ()->fromPost ( 'intitule' );
			$description = $this->params ()->fromPost ( 'description' );

			/**
			 * *****************
			 */
			/* RECUPERER LA PHOTO */
			/**
			 * *****************
			 */
			$today = new \DateTime ( 'now' );
			$nomPhoto = $today->format ( 'dmy_His' ); // Zend_Date::now ()->toString ( 'ddMMyy_HHmmss' );
			//var_dump($nomPhoto);exit();
			$fileBase64 = $this->params ()->fromPost ( 'fichier_tmp' );

			try {
				$fileBase64 = substr ( $fileBase64, 23 );
				$img = imagecreatefromstring ( base64_decode ( $fileBase64 ) );
			} catch ( \Exception $ex ) {
				$img = false;
			}

			if ($img == true) {
				imagejpeg ( $img, 'C:\wamp\www\simens\public\img\medicaments_pharmacie\\' . $nomPhoto . '.jpg' );
			} else {
				$nomPhoto = null;
			}
			/**
			 * ***************************
			 */
			/* FIN RECUPERATION DE LA PHOTO */
			/**
			 * ***************************
			 */
			$Consommable = $this->getConsommableTable ();
			$nbLigne = $Consommable->compteNBLigne ();
			$medicament = array (
					'ID_MATERIEL' => $nbLigne + 1,
					'INDICATION_THERAPEUTIQUE' => $indication_therapeutique,
					'MISE_EN_GARDE' => $mise_en_garde,
					'ADRESSE_FABRIQUANT' => $adresse_fabricant,
					'COMPOSITION' => $composition,
					'EXCIPIENT_NOTOIRE' => $excipient_notoire,
					'VOIE_ADMINISTRATION' => $voie_administration,
					'INTITULE' => $intitule,
					'DESCRIPTION' => $description,
					'IMAGE' => $nomPhoto
			);
			//var_dump($medicament);exit();
			// Pour ins�rer un m�dicament, il faut au moins saisir le nom
			if ($intitule) {
				$Consommable->addConsommable ( $medicament );
			}

			$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
			//return  $this->getResponse ()->setContent(Json::encode());
		} else {
			$form = new MedicamentForm();
			//var_dump($form); exit();
			return new ViewModel(array('form' => $form));
		}
	}
	public function commandesAction() {
		$this->layout ()->setTemplate ( 'layout/pharmacie' );
		$consomTable = $this->getConsommableTable ();
		$nbCommande = $consomTable->compteNBCommandes ();
		$LalisteCommandes = $consomTable->fetchCommandes ();
		// var_dump($LalisteCommandes);exit();

		return array (
				'listeCommandes' => $LalisteCommandes,
				'nbCommandes' => $nbCommande
		);
	}
	public function listeMedicamentsAction() {
		$this->layout ()->setTemplate ( 'layout/pharmacie' );
		$consomTable = $this->getConsommableTable ();
		$LalisteConsommable = $consomTable->getAllConsommable ();
		$nbLigne = $consomTable->compteNBLigne ();
		return array (
				'listeMedicaments' => $LalisteConsommable,
				'nbMedicaments' => $nbLigne
		);
	}
	public function vueMedicamentAction() {

		$idMedicament = (int)$this->params()->fromPost('id');
		$medicament = $this->getConsommableTable();
		$leMedicament = $medicament->getConsommable($idMedicament);

		$image = $leMedicament->image;
		if(!$image){$image = 'efferalgan'; }
		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
		$html = "<div style='width:200px;float:left; margin-top: 5px;'>
		<div id='imagevue'> <img id='MonImage' src='".$chemin."/img/medicaments_pharmacie/".$image.".jpg'> </div>
		</div>
		<div style='float:right;'>
		<table>";

		$html .="<tr>";
		$html .="<td style='display: inline-block; vertical-align: top;'><div style='font-size:20px;'>Intilut&eacute;:</div><p>".$leMedicament->intitule."</p></td>";
		$html .="<td style='display: inline-block; vertical-align: top;'><div style='font-size:20px;'>Indication th&eacute;rapeutique:</div><p>".$leMedicament->indication_therapeutique."</p></td>";
		$html .="<td style='display: inline-block; vertical-align: top;'><div style='font-size:20px;'>Mise en garde:</div><p>".$leMedicament->mise_en_garde."</p></td>";
		$html .="</tr>";

		$html .="<tr>";
		$html .="<td style='display: inline-block; vertical-align: top;'><div style='font-size:20px;'>Composition:</div><p>".$leMedicament->composition."</p></td>";
		$html .="<td style='display: inline-block; vertical-align: top;'><div style='font-size:20px;'>Excipient notoire:</div><p>".$leMedicament->excipient_notoire."</p></td>";
		$html .="<td style='display: inline-block; vertical-align: top;'><div style='font-size:20px;'>Voie administration:</div><p>".$leMedicament->voie_administration."</p></td>";
		$html .="</tr>";

		$html .="<tr>";
		$html .="<td style='display: inline-block; vertical-align: top;'><div style='font-size:20px;'>Adresse fabricant:</div><p style='height:80px; width:240px;'>".$leMedicament->adresse_fabriquant."</p></td>";
		$html .="<td style='display: inline-block; vertical-align: top;'><div style='font-size:20px;'>Description:</div><p style='height:80px; width:240px;'>".$leMedicament->description."</p></td>";
		$html .="<th rowspan='2'> <div id='pharmacie_logo_2'> </div></th>";
		$html .="</tr>";

		$html .="<tr>";
		$html .="<td></td>";
		$html .="<th><div class='block2' id='thoughtbot' style='float:right; display: inline-block; vertical-align: bottom; padding-left:0px; padding-top: 25px;'><button type='submit' id='terminervue'>Terminer</button></div></th>";
		$html .="</tr>";

		$html .="</table>
		</div>";

		$html .="<script> utiliserdansvuemedicament(); </script>";

		$this->getResponse()->setMetadata('Content-Type','application/html');
		return $this->getResponse()->setContent(Json::encode($html));
	}
	public function modificationAction() {

		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();

		if($this->getRequest()->isGet()){
		$idMedicament = (int)$this->params()->fromQuery('id');
		//var_dump($idMedicament);exit();

		$medicament = $this->getConsommableTable();
		$leMedicament = $medicament->getConsommable($idMedicament);

		$form = new MedicamentForm();
		//$consommModel = new Consommable();
		$donnees = array('intitule'=>$leMedicament->intitule,
		'indication_therapeutique'=>$leMedicament->indication_therapeutique,
		'mise_en_garde'=>$leMedicament->mise_en_garde,
		'composition'=>$leMedicament->composition,
		'excipient_notoire'=>$leMedicament->excipient_notoire,
		'voie_administration'=>$leMedicament->voie_administration,
		'adresse_fabricant'=>$leMedicament->adresse_fabriquant,
		'description'=>$leMedicament->description,

		'id_medicament'=>$idMedicament,
		);
// 		$donnees = array('INTITULE'=>$leMedicament->intitule,
// 				'INDICATION_THERAPEUTIQUE'=>$leMedicament->indication_therapeutique,
// 				'MISE_EN_GARDE'=>$leMedicament->mise_en_garde,
// 				'COMPOSITION'=>$leMedicament->composition,
// 				'EXCIPIENT_NOTOIRE'=>$leMedicament->excipient_notoire,
// 				'VOIE_ADMINISTRATION'=>$leMedicament->voie_administration,
// 				'ADRESSE_FABRIQUANT'=>$leMedicament->adresse_fabriquant,
// 				'DESCRIPTION'=>$leMedicament->description,

// 				'ID_MEDICAMENT'=>$idMedicament,
// 		);
// 		$consommModel->exchangeArray($donnees);
		//var_dump($donnees);exit();
		$form->populateValues($donnees);
		$formRow = new FormInput();
		$image = $leMedicament->image;

		$html ="<div style='width:200px;float:left; margin-top: 5px;'>
		<div id='image'> <img id='MonImage' src='".$chemin."/img/medicaments_pharmacie/".$image.".jpg'>
		<input type='file' name='fichier' />
		<input type='hidden' id='fichier_tmp' name='fichier_tmp' />
		</div>

		<div class='supprimer_photo' id='div_supprimer_photo'>
		</div>
		</div>";

		$html .="<div style='float:right; padding-left:10px; '>
		<table>";

		$html .= $formRow->render($form->get('id_medicament')) ;

		$html .="<tr id='form_medicament'>";
		$html .="<td class='comment-form-medicament'>".$formRow->render($form->get('intitule'))."</td>";
		$html .="<td class='comment-form-medicament'>".$formRow->render($form->get('indication_therapeutique'))."</td>";
		$html .="<td class='comment-form-medicament'>".$formRow->render($form->get('mise_en_garde'))."</td>";
		$html .="</tr>";

		$html .="<tr id='form_medicament'>";
		$html .="<td class='comment-form-medicament'>".$formRow->render($form->get('composition'))."</td>";
		$html .="<td class='comment-form-medicament'>".$formRow->render($form->get('excipient_notoire'))."</td>";
		$html .="<td class='comment-form-medicament'>".$formRow->render($form->get('voie_administration'))."</td>";
		$html .="</tr>";

		$html .="<tr id='form_medicament'>";
		$html .="<td class='comment-form-medicament' style='display: inline-block; vertical-align: top;'>".$formRow->render($form->get('adresse_fabricant'))."</td>";
		$html .="<td class='comment-form-medicament' style='display: inline-block; vertical-align: top;'>".$formRow->render($form->get('description'))."</td>";
		$html .="<td rowspan='2'> <div id='pharmacie_logo_2'> </div></td>";
		$html .="</tr>";

		$html .="<tr>";
		$html .="<td></td>";
		$html .="<th>
		<div class='block' id='thoughtbot' style='display: inline-block; vertical-align: bottom; padding-left:0px; padding-top: 25px;'><button type='submit' id='annulerModification'>Annuler</button></div>
		<div class='block2' id='thoughtbot' style='display: inline-block; vertical-align: bottom; padding-left:0px; padding-top: 25px;'><button type='submit' id='terminerModification'>Terminer</button></div>
		</th>";
		$html .="</tr>";

		$html .="</table>
		</div>";

		/***********************************************************************************************

		==========================POP UP pour Confirmation Suppression=================================

		***********************************************************************************************/
		$html .="<div id='confirmation' title='Confirmation de la suppression' style='display:none;'>
		<p style='font-size: 16px;'>
		<span style='float:left; margin:0 0px 20px 0; font-size:17px; '>
		<img src='".$chemin."/images_icons/warning_16.png' />
		Etes-vous s&ucirc;r de vouloir supprimer l'image ?
		</span>
		</p>
		</div>";

		$html .="<script> utiliserdansmodification(); </script>";

		}else
		if($this->getRequest()->isPost()){
		$Consommable = $this->getConsommableTable();

		$idMedicament = (int)$this->params()->fromPost('id');

		$indication_therapeutique = $this->params()->fromPost('indication_therapeutique');
		$mise_en_garde = $this->params()->fromPost('mise_en_garde');
		$adresse_fabricant = $this->params()->fromPost('adresse_fabricant');
		$composition = $this->params()->fromPost('composition');
		$excipient_notoire = $this->params()->fromPost('excipient_notoire');
		$voie_administration = $this->params()->fromPost('voie_administration');
		$intitule = $this->params()->fromPost('intitule');
		$description = $this->params()->fromPost('description');

		$fichier_tmp = $this->params()->fromPost('fichier_tmp');

		/********************/
		/*RECUPERER LA PHOTO*/
		/********************/
		$date = new DateTime();
		$nomImage = $date->format('dmy_His');//Zend_Date::now ()->toString ( 'ddMMyy_HHmmss' );
		$fileBase64 = $this->params()->fromPost('fichier_tmp');
		$fileBase64 = substr($fileBase64, 23);

		$img = imagecreatefromstring(base64_decode($fileBase64));

		if($img == true)
		{
		$LaLigneMedicament = $Consommable->getConsommable($idMedicament);
		$image = $LaLigneMedicament->image;
		if($image){ //SI L'IMAGE EXISTE, ELLE EST SUPPRIMER DU DOSSIER POUR ETRE REMPLACER PAR LA NOUVELLE IMAGE
		unlink('C:\wamp\www\simens\public\img\medicaments_pharmacie\\'.$image.'.jpg');
		}
		imagejpeg($img, 'C:\wamp\www\simens_derniereversion\public\img\medicaments_pharmacie\\'.$nomImage.'.jpg');

		$imagerie = array("IMAGE"=> $nomImage, 'ID_MATERIEL'=>$idMedicament);
		$Consommable->updateConsommable($imagerie);
		}
		/******************************/
		/*FIN RECUPERATION DE LA PHOTO*/
		/******************************/

		$medicament = array('INDICATION_THERAPEUTIQUE'=>$indication_therapeutique, 'MISE_EN_GARDE'=>$mise_en_garde,
		'ADRESSE_FABRIQUANT'=>$adresse_fabricant,'COMPOSITION'=>$composition,
		'EXCIPIENT_NOTOIRE'=>$excipient_notoire, 'VOIE_ADMINISTRATION'=>$voie_administration,
		'INTITULE'=>$intitule, 'DESCRIPTION'=>$description,
		'ID_MATERIEL'=>$idMedicament);

		//MISE A JOUR DES DONNEES
		$Consommable->updateConsommable($medicament);

		$html = "";
		}

		$this->getResponse()->setMetadata('Content-Type','application/html');
		return $this->getResponse()->setContent(Json::encode($html));
	}
	public function supprimerAction() {
		$idMedicament = ( int ) $this->params ()->fromPost ( 'id' );
		$Consommable = $this->getConsommableTable ();
		$unConsommable = $Consommable->getConsommable ( $idMedicament );
		// var_dump($Consommable->compteNBLigne());exit();
		$image = $unConsommable->image;
		var_dump ( $image );
		if ($image) {
			unlink ( 'C:\wamp\www\simens\public\img\medicaments_pharmacie\\' . $image . '.jpg' );
		}
		$Consommable->deleteConsommable ( $idMedicament );
		$nbLigne = $Consommable->compteNBLigne () . " m&eacute;dicament(s)";

		// $this->getResponse()->setMetadata('Content-Type','application/html');
		return $this->getResponse ()->setContent ( Json::encode ( $nbLigne ) );
	}
	public function vueCommandeAction() {

		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
		$idCommande = (int)$this->params()->fromPost('id');
		$medicament = $this->getConsommableTable();

		$lacommande = $medicament->getCommande($idCommande);

		$listeMedicamentCommandes = $medicament->fetchMedicamentsCommande($idCommande);

		$html = "<table style='width:100%; margin-top: 20px; background: #eeeeee;'>
		<thead>
		<tr style='height:50px; margin-top: 20px; width:100%; color: green;'>
		<td style='width:33%; font-family:Times Roman; padding-left: 10px; font-size: 20px; box-shadow: 0pt 3pt 10px rgba(0, 0, 0, 0.2);'>
		N&ordm; Commande : <b><i>".$lacommande['ID_COMMANDE']."</i></b><br>
		<!-- Montant : <b> <i>francs</i></b><br> -->
		Date & Heure : <b><i>".$lacommande['DATE']." ".$lacommande['HEURE']." </i></b><br>
		</td>
		<th style='width:67%; background: white;'></th>
		</tr>
		</thead>";
		$html .="</table>";

		$html .= "<table style='float:left; width:70%; margin-top: 15px;' class='table table-bordered tab_list_mini' id='listeMedicaments'>
		<thead>
		<tr style='height:30px; margin-top: 20px;'>
		<th style='width:5%'>N&ordm;</th>
		<th style='width:20%'>INTITULE</th>
		<th style='width:15%'>QUANTITE</th>
		<th style='width:25%'>PRIX UNITAIRE (FCFA)</th>
		<th style='width:25%'>PRIX TOTAL (FCFA)</th>
		<th style='width:10%'>ETAT</th>
		</tr>
		</thead>";

		$html .="<tbody>";
		$cmpt = 1;
		$totalSatisfait = 0;
		$totalNonSatisfait = 0;
		$TOTAL = 0;
		foreach ($listeMedicamentCommandes as $liste) :
		$totalp = $liste['QUANTITE']*$liste['PRIX'];
		$html .="<tr style='height:40px; margin-top: 20px;'>
		<td style='width:5%'>".$cmpt++."</td>
		<td style='width:20%'>".$liste['INTITULE']."</td>
		<td style='width:20%'>".$liste['QUANTITE']."</td>
		<td style='width:20%'>".$liste['PRIX']."</td>
		<td style='width:20%; font-size: 19px;'>".$totalp."</td>
		<td style='width:20%'>";
		if ($liste['ETAT'] == 1) {
		$html.="<img src='".$chemin."/images_icons/tick_16.png' >";
		$totalSatisfait += $totalp;
		} else {
		$html.="<img src='".$chemin."/images_icons/stop_16.png' >";
		$totalNonSatisfait += $totalp;
		}

		$TOTAL += $totalp;
		$html .="</td>
		</tr>";
		endforeach;
		$html .="</tbody>";
		$html .="</table>";

		$html .="<table style='width: 25%; float:left; margin-top: 55px; margin-left: 25px;' class='table table-bordered tab_list_mini'>";
		$html .="<tr>";
		$html .="<td>Total <img src='".$chemin."/images_icons/tick_16.png' ></td>";
		$html .="<td style='font-size: 22px; '>".$totalSatisfait."</td>";
		$html .="</tr>";

		$html .="<tr>";
		$html .="<td>Total <img src='".$chemin."/images_icons/stop_16.png' ></td>";
		$html .="<td style='font-size: 22px; '>".$totalNonSatisfait."</td>";
		$html .="</tr>";

		$html .="<tr>";
		$html .="<td>Grand Total </td>";
		$html .="<td style='font-size: 22px; font-weight: bold; '>".$TOTAL."</td>";
		$html .="</tr>";

		$html .="</table>";

		$html .="<table style='width: 70%;'>";
		$html .="<tr>";
		$html .="<td></td>";
		$html .="<th><div class='block2' id='thoughtbot' style='float:right; display: inline-block; vertical-align: bottom; padding-left:0px; padding-top: 25px;'><button type='submit' id='terminervue'>Terminer</button></div></th>";
		$html .="</tr>";

		$html .="</table>";

		$html .="<script> utiliserdansvuemedicament(); </script>";

		$this->getResponse()->setMetadata('Content-Type','application/html');
		return $this->getResponse()->setContent(Json::encode($html));
	}
	public function nouvelleCommandeAction() {
		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
		if($this->getRequest()->isGet()){

		$liste = $this->getConsommableTable();
		$nbCommande = $liste->compteNBCommandes();

		$Control = new DateHelper();
		$today = new \DateTime('now');
		$date = $today->format('Y-m-d');//Zend_Date::now ()->toString ( 'yyyy-MM-dd' );
		$time = $today->format('H:i:s'); //Zend_Date::now ()->toString ( 'HH:mm:ss' );

		$html = "<table style='width:100%; margin-top: 20px; background: #eeeeee;'>
		<thead>
		<tr style='height:50px; margin-top: 20px; width:100%; color: green;'>
		<td style='width:33%; font-family:Times Roman; padding-left: 10px; font-size: 20px; box-shadow: 0pt 3pt 10px rgba(0, 0, 0, 0.2);'>
		N&ordm; Commande : <b><i> ".++$nbCommande." </i></b><br>
		<!-- Montant : <b> <i>francs</i></b><br> -->
		Date & Heure : <b><i>". $Control->convertDate($date)." ".$time."</i></b><br>
		</td>
		<th style='width:67%; background: white;'></th>
		</tr>
		</thead>";
		$html .="</table>";

		$html .= "<table style='float:left; width:70%; margin-top: 15px;' class='table table-bordered tab_list_mini' id='listeMedicaments'>
		<thead>
		<tr style='height:30px; margin-top: 20px;'>
		<th style='width:5%'>N&ordm;</th>
		<th style='width:20%'>INTITULE</th>
		<th style='width:20%'>QUANTITE</th>
		<th style='width:20%'>PRIX UNITAIRE</th>
		<th style='width:25%'>PRIX TOTAL</th>
		</tr>
		</thead>";

		$html .="<tbody>";
		for($i=1; $i<4; $i++){
		$form = new CommandeForm();
		$formRow = new FormInput();
		$form->setName('form'.$i);

		$html .="<tr id='idform' style='height:40px; margin-top: 20px;'>
		<td style='width:5%'><i>".$i."</i></td>
		<td style='width:20%'>".$formRow->render($form->get('intitule'))."</td>
		<td style='width:20%' id='champ".$i."'>".$formRow->render($form->get('quantite'))."</td>
		<td style='width:20% font-size: 19px;'><hass id='prix_unitaire".$i."' style='padding-top: 10px;'></hass></td>
		<td style='width:20%; font-size: 21px;'> <hass id='prix_total".$i."' style='padding-top: 10px;'></hass></td>";
		$html .="</tr>";
		}

		$html .="<tr id='idform' style='height:40px; margin-top: 20px;'>
		<td colspan='4' style='width:65%'><i></i></td>
		<td style='width:20%; font-size: 21px;'> <hass id='prix_total".$i."' style=' padding-top: 10px;'>157430</hass></td>";
		$html .="</tr>";

		$html .="</tbody>";
		$html .="</table>";

		$html .="<table style='width: 30%; float:left;'>";
		$html .="<tr>";
		$html .="<th><img style='margin-left:30%; margin-top: 10px;' src='".$chemin."/images_icons/127.png'></br><i style='margin-left: 30%; font-size: 13px;'>Ajouter</i></th>";
		$html .="<th><img style='margin-left:; margin-top: 10px;' src='".$chemin."/images_icons/128.png' ></br><i style='margin-left: %; font-size: 13px;'>Enlever</i></th>";
		$html .="</tr>";
		$html .="</table>";

		$html .="<table style='width: 70%;'>";
		$html .="<tr>";
		$html .="<td></td>";
		$html .="<th><div class='block2' id='thoughtbot' style='float:right; display: inline-block; vertical-align: bottom; padding-left:0px; padding-top: 10px;'><button type='submit' id='annulerNouvelleCommande'>Annuler</button></div></th>";
		$html .="<th><div class='block2' id='thoughtbot' style='float:left; display: inline-block; vertical-align: bottom; padding-left:0px; padding-top: 10px;'><button type='submit' id='terminerNouvelleCommande'>Terminer</button></div></th>";
		$html .="</tr>";

		$html .="</table>";

		$html .="<script> calcule (); </script>";

		}

		$this->getResponse()->setMetadata('Content-Type','application/html');
		return $this->getResponse()->setContent(Json::encode($html));
	}
	public function venteAction() {

		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
		if($this->getRequest()->isGet()){

		$liste = $this->getConsommableTable();
		$nbCommande = $liste->compteNBCommandes();

		$Control = new DateHelper();
		$today = new \DateTime('now');
		$date = $today->format('Y-m-d');//Zend_Date::now ()->toString ( 'yyyy-MM-dd' );
		$time = $today->format('H:i:s');//Zend_Date::now ()->toString ( 'HH:mm:ss' );

		$html = "<table style='float:left; width:80%; margin-top: 20px; background: #eeeeee;'>
		<thead>
		<tr style='height:50px; margin-top: 20px; width:100%; color: green;'>
		<td style='width:40%; font-family:Times Roman; padding-left: 10px; font-size: 20px; box-shadow: 0pt 3pt 10px rgba(0, 0, 0, 0.2);'>
		Vente N&ordm; : <b><i> ".++$nbCommande." </i></b><br>
		Date & Heure : <b><i>". $Control->convertDate($date)." ".$time."</i></b><br>
		</td>
		<th style='width:60%; background: white;'>
		<img style='float:left; margin-left:2%; margin-top: 10px;' src='".$chemin."/images_icons/1.png'></br><i style='float:left; margin-left: 0%; font-size: 13px;'>Re&ccedil;u</i>
		</th>
		</tr>
		</thead>";
		$html .="</table>";

		$html .= "<table style='float:left; width:70%; margin-top: 15px;' class='table table-bordered tab_list_mini' id='listeMedicaments'>
		<thead>
		<tr style='height:30px; margin-top: 20px;'>
		<th style='width:5%'>N&ordm;</th>
		<th style='width:20%'>INTITULE</th>
		<th style='width:20%'>QUANTITE</th>
		<th style='width:20%'>PRIX UNITAIRE</th>
		<th style='width:25%'>PRIX TOTAL</th>
		</tr>
		</thead>";

		$html .="<tbody>";
		for($i=1; $i<4; $i++){
		$form = new CommandeForm();
		$form->setName('form'.$i);
		$formRow = new FormRow();
		$html .="<tr id='idform' style='height:40px; margin-top: 20px;'>
		<td style='width:5%'><i>".$i."</i></td>
		<td style='width:20%'>".$formRow->render($form->get('intitule'))."</td>
		<td style='width:20%' id='champ".$i."'>".$formRow->render($form->get('quantite'))."</td>
		<td style='width:20% font-size: 19px;'><hass id='prix_unitaire".$i."' style='padding-top: 10px;'></hass></td>
		<td style='width:20%; font-size: 21px;'> <hass id='prix_total".$i."' style='padding-top: 10px;'></hass></td>";
		$html .="</tr>";
		}

		$html .="<tr id='idform' style='height:40px; margin-top: 20px;'>
		<td colspan='4' style='width:65%'><i></i></td>
		<td style='width:20%; font-size: 21px;'> <hass id='prix_total".$i."' style=' padding-top: 10px;'>23366</hass></td>";
		$html .="</tr>";

		$html .="</tbody>";
		$html .="</table>";

		$html .="<table style='width: 30%; float:left;'>";
		$html .="<tr>";
		$html .="<th><img style='margin-left:30%; margin-top: 10px;' src='".$chemin."/images_icons/127.png'></br><i style='margin-left: 30%; font-size: 13px;'>Ajouter</i></th>";
		$html .="<th><img style='margin-left:; margin-top: 10px;' src='".$chemin."/images_icons/128.png' ></br><i style='margin-left: %; font-size: 13px;'>Enlever</i></th>";
		$html .="</tr>";
		$html .="</table>";

		$html .="<table style='width: 70%;'>";
		$html .="<tr>";
		$html .="<td></td>";
		$html .="<th><div class='block2' id='thoughtbot' style='float:right; display: inline-block; vertical-align: bottom; padding-left:0px; padding-top: 10px;'><button type='submit' id='terminervue'>Annuler</button></div></th>";
		$html .="<th><div class='block2' id='thoughtbot' style='float:left; display: inline-block; vertical-align: bottom; padding-left:0px; padding-top: 10px;'><button type='submit' id='terminervue'>Terminer</button></div></th>";
		$html .="</tr>";

		$html .="</table>";

		$html .="<script> calcule (); </script>";

		}

		$this->getResponse()->setMetadata('Content-Type','application/html');
		return $this->getResponse()->setContent(Json::encode($html));
	}
	public function tarifAction() {

		// $idCommande = (int)$this->getRequest()->getParam('id');
		// $liste = new Pharmacie_Model_Managers_Consommable();
		// $leMedoc = $liste->getConsommable($idCommande);

		// $this->getResponse()->setHeader('Content-Type','application/html');
		// $this->_helper->json->sendJson($leMedoc['PRIX']);
	}
}
