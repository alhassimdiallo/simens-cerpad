<?php

namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\NotIn;

class DemandehospitalisationTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getDemandehospitalisation($data)
	{
		$rowset = $this->tableGateway->select(array(
				'id_cons' => $data['id_cons'],
		));
		$row = $rowset->current();
		if (!$row) {
			return null;
		}
		return $row;
	}
	
	public function saveDemandehospitalisation($infoDemandeHospitalisation)
	{
		if(!$this->getDemandehospitalisation($infoDemandeHospitalisation)){
			if($infoDemandeHospitalisation['motif_demande_hospi'] && $infoDemandeHospitalisation['date_fin_prevue_hospi']){
				$this->tableGateway->insert($infoDemandeHospitalisation);				
			}
		}
	}
	
	public function getConversionDate(){
		$this->conversionDate = new DateHelper();
		
		return $this->conversionDate;
	}
	
	public function deleteDemandehospitalisation($id_demande_hospi){
		$id_demande_hospi = (int) $id_demande_hospi;
		$this->tableGateway->delete( array('id_demande_hospi' => $id_demande_hospi));
	}
	
	public function getDemandeHospitalisationWithIdcons($id_cons){
		
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array())
		->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = pat.ID_PERSONNE', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','id'=>'id_personne'))
		->join(array('cons' => 'consultation'), 'cons.ID_PATIENT= pat.ID_PERSONNE', array('Datedemandehospi'=>'date', 'Idcons'=>'id_cons'))
		->join(array('dh' => 'demande_hospitalisation'), 'dh.id_cons = cons.id_cons' , array('*'))
		->join(array('med' => 'personne') , 'med.ID_PERSONNE = cons.id_medecin' , array('NomMedecin' =>'nom', 'PrenomMedecin' => 'prenom'))
		->where(array('cons.id_cons' => $id_cons));
		
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute()->current();
		
		return $Result;
	}
	
	//Réduire la chaine addresse
	function adresseText($Text){
		$chaine = $Text;
		if(strlen($Text)>36){
			$chaine = substr($Text, 0, 36);
			$nb = strrpos($chaine, ' ');
			$chaine = substr($chaine, 0, $nb);
			$chaine .=' ...';
		}
		return $chaine;
	}
	
	/**
	 * Recuperation de la liste des demandes d'hospitalisation
	 */
	public function getListeDemandeHospitalisation()
	{



		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('Nom','Prenom','Datenaissance','Sexe', 'Datedemandehospi', 'Prenom&NomMedecin' , 'id');
		
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "id";
		
		/*
		 * Paging
		*/
		$sLimit = array();
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit[0] = $_GET['iDisplayLength'];
			$sLimit[1] = $_GET['iDisplayStart'];
		}
		
		/*
		 * Ordering
		*/
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = array();
			$j = 0;
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder[$j++] = $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
								 	".$_GET['sSortDir_'.$i];
				}
			}
		}
		
		/*
		 * SQL queries
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','id'=>'id_personne'))
		->join(array('cons' => 'consultation'), 'cons.pat_id_personne = pat.id_personne', array('Datedemandehospi'=>'date', 'Idcons'=>'id_cons'))
		->join(array('dh' => 'demande_hospitalisation2'), 'dh.id_cons = cons.id_cons' , array('*'))
		->join(array('med' => 'medecin') , 'med.id_personne = cons.id_personne' , array('NomMedecin' =>'nom', 'PrenomMedecin' => 'prenom'))
		->where(array('dh.valider_demande_hospi'=>0));
		
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
		
		$rResult = $rResultFt;
		
		$output = array(
				//"sEcho" => intval($_GET['sEcho']),
				//"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
		);
		
		/*
		 * $Control pour convertir la date en fran�ais
		*/
		$Control = new DateHelper();
		
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
		
		/*
		 * Pr�parer la liste
		*/
		foreach ( $rResult as $aRow )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					if ($aColumns[$i] == 'Nom'){
						$row[] = "<khass id='nomMaj'>".$aRow[ $aColumns[$i]]."</khass>";
					}
		
					else if ($aColumns[$i] == 'Datenaissance') {
						$row[] = $Control->convertDate($aRow[ $aColumns[$i] ]);
					}
		
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = $this->adresseText($aRow[ $aColumns[$i] ]);
					}
		
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue><a href='javascript:affichervue(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir.png' title='détails'></a></infoBulleVue>";
		
						$html  .="<infoBulleVue><a href='javascript:hospitaliser(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/details.png' title='Hospitaliser'></a></infoBulleVue>";
		
						$html .="<input id='".$aRow[ $aColumns[$i] ]."'   type='hidden' value='".$aRow[ 'Idcons' ]."'>";
						$html .="<input id='".$aRow[ $aColumns[$i] ]."dh' type='hidden' value='".$aRow[ 'id_demande_hospi' ]."'>";
						
						$row[] = $html;
					}
						
					else if ($aColumns[$i] == 'Prenom&NomMedecin') {
						$row[] = $aRow[ 'PrenomMedecin' ]." ".$aRow[ 'NomMedecin' ];
					}
					
					else if ($aColumns[$i] == 'Datedemandehospi') {
						$row[] = $Control->convertDateTime($aRow[ 'Datedemandehospi' ]);
					}
					
					else {
						$row[] = $aRow[ $aColumns[$i] ];
					}
		
				}
			}
				
			$output['aaData'][] = $row;
		}
		
		return $output;
		
	}
	
	/**
	 * Recuperation de la liste des patients en cours d'hospitalisation et deja hospitaliser
	 */
	public function getListePatientEncoursHospitalisation($id_service)
	{
	
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('Nom','Prenom','Datenaissance','Sexe', 'Datedebut', 'date_fin_prevue_hospi' , 'id');
	
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "id";
	
		/*
		 * Paging
		*/
		$sLimit = array();
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit[0] = $_GET['iDisplayLength'];
			$sLimit[1] = $_GET['iDisplayStart'];
		}
	
		/*
		 * Ordering
		*/
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = array();
			$j = 0;
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder[$j++] = $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
								 	".$_GET['sSortDir_'.$i];
				}
			}
		}
		
		/*
		 * SQL queries 1
		*/
		$sql1 = new Sql($db);
		$sQuery1 = $sql1->select()
		->from(array('pat' => 'patient'))->columns(array('id'=>'ID_PERSONNE'))
		->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = pat.ID_PERSONNE', array())
		->join(array('cons' => 'consultation'), 'cons.ID_PATIENT = pat.ID_PERSONNE', array())
		->join(array('cons_eff' => 'consultation_effective'), 'cons_eff.ID_CONS = cons.ID_CONS' , array())
		->join(array('dh' => 'demande_hospitalisation'), 'dh.id_cons = cons.ID_CONS' , array())
		->join(array('s' => 'service'), 's.ID_SERVICE = cons.ID_SERVICE', array())
		->join(array('h' => 'hospitalisation'), 'h.code_demande_hospitalisation = dh.id_demande_hospi', array() )
		->where(array('dh.valider_demande_hospi'=>1 , 's.ID_SERVICE'=> $id_service, 'cons.ARCHIVAGE'=>1, 'h.terminer'=>0))
		->order('h.terminer ASC');
		/*
		 * SQL queries 2
		 */
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array())
		->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = pat.ID_PERSONNE', array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','id'=>'ID_PERSONNE'))
		->join(array('cons' => 'consultation'), 'cons.ID_PATIENT = pat.ID_PERSONNE', array('Datedemandehospi'=>'DATE', 'Idcons'=>'ID_CONS', 'Archivage' => 'ARCHIVAGE'))
		->join(array('cons_eff' => 'consultation_effective'), 'cons_eff.ID_CONS = cons.ID_CONS' , array('*'))
		->join(array('dh' => 'demande_hospitalisation'), 'dh.id_cons = cons.ID_CONS' , array('*'))
		->join(array('s' => 'service'), 's.ID_SERVICE = cons.ID_SERVICE', array())
		->join(array('h' => 'hospitalisation'), 'h.code_demande_hospitalisation = dh.id_demande_hospi' , array('Datedebut'=>'date_debut', 'Idhosp'=>'id_hosp', 'Terminer'=>'terminer'))
		->where(array('dh.valider_demande_hospi'=>1 , 's.ID_SERVICE'=> $id_service, new NotIn ( 'pat.ID_PERSONNE', $sQuery1 )))
		->order('h.terminer ASC');
	
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		//var_dump($rResultFt); exit();
		
		$iFilteredTotal = count($rResultFt);
	
		$rResult = $rResultFt;
	
		$output = array(
				//"sEcho" => intval($_GET['sEcho']),
				//"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
		);
	
		/*
		 * $Control pour convertir la date en fran�ais
		*/
		$Control = new DateHelper();
	
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
	
		/*
		 * Pr�parer la liste
		*/
		foreach ( $rResult as $aRow )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					if ($aColumns[$i] == 'Nom'){
						$row[] = "<khass id='nomMaj'>".$aRow[ $aColumns[$i]]."</khass>";
					}
	
					else if ($aColumns[$i] == 'Datenaissance') {
						$row[] = $Control->convertDate($aRow[ $aColumns[$i] ]);
					}
	
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = $this->adresseText($aRow[ $aColumns[$i] ]);
					}
	
					else if ($aColumns[$i] == 'id') {
						
						if($aRow[ 'Terminer' ] == 0) {
							$html  ="<infoBulleVue><a href='javascript:affichervue(".$aRow[ 'id_demande_hospi' ].")'>";
							$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir.png' title='détails'></a></infoBulleVue>";
							
							$html  .="<infoBulleVue><a href='javascript:administrerSoin(".$aRow[ 'id_demande_hospi' ].")'>";
							$html .="<img style='display: inline; margin-right: 14%;' src='".$tabURI[0]."public/images_icons/details.png' title='Administrer'></a></infoBulleVue>";
	
							$html  .="<infoBulleVue><a id='lebererPatientHospi".$aRow[ 'id_demande_hospi' ]."'  href='javascript:liberer(".$aRow[ 'id_demande_hospi' ].")'>";
							$html .="<img style='display: inline;' src='".$tabURI[0]."public/images_icons/edit_item_btn.png' title='Libérer'></a></infoBulleVue>";
						
						}else {
							$html  ="<infoBulleVue><a href='javascript:affichervuedetailhospi(".$aRow[ 'id_demande_hospi' ].")'>";
							$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir.png' title='détails'></a></infoBulleVue>";
								
							$html  .="<infoBulleVue><a>";
							$html .="<img style='color: white; opacity: 0.15; margin-right: 14%;' src='".$tabURI[0]."public/images_icons/details.png' ></a></infoBulleVue>";
							
							$html  .="<infoBulleVue><a>";
							$html .="<img style='color: white; opacity: 0.15;' src='".$tabURI[0]."public/images_icons/edit_item_btn.png' ></a></infoBulleVue>";
							
						}
						
						$html .="<input id='".$aRow[ 'id_demande_hospi' ]."'   type='hidden' value='".$aRow[ 'Idcons' ]."'>";
						$html .="<input id='".$aRow[ 'id_demande_hospi' ]."hp' type='hidden' value='".$aRow[ 'Idhosp' ]."'>";
						$html .="<input id='".$aRow[ 'id_demande_hospi' ]."idPers' type='hidden' value='".$aRow[ $aColumns[$i] ]."'>";
						
						$row[] = $html;
					}
	
					else if ($aColumns[$i] == 'date_fin_prevue_hospi') {
						$row[] = $Control->convertDate($aRow[ 'date_fin_prevue_hospi' ]);
					}
						
					else if ($aColumns[$i] == 'Datedebut') {
						$row[] = $Control->convertDateTime($aRow[ 'Datedebut' ]);
					}
						
					else {
						$row[] = $aRow[ $aColumns[$i] ];
					}
	
				}
			}
	
			$output['aaData'][] = $row;
		}
	
		return $output;
	
	}
	
	public function validerDemandeHospitalisation($code_demande){
		$this->tableGateway->update(array('valider_demande_hospi'=>1), array('id_demande_hospi'=>$code_demande));
	}
	
	
	/**
	 * Recuperation de la liste des patients en cours d'hospitalisation pour le suivi des patients
	 */
	public function getListePatientSuiviHospitalisation()
	{
	
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('Nom','Prenom','Datenaissance','Sexe', 'Datedebut', 'date_fin_prevue_hospi' , 'id');
	
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "id";
	
		/*
		 * Paging
		*/
		$sLimit = array();
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit[0] = $_GET['iDisplayLength'];
			$sLimit[1] = $_GET['iDisplayStart'];
		}
	
		/*
		 * Ordering
		*/
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = array();
			$j = 0;
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder[$j++] = $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
								 	".$_GET['sSortDir_'.$i];
				}
			}
		}
	
		/*
		 * SQL queries
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','id'=>'id_personne'))
		->join(array('cons' => 'consultation'), 'cons.pat_id_personne = pat.id_personne', array('Datedemandehospi'=>'date', 'Idcons'=>'id_cons'))
		->join(array('dh' => 'demande_hospitalisation2'), 'dh.id_cons = cons.id_cons' , array('*'))
		->join(array('med' => 'medecin') , 'med.id_personne = cons.id_personne' , array('NomMedecin' =>'nom', 'PrenomMedecin' => 'prenom'))
		->join(array('h' => 'hospitalisation'), 'h.code_demande_hospitalisation = dh.id_demande_hospi' , array('Datedebut'=>'date_debut', 'Idhosp'=>'id_hosp', 'Terminer'=>'terminer'))
		->where(array('dh.valider_demande_hospi'=>1))
		->where(array('h.terminer' => 0));
	
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
	
		$rResult = $rResultFt;
	
		$output = array(
				//"sEcho" => intval($_GET['sEcho']),
				//"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
		);
	
		/*
		 * $Control pour convertir la date en fran�ais
		*/
		$Control = new DateHelper();
	
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
	
		/*
		 * Pr�parer la liste
		*/
		foreach ( $rResult as $aRow )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					if ($aColumns[$i] == 'Nom'){
						$row[] = "<khass id='nomMaj'>".$aRow[ $aColumns[$i]]."</khass>";
					}
	
					else if ($aColumns[$i] == 'Datenaissance') {
						$row[] = $Control->convertDate($aRow[ $aColumns[$i] ]);
					}
	
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = $this->adresseText($aRow[ $aColumns[$i] ]);
					}
	
					else if ($aColumns[$i] == 'id') {
	
						$html  ="<infoBulleVue><a href='javascript:affichervue(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir.png' title='détails'></a></infoBulleVue>";
								
						$html  .="<infoBulleVue><a href='javascript:administrerSoin(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 0%;' src='".$tabURI[0]."public/img/dark/blu-ray.png' title='Appliquer un soin'></a></infoBulleVue>";

						$html .="<input id='".$aRow[ $aColumns[$i] ]."'   type='hidden' value='".$aRow[ 'Idcons' ]."'>";
						$html .="<input id='".$aRow[ $aColumns[$i] ]."dh' type='hidden' value='".$aRow[ 'id_demande_hospi' ]."'>";
						$html .="<input id='".$aRow[ $aColumns[$i] ]."hp' type='hidden' value='".$aRow[ 'Idhosp' ]."'>";
	
						$row[] = $html;
					}
	
					else if ($aColumns[$i] == 'date_fin_prevue_hospi') {
						$row[] = $Control->convertDate($aRow[ 'date_fin_prevue_hospi' ]);
					}
	
					else if ($aColumns[$i] == 'Datedebut') {
						$row[] = $Control->convertDateTime($aRow[ 'Datedebut' ]);
					}
	
					else {
						$row[] = $aRow[ $aColumns[$i] ];
					}
	
				}
			}
	
			$output['aaData'][] = $row;
		}
	
		return $output;
	
	}
	
	
	public function getDemandehospitalisationParIdcons($idcons)
	{
		$rowset = $this->tableGateway->select(array(
				'id_cons' => $idcons,
		));
		$row = $rowset->current();
		if (!$row) {
			return null;
		}
		return $row;
	}
	
	public function getDemandeHospitalisationWithIdPatient($id_pat)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array())
		->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = pat.ID_PERSONNE', array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','id'=>'ID_PERSONNE'))
		->join(array('cons' => 'consultation'), 'cons.ID_PATIENT = pat.ID_PERSONNE', array('DateConsultation'=>'DATE', 'Idcons'=>'ID_CONS'))
		->join(array('dh' => 'demande_hospitalisation'), 'dh.id_cons = cons.ID_CONS' , array('*'))
		->join(array('med' => 'personne') , 'med.ID_PERSONNE = cons.ID_MEDECIN' , array('NomMedecin' =>'NOM', 'PrenomMedecin' => 'PRENOM'))
		->join(array('h' => 'hospitalisation'), 'h.code_demande_hospitalisation = dh.id_demande_hospi' , array('*'))
		->where(array('h.terminer' => 1, 'pat.ID_PERSONNE' => $id_pat));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute();
	
		return $Result;
	}
	
	public function getDemandehospitalisationParIdDemande($id_demande_hospi)
	{
		$rowset = $this->tableGateway->select(array(
				'id_demande_hospi' => $id_demande_hospi,
		));
		$row = $rowset->current();
		if (!$row) {
			return null;
		}
		return $row;
	}
}